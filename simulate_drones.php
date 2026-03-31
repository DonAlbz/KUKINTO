<?php
require_once 'config.php';

// Quanto si muove il drone ad ogni aggiornamento
$step = 0.0005;

// Recupera tutti i droni attivi o occupati
$stmt = $pdo->query("SELECT * FROM drones WHERE status IN ('attivo','occupato')");
$drones = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($drones as $d) {

    // Se non ha posizione → generiamo una posizione iniziale
    if (!$d['location']) {
        $lat = 45.5416 + (rand(-100, 100) / 10000);
        $lng = 10.2118 + (rand(-100, 100) / 10000);
    } else {
        list($lat, $lng) = explode(",", $d['location']);
    }

    // Movimento casuale
    $lat += (rand(-1, 1) * $step);
    $lng += (rand(-1, 1) * $step);

    // Aggiorna posizione nel DB
    $update = $pdo->prepare("UPDATE drones SET location = :loc WHERE id = :id");
    $update->execute([
        ':loc' => "$lat,$lng",
        ':id' => $d['id']
    ]);

    // Salva storico
    $track = $pdo->prepare("
        INSERT INTO drone_tracking (drone_id, latitude, longitude, timestamp)
        VALUES (:id, :lat, :lng, NOW())
    ");
    $track->execute([
        ':id' => $d['id'],
        ':lat' => $lat,
        ':lng' => $lng
    ]);
}

echo "OK";
