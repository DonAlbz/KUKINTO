<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth.php';
require_once 'config.php';

// Recupera droni
$stmt = $pdo->query("SELECT * FROM drones");
$drones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mappa Droni</title>
    <link rel="stylesheet" href="assets/tech.css">


    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />


    <style>
        #map {
            width: 90%;
            height: 600px;
            margin: 30px auto;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>

<?php include 'menu.php'; ?>

<h2 style="text-align:center; margin-top:20px;">Mappa Droni</h2>

<div id="map"></div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
// Inizializza la mappa
var map = L.map('map').setView([45.5416, 10.2118], 12);

// Aggiunge la mappa base
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

// Icone personalizzate
var icons = {
    attivo: L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/854/854878.png',
        iconSize: [32, 32]
    }),
    occupato: L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/854/854866.png',
        iconSize: [32, 32]
    }),
    offline: L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/463/463612.png',
        iconSize: [32, 32]
    })
};

// Marker attivi
var droneMarkers = {};

// Funzione per aggiornare la posizione dei droni
function updateDrones() {
    fetch("get_drones_positions.php")
        .then(res => res.json())
        .then(data => {

            data.forEach(drone => {
                let [lat, lng] = drone.location.split(",");

                // Se il marker esiste → aggiorna posizione
                if (droneMarkers[drone.id]) {
                    droneMarkers[drone.id].setLatLng([lat, lng]);
                } 
                // Altrimenti → crea nuovo marker
                else {
                    droneMarkers[drone.id] = L.marker([lat, lng], {
                        icon: icons[drone.status]
                    }).addTo(map)
                    .bindPopup(`
                        <b>Drone: ${drone.name}</b><br>
                        Stato: ${drone.status}<br>
                        Batteria: ${drone.battery}%<br>
                        Posizione: ${lat}, ${lng}
                    `);
                }
            });

        });
}

// Aggiorna ogni 3 secondi
setInterval(updateDrones, 3000);

// Primo caricamento
updateDrones();
</script>

</body>
</html>
