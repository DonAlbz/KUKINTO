<?php
header("Content-Type: application/json; charset=UTF-8");

$q = trim($_GET['q'] ?? '');

if (strlen($q) < 2) {
    echo json_encode(["features" => []]);
    exit;
}

// Query corretta per Photon
$query = urlencode($q . " Brescia Italia");

// Photon API
$url = "https://photon.komoot.io/api/?q={$query}&limit=10";

// CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "DroneDeliverySystem/1.0");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    echo json_encode(["features" => []]);
    exit;
}

$data = json_decode($response, true);

// Filtra SOLO risultati dentro Brescia
$filtered = [];

foreach ($data["features"] as $f) {
    $p = $f["properties"];

    // Photon è incoerente: a volte city, a volte district, a volte county
    $isBrescia =
        (isset($p["city"])     && strtolower($p["city"])     === "brescia") ||
        (isset($p["district"]) && strtolower($p["district"]) === "brescia") ||
        (isset($p["county"])   && strtolower($p["county"])   === "brescia");

    if ($isBrescia) {
        $filtered[] = $f;
    }
}

echo json_encode(["features" => $filtered]);
exit;


