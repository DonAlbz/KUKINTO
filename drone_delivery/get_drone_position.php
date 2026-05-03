<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: application/json');

if (!isset($_GET['order_id'])) {
    echo json_encode(['error' => 'order_id mancante']);
    exit;
}

$orderId = intval($_GET['order_id']);

function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $R = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    return $R * 2 * atan2(sqrt($a), sqrt(1-$a));
}

try {
    // 1) Recupero ordine
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['error' => 'Ordine non trovato']);
        exit;
    }

    if (empty($order['assigned_drone'])) {
        echo json_encode(['error' => 'Nessun drone assegnato']);
        exit;
    }

    $droneId = $order['assigned_drone'];

    // 2) Recupero drone
    $stmt = $pdo->prepare("SELECT * FROM drones WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $droneId]);
    $drone = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$drone) {
        echo json_encode(['error' => 'Drone non trovato']);
        exit;
    }

    // Se ordine già completato o annullato → restituisci posizione finale senza simulare
    if (in_array($order['status'], ['completato', 'annullato'])) {
        echo json_encode([
            'lat'                   => (float)$order['delivery_lat'],
            'lng'                   => (float)$order['delivery_lng'],
            'battery'               => (int)($drone['battery'] ?? 0),
            'status'                => $order['status'],
            'speed_kmh'             => 0,
            'distance_remaining_km' => 0,
            'eta_minutes'           => 0,
            'simulated'             => false,
            'progress'              => 100,
            'path'                  => [],
            'delivery'              => ['lat' => (float)$order['delivery_lat'], 'lng' => (float)$order['delivery_lng']]
        ]);
        exit;
    }

    $pickupLat   = (float)$order['pickup_lat'];
    $pickupLng   = (float)$order['pickup_lng'];
    $deliveryLat = (float)$order['delivery_lat'];
    $deliveryLng = (float)$order['delivery_lng'];

    // 3) Dati reali da hardware esterno (solo se freschi < 10 sec)
    $stmt = $pdo->prepare("
        SELECT latitude, longitude, recorded_at
        FROM drone_tracking
        WHERE drone_id = :drone_id
        ORDER BY recorded_at DESC
        LIMIT 2
    ");
    $stmt->execute([':drone_id' => $droneId]);
    $trackPoints = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $currentLat    = null;
    $currentLng    = null;
    $speedKmh      = null;
    $useSimulation = true;
    $progress      = 0;

    if (count($trackPoints) >= 2) {
        $lastTime = strtotime($trackPoints[0]['recorded_at']);
        $age = time() - $lastTime;
        if ($age < 10) {
            $useSimulation = false;
            $currentLat = (float)$trackPoints[0]['latitude'];
            $currentLng = (float)$trackPoints[0]['longitude'];
            $p1 = $trackPoints[1];
            $p2 = $trackPoints[0];
            $dt = strtotime($p2['recorded_at']) - strtotime($p1['recorded_at']);
            if ($dt > 0) {
                $distKm   = haversineDistance((float)$p1['latitude'], (float)$p1['longitude'], (float)$p2['latitude'], (float)$p2['longitude']);
                $speedKmh = round($distKm / ($dt / 3600), 1);
            }
        }
    }

    // 4) SIMULAZIONE
    if ($useSimulation) {
        $simSpeedKmh  = 60;
        $totalDistKm  = haversineDistance($pickupLat, $pickupLng, $deliveryLat, $deliveryLng);
        $totalSeconds = ($totalDistKm / $simSpeedKmh) * 3600;

        $startTime = strtotime(!empty($order['updated_at']) ? $order['updated_at'] : $order['created_at']);

        // Se updated_at è troppo vecchio (> 1 ora), resetta
        if ((time() - $startTime) > 3600) {
            $pdo->prepare("UPDATE orders SET updated_at = NOW() WHERE id = :id")
                ->execute([':id' => $orderId]);
            $startTime = time();
        }

        $elapsed  = time() - $startTime;
        $progress = min($elapsed / max($totalSeconds, 1), 1.0);

        $currentLat = $pickupLat + ($deliveryLat - $pickupLat) * $progress;
        $currentLng = $pickupLng + ($deliveryLng - $pickupLng) * $progress;
        $speedKmh   = $progress < 1 ? $simSpeedKmh : 0;

        // Consuma batteria: 1% ogni 0.5 km percorsi
        $distPercorsa = $totalDistKm * $progress;
        $batteryUsed  = (int)round($distPercorsa * 2);
        $newBattery   = max(0, 100 - $batteryUsed);

        // Salva punto simulato
        $pdo->prepare("
            INSERT INTO drone_tracking (drone_id, latitude, longitude)
            VALUES (:d, :lat, :lng)
        ")->execute([':d' => $droneId, ':lat' => $currentLat, ':lng' => $currentLng]);

        // Aggiorna drone con nuova posizione e batteria
        $pdo->prepare("
            UPDATE drones SET latitude = :lat, longitude = :lng, battery = :bat WHERE id = :id
        ")->execute([':lat' => $currentLat, ':lng' => $currentLng, ':bat' => $newBattery, ':id' => $droneId]);

        // Se arrivato a destinazione → completa ordine e libera drone
        if ($progress >= 1.0) {
            $pdo->prepare("
                UPDATE orders SET status = 'completato', updated_at = NOW() WHERE id = :id
            ")->execute([':id' => $orderId]);

            $pdo->prepare("
                UPDATE drones SET status = 'offline', current_order = NULL WHERE id = :id
            ")->execute([':id' => $droneId]);
        }
    }

    // 5) Path storico (ultimi 100 punti)
    $stmt = $pdo->prepare("
        SELECT latitude AS lat, longitude AS lng
        FROM drone_tracking
        WHERE drone_id = :drone_id
        ORDER BY recorded_at ASC
        LIMIT 100
    ");
    $stmt->execute([':drone_id' => $droneId]);
    $pathRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $path = array_map(fn($r) => [
        'lat' => (float)$r['lat'],
        'lng' => (float)$r['lng']
    ], $pathRows);

    // 6) Distanza rimasta ed ETA
    $distanceRemainingKm = haversineDistance($currentLat, $currentLng, $deliveryLat, $deliveryLng);
    $etaMinutes = ($speedKmh > 0)
        ? round(($distanceRemainingKm / $speedKmh) * 60)
        : 0;

    echo json_encode([
        'lat'                   => $currentLat,
        'lng'                   => $currentLng,
        'battery'               => (int)($drone['battery'] ?? 100),
        'status'                => $drone['status'],
        'speed_kmh'             => $speedKmh,
        'distance_remaining_km' => round($distanceRemainingKm, 2),
        'eta_minutes'           => $etaMinutes,
        'simulated'             => $useSimulation,
        'progress'              => round($progress * 100),
        'path'                  => $path,
        'delivery'              => ['lat' => $deliveryLat, 'lng' => $deliveryLng]
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => 'Errore server: ' . $e->getMessage()]);
}