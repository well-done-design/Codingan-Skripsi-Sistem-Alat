<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Kondisi BSF - Gelap & Terang (Animasi Lengkap)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <style>
        :root {
            --primary: #2E7D32; --secondary: #4CAF50; --accent: #8BC34A;
            --warning: #f1c40f; --danger: #FF5252; --info: #4FC3F7;
            --lamp-on: #f1c40f;
            --gelap: #95a5a6;
            --terang: #f1c40f;

            /* --- DARK MODE (Default) --- */
            --bg-main: #111; --text-primary: white; --text-secondary: #bdc3c7;
            --section-bg: rgba(0, 0, 0, 0.5); --card-bg: linear-gradient(135deg, #263238, #1B5E20);
            --card-header-bg: linear-gradient(to right, #1B5E20, #2E7D32); --title-bg: rgba(0, 0, 0, 0.3);
            --border-color: rgba(255, 255, 255, 0.1);
        }
        body.theme-bright {
            --bg-main: #f0f2f5; --text-primary: #212529; --text-secondary: #6c757d;
            --section-bg: #ffffff; --card-bg: #ffffff; --card-header-bg: #2E7D32;
            --title-bg: #ffffff; --border-color: #dee2e6;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--bg-main); color: var(--text-primary); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; transition: background 0.3s, color 0.3s; min-height: 100vh; overflow-x: hidden; position: relative; }
        
        /* --- ANIMASI LATAR BELAKANG & PARTIKEL --- */
        .bg-animation { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2; background: linear-gradient(45deg, #143601, #1B5E20, #0D3311); background-size: 400% 400%; animation: gradient 15s ease infinite; opacity: 0.7; }
        @keyframes gradient { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .particles { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; pointer-events: none; }
        .particle { position: absolute; background-color: rgba(139, 195, 74, 0.5); border-radius: 50%; animation: float linear infinite; }
        @keyframes float { 0% { transform: translateY(0) rotate(0deg); opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; } }

        .main-container { padding: 2rem; max-width: 1200px; margin: 0 auto; z-index: 1; position: relative; }
        .page-title { text-align: center; font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; padding: 1rem; background: var(--title-bg); border-radius: 15px; border-left: 5px solid var(--accent); border-right: 5px solid var(--accent); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .header-controls { display: flex; justify-content: center; align-items: center; gap: 1rem; margin-bottom: 2rem; }
        .back-btn, .theme-switcher button { transition: all 0.3s ease; }
        .back-btn { display: inline-block; background-color: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 5px; text-decoration: none; }
        .theme-switcher button { background: var(--section-bg); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; font-size: 1.2rem; cursor: pointer; display: flex; justify-content: center; align-items: center; }
        .sensor-section { margin-bottom: 3rem; background: var(--section-bg); padding: 1.5rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); backdrop-filter: blur(5px); }
        .sensor-section-title { color: var(--accent); font-size: 1.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--accent); padding-bottom: 0.5rem; font-weight: 600; }
        .card-container { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .sensor-card { flex: 1; min-width: 250px; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 15px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; transition: all 0.3s ease; }
        .sensor-card:hover { transform: translateY(-10px) scale(1.03); box-shadow: 0 15px 30px rgba(0,0,0,0.2); }
        .card-header { background: var(--card-header-bg); color: white; font-weight: 600; font-size: 1.2rem; padding: 1rem; }
        .card-body { padding: 1.5rem 1rem; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; }
        .card-body h1 { font-size: 3rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-primary); }
        .card-body p { color: var(--text-secondary); }
        .data-indicator { width: 100%; height: 8px; background-color: rgba(0, 0, 0, 0.1); border-radius: 4px; margin-bottom: 1rem; }
        .data-indicator-progress { height: 100%; background: linear-gradient(90deg, var(--accent), var(--primary)); border-radius: 4px; transition: width 1s ease; }
        .data-unit { font-size: 1.2rem; opacity: 0.8; margin-left: 0.5rem; color: var(--text-secondary); }
        
        /* --- ANIMASI UTAMA --- */
        .value-change { animation: pulse 1s ease; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }
        
        #prediction-verdict.gelap { color: var(--gelap); animation: glowGelap 1.5s infinite alternate; }
        #prediction-verdict.terang { color: var(--terang); animation: glowTerang 1.5s infinite alternate; }
        @keyframes glowGelap { from { text-shadow: 0 0 5px rgba(149, 165, 166, 0.5); } to { text-shadow: 0 0 15px rgba(149, 165, 166, 1); } }
        @keyframes glowTerang { from { text-shadow: 0 0 5px rgba(241, 196, 15, 0.5); } to { text-shadow: 0 0 20px rgba(241, 196, 15, 1); } }

        #lamp-status-verdict.on { color: var(--lamp-on); animation: blink 1s step-end infinite; }
        @keyframes blink { 50% { opacity: 0.4; } }
        #lamp-status-verdict { color: var(--lamp-off-text); }
        
        .chart-container { height: 200px; display: flex; justify-content: space-around; align-items: flex-end; padding: 0 1rem; }
        .chart-bar { position: relative; width: 40%; border-radius: 5px 5px 0 0; transition: all 0.5s ease-out; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem; text-shadow: 1px 1px 2px black; }
        #gelap-bar { background: linear-gradient(to top, #7f8c8d, #95a5a6); }
        #terang-bar { background: linear-gradient(to top, #f39c12, #f1c40f); }
        .chart-label { position: absolute; bottom: -25px; left: 50%; transform: translateX(-50%); font-size: 0.9rem; color: var(--text-primary); white-space: nowrap; }
    </style>
</head>
<body>
    <div class="bg-animation"></div>
    <div class="particles"></div>
    <div class="main-container">
        <h2 class="page-title" data-aos="zoom-in">
            <i class="fas fa-lightbulb mr-2"></i> Analisis Lingkungan BSF
            <span style="font-size: 1rem; display: block; margin-top: 10px; opacity: 0.7;">Prediksi Kondisi Gelap / Terang</span>
        </h2>
        
        <div class="header-controls">
            <a href="http://magrowkit.my.id/" class="back-btn"><i class="fas fa-arrow-left mr-2"></i> Dashboard</a>
            <div class="theme-switcher"><button id="theme-toggle" aria-label="Switch Theme"><i class="fas fa-moon"></i></button></div>
        </div>
        
        <div class="sensor-section" data-aos="fade-up">
            <h3 class="sensor-section-title"><i class="fas fa-microchip me-2"></i> Data Sensor Real-time</h3>
            <div class="card-container">
                <div class="card text-center sensor-card" data-aos="flip-left">
                    <div class="card-header"><i class="fas fa-thermometer-half me-2"></i> Suhu</div>
                    <div class="card-body">
                        <div class="data-indicator"><div id="temp-indicator" class="data-indicator-progress"></div></div>
                        <h1><span id="ceksuhulalat">0</span><span class="data-unit">C</span></h1>
                    </div>
                </div>
                <div class="card text-center sensor-card" data-aos="flip-up" data-aos-delay="200">
                    <div class="card-header"><i class="fas fa-tint me-2"></i> Kelembaban</div>
                    <div class="card-body">
                        <div class="data-indicator"><div id="humidity-indicator" class="data-indicator-progress"></div></div>
                        <h1><span id="cekkelembabanlalat">0</span><span class="data-unit">%</span></h1>
                    </div>
                </div>
                <div class="card text-center sensor-card" data-aos="flip-right" data-aos-delay="400">
                    <div class="card-header"><i class="fas fa-sun me-2"></i> Intensitas Cahaya</div>
                    <div class="card-body">
                        <div class="data-indicator"><div id="light-indicator" class="data-indicator-progress"></div></div>
                        <h1><span id="cekcahaya">0</span><span class="data-unit">lux</span></h1>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="sensor-section" data-aos="fade-up" data-aos-delay="200">
            <h3 class="sensor-section-title"><i class="fas fa-cogs me-2"></i> Analisis & Sistem Kontrol</h3>
            <div class="card-container">
                <div class="card text-center sensor-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="card-header"><i class="fas fa-brain me-2"></i> Prediksi Kondisi (Decision Tree)</div>
                    <div class="card-body">
                        <h1 id="prediction-verdict">-</h1>
                        <p id="prediction-explanation">Menganalisis kondisi lingkungan berdasarkan sensor.</p>
                    </div>
                </div>
                <div class="card text-center sensor-card" data-aos="zoom-in" data-aos-delay="200">
                    <div class="card-header"><i class="fas fa-power-off me-2"></i> Status Lampu Kontrol</div>
                    <div class="card-body">
                        <h1 id="lamp-status-verdict">-</h1>
                        <p id="lamp-status-explanation">Aturan kontrol berdasarkan suhu dan cahaya.</p>
                    </div>
                </div>
                 <div class="card text-center sensor-card" data-aos="zoom-in" data-aos-delay="300">
                    <div class="card-header"><i class="fas fa-chart-bar me-2"></i> Statistik Keputusan</div>
                    <div class="card-body">
                         <div class="chart-container" id="decision-chart">
                            <div class="chart-bar" id="gelap-bar" style="height: 0%;"><span id="gelap-count">0</span><span class="chart-label">Gelap</span></div>
                            <div class="chart-bar" id="terang-bar" style="height: 0%;"><span id="terang-count">0</span><span class="chart-label">Terang</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<script>
// ======================================================================
// FUNGSI ANALISIS BERDASARKAN FLOWCHART
// ======================================================================
const trainingData = [
    { suhu: 24.5, kelembaban: 85, cahaya: 50,  kondisi: 'Gelap' }, { suhu: 28.0, kelembaban: 75, cahaya: 80,  kondisi: 'Gelap' },
    { suhu: 32.0, kelembaban: 65, cahaya: 95,  kondisi: 'Gelap' }, { suhu: 23.0, kelembaban: 92, cahaya: 20,  kondisi: 'Gelap' },
    { suhu: 37.0, kelembaban: 88, cahaya: 60,  kondisi: 'Gelap' }, { suhu: 29.0, kelembaban: 80, cahaya: 250, kondisi: 'Terang' },
    { suhu: 34.0, kelembaban: 70, cahaya: 1500,kondisi: 'Terang' }, { suhu: 38.0, kelembaban: 55, cahaya: 500, kondisi: 'Terang' },
    { suhu: 27.5, kelembaban: 95, cahaya: 350, kondisi: 'Terang' }, { suhu: 26.0, kelembaban: 78, cahaya: 150, kondisi: 'Terang' }
];

function tentukanStatusLampu(suhu, cahaya) {
    if (suhu < 25 && cahaya <= 100) {
        return { status: 'Menyala', alasan: `Kondisi Dingin (${suhu.toFixed(1)}°C) & Gelap (${cahaya.toFixed(1)} lux).` };
    }
    return { status: 'Padam', alasan: 'Kondisi tidak memenuhi syarat (dingin & gelap).' };
}

function predictWithDecisionTree(dataPoint) {
    const { suhu, cahaya } = dataPoint;
    let verdict, explanation;
    if (cahaya <= 100) {
        verdict = 'Gelap';
        explanation = suhu < 25 ? `Kondisi Gelap (${cahaya} lux) dan lingkungan Dingin.` : `Intensitas cahaya rendah (${cahaya} lux).`;
    } else {
        verdict = 'Terang';
        explanation = (suhu >= 27 && suhu <= 30) ? `Kondisi Terang (${cahaya} lux) dengan suhu Ideal.` : `Intensitas cahaya tinggi (${cahaya} lux).`;
    }
    return { verdict, explanation };
}

// ======================================================================
// SCRIPT UTAMA HALAMAN
// ======================================================================
$(document).ready(function() {
    AOS.init({ once: true, duration: 800 });

    const themeToggle = $('#theme-toggle'), body = $('body'), currentTheme = localStorage.getItem('theme');
    const setIcon = (theme) => themeToggle.html(theme === 'bright' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>');
    if (currentTheme) { body.addClass(`theme-${currentTheme}`); setIcon(currentTheme); } else { setIcon('dark'); }
    themeToggle.on('click', () => {
        const isBright = body.hasClass('theme-bright');
        localStorage.setItem('theme', isBright ? 'dark' : 'bright');
        body.toggleClass('theme-bright');
        setIcon(isBright ? 'dark' : 'bright');
    });

    const decisionCounts = { 'Gelap': 0, 'Terang': 0, total: 0 };
    
    function jalankanAnalisis(suhu, kelembaban, cahaya) {
        if (isNaN(suhu) || isNaN(kelembaban) || isNaN(cahaya)) return;

        const hasilPrediksi = predictWithDecisionTree({ suhu, kelembaban, cahaya });
        const verdictEl = $('#prediction-verdict');
        if (verdictEl.text() !== hasilPrediksi.verdict) {
             verdictEl.text(hasilPrediksi.verdict).removeClass('gelap terang').addClass(hasilPrediksi.verdict.toLowerCase());
        }
        $('#prediction-explanation').text(hasilPrediksi.explanation);

        const hasilKontrolLampu = tentukanStatusLampu(suhu, cahaya);
        const lampVerdictEl = $('#lamp-status-verdict');
        const isLampOn = hasilKontrolLampu.status === 'Menyala';
        if (lampVerdictEl.text() !== hasilKontrolLampu.status) {
            lampVerdictEl.text(hasilKontrolLampu.status).toggleClass('on', isLampOn);
        }
        $('#lamp-status-explanation').text(hasilKontrolLampu.alasan);
        
        if (hasilPrediksi.verdict in decisionCounts) {
            decisionCounts[hasilPrediksi.verdict]++;
            decisionCounts.total++;
        }
        const { Gelap, Terang, total } = decisionCounts;
        $('#gelap-count').text(Gelap);
        $('#terang-count').text(Terang);
        if (total > 0) {
            $('#gelap-bar').css('height', (Gelap / total) * 100 + '%');
            $('#terang-bar').css('height', (Terang / total) * 100 + '%');
        }
    }
    
    function fetchDataAndAnalyze() {
        Promise.all([
            $.get('ceksuhulalat.php'),
            $.get('cekkelembabanlalat.php'),
            $.get('cekcahaya.php')
        ]).then(function(values) {
            const [suhu, kelembaban, cahaya] = values.map(v => parseFloat(v));
            
            const animateValueChange = (id, oldVal, newVal) => {
                 if (oldVal.toFixed(1) !== newVal.toFixed(1)) {
                    $(`#${id}`).addClass('value-change');
                    setTimeout(() => $(`#${id}`).removeClass('value-change'), 1000);
                }
            };

            animateValueChange('ceksuhulalat', parseFloat($('#ceksuhulalat').text()), suhu);
            animateValueChange('cekkelembabanlalat', parseFloat($('#cekkelembabanlalat').text()), kelembaban);
            animateValueChange('cekcahaya', parseFloat($('#cekcahaya').text()), cahaya);

            $('#ceksuhulalat').text(suhu.toFixed(1));
            $('#cekkelembabanlalat').text(kelembaban.toFixed(0));
            $('#cekcahaya').text(cahaya.toFixed(0));

            const updateIndicator = (id, val, max) => $(`#${id}`).css('width', `${Math.min(100, (val/max)*100)}%`);
            updateIndicator('temp-indicator', suhu, 45);
            updateIndicator('humidity-indicator', kelembaban, 100);
            updateIndicator('light-indicator', cahaya, 2000);

            jalankanAnalisis(suhu, kelembaban, cahaya);
        }).catch(error => console.error("Gagal mengambil data sensor:", error));
    }
    
    // --- FUNGSI ANIMASI PARTIKEL ---
    function createParticles() {
        const container = document.querySelector('.particles');
        if(!container) return;
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            const size = Math.random() * 5 + 2;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.animationDuration = `${Math.random() * 15 + 10}s`;
            particle.style.animationDelay = `${Math.random() * 5}s`;
            container.appendChild(particle);
        }
    }

    setInterval(fetchDataAndAnalyze, 3000);
    fetchDataAndAnalyze();
    createParticles();
});
</script>

</body>
</html>
