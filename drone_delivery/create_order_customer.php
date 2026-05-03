<?php
require_once "auth_customer.php";
require_once "config.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: login_customer.php");
    exit;
}

$message = "";

/* GEO + VALIDAZIONE BRESCIA */
function geocode_brescia($address) {
    if (stripos($address, "brescia") === false) {
        $full = $address . ", Brescia, Italia";
    } else {
        $full = $address;
    }

    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($full) . "&limit=1&addressdetails=1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "DroneDeliverySystem/1.0");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return false;

    $data = json_decode($response, true);
    if (empty($data)) return false;

    if (stripos($data[0]['display_name'], "Brescia") === false) {
        return false;
    }

    return [
        "lat"   => floatval($data[0]['lat']),
        "lng"   => floatval($data[0]['lon']),
        "clean" => $data[0]['display_name']
    ];
}

/* CREAZIONE ORDINE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pickup   = trim($_POST['pickup_address']   ?? '');
    $delivery = trim($_POST['delivery_address'] ?? '');
    $priority = trim($_POST['priority']         ?? 'bassa');
    $notes    = trim($_POST['notes']            ?? '');

    if ($pickup !== "" && $delivery !== "") {

        $geo_pickup = geocode_brescia($pickup);
        if (!$geo_pickup) {
            $message = "Indirizzo di ritiro non valido o fuori Brescia.";
        } else {
            sleep(1);
            $geo_delivery = geocode_brescia($delivery);
            if (!$geo_delivery) {
                $message = "Indirizzo di consegna non valido o fuori Brescia.";
            }
        }

        if (!$message && $geo_pickup && $geo_delivery) {
            $stmt = $pdo->prepare("
                INSERT INTO orders
                (customer_id, pickup_address, delivery_address, priority, notes, status, created_at,
                 pickup_lat, pickup_lng, delivery_lat, delivery_lng)
                VALUES
                (:cid, :p, :d, :prio, :n, 'in_attesa', NOW(),
                 :plat, :plng, :dlat, :dlng)
            ");

            $stmt->execute([
                ':cid'  => $_SESSION['customer_id'],
                ':p'    => $geo_pickup['clean'],
                ':d'    => $geo_delivery['clean'],
                ':prio' => $priority,
                ':n'    => $notes,
                ':plat' => $geo_pickup['lat'],
                ':plng' => $geo_pickup['lng'],
                ':dlat' => $geo_delivery['lat'],
                ':dlng' => $geo_delivery['lng']
            ]);

            header("Location: customer_dashboard.php");
            exit;
        }

    } else {
        $message = "Compila entrambi gli indirizzi.";
    }
}

include "menu_customer.php";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Crea nuovo ordine</title>

    <style>
        body {
            margin: 0;
            background: #0a0f1f;
            color: #e6f1ff;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        select {
            width: 100%;
            padding: 10px 14px;
            background: #0d1628;
            border: 1px solid rgba(0,234,255,0.2);
            border-radius: 8px;
            color: #e6f1ff;
            font-size: 14px;
            outline: none;
        }

        select option {
            background: #0d1628;
            color: #e6f1ff;
        }
        .form-container {
            width: 90%;
            max-width: 600px;
            margin: 60px auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid #00eaff33;
            border-radius: 12px;
            box-shadow: 0 0 20px #00eaff22;
        }

        h1 {
            text-align: center;
            color: #00eaff;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            color: #00eaff;
            font-weight: 600;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #00eaff44;
            background: rgba(255, 255, 255, 0.05);
            color: #e6f1ff;
            font-size: 15px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 25px;
            background: #00eaff22;
            border: 1px solid #00eaff;
            color: #00eaff;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.25s;
        }

        button:hover {
            background: #00eaff33;
            box-shadow: 0 0 10px #00eaff55;
        }

        .address-wrapper {
            position: relative;
        }

        .autocomplete-box {
            position: absolute;
            left: 0;
            right: 0;
            top: 100%;
            background: #0a0f1f;
            border: 1px solid #00eaff55;
            border-radius: 6px;
            max-height: 180px;
            overflow-y: auto;
            z-index: 9999;
            display: none;
        }

        .autocomplete-item {
            padding: 8px 10px;
            cursor: pointer;
            color: #e6f1ff;
            font-size: 14px;
        }

        .autocomplete-item:hover {
            background: #00eaff22;
        }

        .address-hint {
            height: 14px;
            margin-top: 6px;
            display: flex;
            align-items: center;
        }

        .loading-dot {
            width: 6px;
            height: 6px;
            background: #00eaff;
            border-radius: 50%;
            box-shadow: 0 0 8px #00eaff;
            animation: pulse 0.8s infinite alternate;
        }

        @keyframes pulse {
            from { opacity: 0.3; transform: scale(0.8); }
            to   { opacity: 1;   transform: scale(1.2); }
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #00eaff;
            text-decoration: none;
            font-size: 16px;
            transition: 0.25s;
        }

        .back:hover {
            text-shadow: 0 0 8px #00eaff;
        }

        .error {
            margin-top: 15px;
            color: #ff6b6b;
            text-align: center;
        }

        .input-valid {
            border-color: #69db7c !important;
            box-shadow: 0 0 10px #69db7c55;
        }

        .input-invalid {
            border-color: #ff6b6b !important;
            box-shadow: 0 0 10px #ff6b6b55;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>
<body>

<div class="form-container">

    <h1>Crea un nuovo ordine</h1>

    <div id="hud" style="
        background: rgba(0,234,255,0.06);
        border: 1px solid #00eaff44;
        padding: 18px;
        border-radius: 10px;
        margin-bottom: 25px;
        box-shadow: 0 0 15px #00eaff22;
        display: none;
    ">
        <h3 style="color:#00eaff; margin-top:0;">📡 Info consegna</h3>
        <p id="hud_distance">Distanza: —</p>
        <p id="hud_eta">Tempo stimato: —</p>
        <p id="hud_drone">Drone assegnato: —</p>
    </div>

    <div style="text-align:center; margin-bottom:25px;">
        <svg width="140" height="140" viewBox="0 0 100 100" style="filter: drop-shadow(0 0 6px #00eaffaa);">
            <circle cx="50" cy="50" r="45" stroke="#00eaff55" stroke-width="2" fill="none"/>
            <circle cx="50" cy="50" r="30" stroke="#00eaff33" stroke-width="2" fill="none"/>
            <circle cx="50" cy="50" r="15" stroke="#00eaff22" stroke-width="2" fill="none"/>
            <line id="radarLine" x1="50" y1="50" x2="50" y2="5" stroke="#00eaff" stroke-width="2"/>
        </svg>
    </div>

    <form method="POST">

        <label>Indirizzo di ritiro *</label>
        <div class="address-wrapper">
            <input type="text" id="pickup_address" name="pickup_address" placeholder="Indirizzo di ritiro" required>
            <div class="autocomplete-box" id="box_pickup_address"></div>
        </div>
        <div class="address-hint" id="hint_pickup_address"></div>

        <label>Indirizzo di consegna *</label>
        <div class="address-wrapper">
            <input type="text" id="delivery_address" name="delivery_address" placeholder="Indirizzo di consegna" required>
            <div class="autocomplete-box" id="box_delivery_address"></div>
        </div>
        <div class="address-hint" id="hint_delivery_address"></div>

        <label>Priorità</label>
        <select name="priority">
            <option value="bassa">Bassa</option>
            <option value="media">Media</option>
            <option value="alta">Alta</option>
        </select>

        <label>Note</label>
        <textarea name="notes" placeholder="Informazioni aggiuntive (opzionale)"></textarea>

        <button type="submit">Crea ordine</button>
    </form>

    <?php if ($message): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <a href="customer_dashboard.php" class="back">← Torna alla dashboard</a>

</div>

<script>
gsap.to("#radarLine", {
    rotation: 360,
    transformOrigin: "50% 50%",
    repeat: -1,
    duration: 2,
    ease: "linear"
});

function setupAutocomplete(inputId) {
    const input = document.getElementById(inputId);
    const box   = document.getElementById("box_" + inputId);
    const hint  = document.getElementById("hint_" + inputId);

    input.addEventListener("input", async () => {
        const q = input.value.trim();

        if (q.length < 3) {
            box.style.display = "none";
            box.innerHTML = "";
            hint.innerHTML = "";
            return;
        }

        hint.innerHTML = '<div class="loading-dot"></div>';

        const url = "autocomplete.php?q=" + encodeURIComponent(q);

        let data;
        try {
            const res = await fetch(url);
            data = await res.json();
        } catch (e) {
            box.style.display = "none";
            hint.innerHTML = "";
            return;
        }

        box.innerHTML = "";

        if (!data || !data.features || data.features.length === 0) {
            box.style.display = "none";
            hint.innerHTML = "";
            return;
        }

        hint.innerHTML = "";

        data.features.slice(0, 8).forEach(f => {
            const p = f.properties;

            const street  = p.street || p.name || "";
            const number  = p.housenumber ? " " + p.housenumber : "";
            const city    = p.city ? ", " + p.city : "";
            const country = p.country ? ", " + p.country : "";

            const full = street + number + city + country;

            const div = document.createElement("div");
            div.className = "autocomplete-item";
            div.textContent = full;

            div.addEventListener("click", () => {
                input.value = full;
                box.style.display = "none";
            });

            box.appendChild(div);
        });

        box.style.display = "block";
    });

    document.addEventListener("click", (e) => {
        if (!input.contains(e.target) && !box.contains(e.target)) {
            box.style.display = "none";
        }
    });
}

function validateField(input) {
    if (input.value.trim().length < 5) {
        input.classList.add("input-invalid");
        input.classList.remove("input-valid");
        return false;
    }
    input.classList.add("input-valid");
    input.classList.remove("input-invalid");
    return true;
}

async function updateHUD() {
    const p = document.getElementById("pickup_address").value.trim();
    const d = document.getElementById("delivery_address").value.trim();

    if (p.length < 5 || d.length < 5) return;

    const hud = document.getElementById("hud");
    hud.style.display = "block";

    const drones = ["DRN‑01", "DRN‑07", "DRN‑12", "DRN‑21"];
    const drone = drones[Math.floor(Math.random() * drones.length)];

    const dist = (Math.random() * 4 + 1).toFixed(2);
    const eta = Math.round(dist * 3 + 2);

    document.getElementById("hud_distance").textContent = "Distanza: " + dist + " km";
    document.getElementById("hud_eta").textContent = "Tempo stimato: " + eta + " min";
    document.getElementById("hud_drone").textContent = "Drone assegnato: " + drone;
}

document.addEventListener("DOMContentLoaded", () => {
    setupAutocomplete("pickup_address");
    setupAutocomplete("delivery_address");

    const pickupInput = document.getElementById("pickup_address");
    const deliveryInput = document.getElementById("delivery_address");
    const btn = document.querySelector("button[type='submit']");

    pickupInput.addEventListener("blur", e => validateField(e.target));
    deliveryInput.addEventListener("blur", e => validateField(e.target));

    pickupInput.addEventListener("change", updateHUD);
    deliveryInput.addEventListener("change", updateHUD);

    btn.addEventListener("mouseenter", () => {
        gsap.to(btn, { scale: 1.05, duration: 0.2, boxShadow: "0 0 20px #00eaffaa" });
    });
    btn.addEventListener("mouseleave", () => {
        gsap.to(btn, { scale: 1, duration: 0.2, boxShadow: "0 0 0px transparent" });
    });
    btn.addEventListener("click", () => {
        gsap.to(btn, { scale: 0.92, duration: 0.1 });
    });
});
</script>

</body>
</html>





