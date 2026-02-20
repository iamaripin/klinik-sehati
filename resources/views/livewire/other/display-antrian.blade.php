<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">

    <style>
        body {
            background: #f5f7fa;
        }

        .queue-box {
            min-height: 220px;
        }

        .queue-number {
            font-size: 4rem;
            font-weight: bold;
        }

        .next-number {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .navbar-time {
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .queue-number {
                font-size: 2.5rem;
            }

            .next-number {
                font-size: 1.8rem;
            }
        }

        .notification {
            min-height: 110px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .next-number {
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .next-number {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar is-primary">
        <div class="navbar-brand">
            <div class="navbar-item">
                <h1 class="title has-text-white">🏥 KLINIK SEHAT</h1>
            </div>
        </div>
        <div class="navbar-end">
            <div class="navbar-item">
                <div class="has-text-white" id="clock"></div>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container">
            <div class="columns">

                <!-- KOLOM UTAMA -->
                <div class="column is-5">

                    <!-- ANTRIAN AKTIF -->
                    <div class="box queue-box has-background-danger-light">
                        <h2 class="title has-text-centered">ANTRIAN AKTIF</h2>
                        <div class="has-text-centered">
                            <div id="active-number" class="queue-number has-text-danger">
                                -
                            </div>
                            <p id="active-poli" class="subtitle"></p>
                        </div>
                    </div>

                    <!-- ANTRIAN SELANJUTNYA -->
                    <div class="box queue-box mt-4">
                        <h4 class="title has-text-centered">SELANJUTNYA</h4>
                    
                        <div class="columns has-text-centered mt-4">
                    
                            <!-- NEXT 1 (LEBIH PRIORITAS) -->
                            <div class="column"> 
                                    <div id="next-1" class="next-number has-text-warning-dark">-</div>
                                    <p id="next-1-poli" class="has-text-weight-semibold"></p>
                            </div>
                    
                            <!-- NEXT 2 -->
                            <div class="column">
                                    <div id="next-2" class="next-number has-text-info-dark">-</div>
                                    <p id="next-2-poli" class="has-text-weight-semibold"></p>
                            </div>
                    
                        </div>
                    </div>

                </div>

                <!-- KOLOM VIDEO -->
                <div class="column is-7">
                    <div class="box">
                        <h2 class="title has-text-centered">Informasi</h2>
                        <iframe width="100%" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        // =======================
// JAM REALTIME
// =======================
function updateClock() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    const date = now.toLocaleDateString('id-ID', options);
    const time = now.toLocaleTimeString('id-ID');

    document.getElementById('clock').innerHTML = date + " | " + time;
}
setInterval(updateClock, 1000);
updateClock();

// =======================
// AJAX QUEUE REFRESH
// =======================

function loadQueue() {
    fetch('api/queue/display')
        .then(response => response.json())
        .then(data => {

            // ACTIVE
            document.getElementById('active-number').innerText = data.active?.queue_prefix ?? '-';
            document.getElementById('active-poli').innerText = data.active?.poli ?? '';

            // NEXT 1
            document.getElementById('next-1').innerText = data.next[0]?.queue_prefix ?? '-';
            document.getElementById('next-1-poli').innerText = data.next[0]?.poli ?? '';

            // NEXT 2
            document.getElementById('next-2').innerText = data.next[1]?.queue_prefix ?? '-';
            document.getElementById('next-2-poli').innerText = data.next[1]?.poli ?? '';

        })
        .catch(err => {
            console.error("Gagal load antrian:", err);
        });
}

// Load pertama kali
loadQueue();

// Auto refresh tiap 5 detik
setInterval(loadQueue, 5000);

    </script>

</body>

</html>