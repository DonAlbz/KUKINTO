<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'config.php';

$userRole = $_SESSION['role'];

// Recupera droni
$stmt = $pdo->query("SELECT * FROM drones ORDER BY id DESC");
$drones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Aggiornamento automatico dello stato
foreach ($drones as &$d) {

    // Regola 1: drone occupato se ha un ordine assegnato
    if (!empty($d['current_order'])) {
        $newStatus = 'occupato';

    // Regola 2: drone offline se batteria bassa o coordinate mancanti
    } elseif ($d['battery'] <= 20 || empty($d['latitude']) || empty($d['longitude'])) {
        $newStatus = 'offline';

    // Regola 3: altrimenti è attivo
    } else {
        $newStatus = 'attivo';
    }

    // Aggiorna solo se lo stato è cambiato
    if ($newStatus !== $d['status']) {
        $update = $pdo->prepare("UPDATE drones SET status = :s WHERE id = :id");
        $update->execute([':s' => $newStatus, ':id' => $d['id']]);
        $d['status'] = $newStatus;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Droni</title>

    <!-- Stile Tech Globale -->
    <link rel="stylesheet" href="assets/tech.css">

    <style>
        /* ============================
           LAYOUT DRONI
        ============================ */

        .drones-container {
            width: 90%;
            margin: 40px auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #00eaff;
        }

        .btn-add {
            display: inline-block;
            padding: 10px 18px;
            border: 1px solid #00eaff;
            color: #00eaff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            background: rgba(0, 234, 255, 0.04);
            transition: 0.25s;
        }

        .btn-add:hover {
            background: rgba(0, 234, 255, 0.12);
            box-shadow: 0 0 10px #00eaff55;
        }

        /* Badge stato */
        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .status-attivo { background: #00ff8844; color: #00ff88; }
        .status-occupato { background: #ffcc0044; color: #ffcc00; }
        .status-offline { background: #ff444444; color: #ff4444; }

        .edit-btn {
            color: #00eaff;
            text-decoration: none;
            font-weight: 500;
            transition: 0.25s;
        }

        .edit-btn:hover {
            text-shadow: 0 0 6px #00eaff;
        }
    </style>
</head>
<body>

<?php include 'menu.php'; ?>

<div class="drones-container">

    <h1>Gestione Droni</h1>

    <?php if ($userRole !== 'cliente'): ?>
    <div style="text-align:right; margin-bottom:20px;">
        <a href="new_drone.php" class="btn-add">+ Aggiungi Drone</a>
    </div>
    <?php endif; ?>

    <div class="card">
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Stato</th>
                <th>Batteria</th>
                <th>Posizione</th>
                <th>Azioni</th>
            </tr>

            <?php foreach ($drones as $d): ?>
            <tr>
                <td><?= $d['id'] ?></td>
                <td><?= htmlspecialchars($d['name']) ?></td>

                <td>
                    <span class="status-badge status-<?= $d['status'] ?>">
                        <?= $d['status'] ?>
                    </span>
                </td>

                <td><?= $d['battery'] ?>%</td>

                <td><?= $d['latitude'] ?>, <?= $d['longitude'] ?></td>

                <td>
                    <?php if ($userRole !== 'cliente'): ?>
                        <a href="edit_drone.php?id=<?= $d['id'] ?>" class="edit-btn">Modifica</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>
    </div>

</div>

</body>
</html>
