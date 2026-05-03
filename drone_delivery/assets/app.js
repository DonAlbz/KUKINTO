// Animazioni homepage
document.addEventListener('DOMContentLoaded', () => {
    const title = document.getElementById('title');
    const subtitle = document.getElementById('subtitle');
    const cta = document.getElementById('cta-btn');
    const drone = document.getElementById('drone-icon');

    if (title && subtitle && cta && drone) {
        gsap.from(title, { y: -40, opacity: 0, duration: 0.8, ease: "power3.out" });
        gsap.from(subtitle, { y: -20, opacity: 0, duration: 0.8, delay: 0.2, ease: "power3.out" });
        gsap.from(cta, { y: 20, opacity: 0, duration: 0.8, delay: 0.4, ease: "power3.out" });

        // Drone che “vola” in loop
        gsap.to(drone, {
            y: -20,
            x: 20,
            rotation: 5,
            duration: 2,
            yoyo: true,
            repeat: -1,
            ease: "sine.inOut"
        });
    }

    // ================================
    // MAPPA DRONI IN TEMPO REALE
    // ================================
    const mapContainer = document.getElementById('map');

    if (mapContainer) {
        // Inizializza la mappa
        const map = L.map('map').setView([45.5416, 10.2118], 12);
            setTimeout(() => {
    map.invalidateSize();
}, 500);
        // Aggiunge la mappa base
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        // Icone personalizzate
        const icons = {
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
        const droneMarkers = {};

        // Funzione per aggiornare la posizione dei droni
        function updateDrones() {
            fetch("get_drones_positions.php")
                .then(res => res.json())
                .then(data => {
                    data.forEach(drone => {
                        let [lat, lng] = drone.location.split(",");

                        if (droneMarkers[drone.id]) {
                            droneMarkers[drone.id].setLatLng([lat, lng]);
                        } else {
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
        updateDrones();
    }

    // ================================
    // GESTIONE FORM ORDINI (già presente)
    // ================================
    const orderForm = document.getElementById('order-form');
    const formMessage = document.getElementById('form-message');
    const ordersBody = document.getElementById('orders-body');

    if (orderForm) {
        orderForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            formMessage.textContent = 'Invio in corso...';
            formMessage.style.color = '#e5e7eb';

            const formData = new FormData(orderForm);

            try {
                const res = await fetch('api_create_order.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    formMessage.textContent = 'Ordine creato con successo! ID #' + data.order_id;
                    formMessage.style.color = '#22c55e';

                    const btn = document.getElementById('create-order-btn');
                    gsap.fromTo(btn, { scale: 1 }, { scale: 1.1, duration: 0.2, yoyo: true, repeat: 1 });

                    refreshOrders();
                    orderForm.reset();
                } else {
                    formMessage.textContent = data.error || 'Errore nella creazione dell’ordine';
                    formMessage.style.color = '#f97373';
                }
            } catch (err) {
                formMessage.textContent = 'Errore di rete';
                formMessage.style.color = '#f97373';
            }
        });
    }

    async function refreshOrders() {
        if (!ordersBody) return;
        try {
            const res = await fetch('api_get_orders.php');
            const orders = await res.json();

            ordersBody.innerHTML = '';
            orders.forEach(o => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>#${o.id}</td>
                    <td>${escapeHtml(o.customer)}</td>
                    <td>${o.drone_code ? escapeHtml(o.drone_code) : '-'}</td>
                    <td>${escapeHtml(o.status)}</td>
                    <td>${o.created_at}</td>
                `;
                ordersBody.appendChild(tr);
            });

            gsap.from('#orders-body tr', {
                opacity: 0,
                y: 10,
                duration: 0.4,
                stagger: 0.05,
                ease: "power2.out"
            });
        } catch (err) {
            console.error(err);
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>"']/g, m => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        })[m]);
    }
});

