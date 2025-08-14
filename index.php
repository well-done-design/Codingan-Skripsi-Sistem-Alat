<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Larva BSF - Analisis Decision Tree</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <style>
        :root {
            --primary: #2E7D32;
            --secondary: #4CAF50;
            --accent: #8BC34A;   /* Optimal */
            --warning: #f1c40f;  /* Moderate */
            --danger: #FF5252;   /* Poor / Danger */
            --light: #F1F8E9;
            --dark: #1B5E20;
            --info: #4FC3F7;     /* Cukup */
            --harvest: #FFD700;  /* Siap Panen */
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #111; color: white; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; overflow-x: hidden; position: relative; }
        .bg-animation { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2; background: linear-gradient(45deg, #143601, #1B5E20, #0D3311); background-size: 400% 400%; animation: gradient 15s ease infinite; opacity: 0.7; }
        @keyframes gradient { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .particles { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; pointer-events: none; }
        .particle { position: absolute; background-color: rgba(139, 195, 74, 0.5); border-radius: 50%; animation: float linear infinite; }
        @keyframes float { 0% { transform: translateY(0) rotate(0deg); opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; } }
        .main-container { padding: 2rem; max-width: 1200px; margin: 0 auto; position: relative; z-index: 1; }
        .page-title { text-align: center; font-size: 2.5rem; font-weight: 700; color: white; margin-bottom: 2rem; text-shadow: 0 0 10px rgba(0, 0, 0, 0.5); padding: 1rem; background: rgba(0, 0, 0, 0.3); border-radius: 15px; border-left: 5px solid var(--accent); border-right: 5px solid var(--accent); }
        .back-btn { display: inline-block; background-color: var(--dark); color: white; padding: 0.5rem 1rem; border-radius: 5px; text-decoration: none; margin-bottom: 2rem; transition: all 0.3s ease; }
        .back-btn:hover { background-color: var(--primary); transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); color: white; text-decoration: none; }
        .sensor-section { margin-bottom: 3rem; background: rgba(0, 0, 0, 0.5); padding: 1.5rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); backdrop-filter: blur(5px); transition: all 0.3s ease; }
        .sensor-section:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4); }
        .sensor-section-title { color: var(--accent); font-size: 1.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--accent); padding-bottom: 0.5rem; font-weight: 600; }
        .card-container { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .sensor-card { flex: 1; min-width: 250px; background: linear-gradient(135deg, #263238, #1B5E20); border: none; border-radius: 15px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3); transition: all 0.3s ease; overflow: hidden; display: flex; flex-direction: column; }
        .sensor-card:hover { transform: translateY(-10px) scale(1.03); box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4); }
        .card-header { background: linear-gradient(to right, var(--dark), var(--primary)); color: white; font-weight: 600; font-size: 1.2rem; padding: 1rem; border: none; }
        .card-body { padding: 1.5rem 1rem; flex-grow: 1; display: flex; flex-direction: column; justify-content: center; }
        .card-body h1 { font-size: 3rem; font-weight: 700; margin-bottom: 1rem; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5); color: white; }
        .btn-primary { background-color: var(--accent); border: none; padding: 0.7rem 1.5rem; font-weight: 600; border-radius: 50px; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); margin-top: auto; }
        .btn-primary:hover { background-color: var(--primary); transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3); }
        .data-indicator { width: 100%; height: 8px; background-color: rgba(255, 255, 255, 0.1); border-radius: 4px; margin-bottom: 1rem; overflow: hidden; position: relative; }
        .data-indicator-progress { height: 100%; background: linear-gradient(90deg, var(--accent), var(--primary)); border-radius: 4px; transition: width 1s ease; position: relative; }
        .data-unit { font-size: 1.2rem; opacity: 0.8; margin-left: 0.5rem; }
        .value-change { animation: pulse 1s ease; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }
        .ph-display { font-size: 1.8rem; font-weight: 600; margin-top: 1rem; text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3); }
        .decision-path-text { background: rgba(0, 0, 0, 0.3); padding: 1rem; border-radius: 10px; margin-top: 1rem; font-size: 0.9rem; border-left: 3px solid var(--accent); }
        .confidence-meter { width: 100%; height: 20px; background: rgba(255, 255, 255, 0.1); border-radius: 10px; margin: 1rem 0; overflow: hidden; }
        .confidence-fill { height: 100%; background: linear-gradient(90deg, var(--danger), var(--warning), var(--accent)); border-radius: 10px; transition: width 1s ease; }
        .data-stats { display: flex; justify-content: space-around; margin-top: 1rem; }
        .stat-item { text-align: center; }
        .stat-value { font-size: 1.5rem; font-weight: 600; color: var(--accent); }
        .stat-label { font-size: 0.8rem; opacity: 0.7; }

        /* Harvest Status Styles */
        .harvest-status { background: rgba(0, 0, 0, 0.4); padding: 1.5rem; border-radius: 15px; margin-top: 1rem; text-align: center; position: relative; overflow: hidden; border: 2px solid transparent; transition: border-color 0.5s ease; }
        .harvest-message { font-size: 1.3rem; font-weight: 600; margin-bottom: 1rem; text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5); }
        .harvest-timer { font-size: 1.1rem; opacity: 0.9; margin-bottom: 1rem; }
        .harvest-progress { width: 100%; height: 12px; background: rgba(255, 255, 255, 0.1); border-radius: 6px; overflow: hidden; margin-bottom: 1rem; }
        .harvest-progress-fill { height: 100%; border-radius: 6px; transition: width 2s ease; }
        .harvest-icon { font-size: 2rem; margin-bottom: 1rem; }

        .ready-harvest { border-color: var(--harvest); animation: harvestGlow 2s ease-in-out infinite; }
        @keyframes harvestGlow { 0%, 100% { box-shadow: 0 0 20px rgba(255, 215, 0, 0.3); } 50% { box-shadow: 0 0 30px rgba(255, 215, 0, 0.6); } }
        .ready-harvest .harvest-icon { animation: bounce 1s ease-in-out infinite; }
        @keyframes bounce { 0%, 20%, 50%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-10px); } 60% { transform: translateY(-5px); } }
        
        .growing { border-color: var(--accent); }
        .growing .harvest-icon { animation: rotate 3s linear infinite; }
        @keyframes rotate { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .moderate-growth { border-color: var(--info); }

        .warning-acid { border-color: var(--warning); animation: pulseBorder 1.5s infinite; }
        @keyframes pulseBorder { 0%, 100% { border-color: var(--warning); } 50% { border-color: white; } }

        .danger-acid { border-color: var(--danger); animation: shake 0.5s infinite; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        /* --- NEW VISUALIZATION STYLES --- */
        .analysis-column { flex: 1; min-width: 350px; display: flex; flex-direction: column; gap: 20px; }
        .tree-node { background: var(--dark); border: 2px solid var(--primary); border-radius: 10px; padding: 0.75rem; text-align: center; color: white; font-weight: 600; transition: all 0.4s ease; position: relative; }
        .tree-node.active { border-color: var(--accent); transform: scale(1.05); box-shadow: 0 0 20px var(--accent); animation: nodeGlow 1.5s infinite; }
        @keyframes nodeGlow { 0%, 100% { box-shadow: 0 0 15px var(--accent); } 50% { box-shadow: 0 0 25px var(--accent), 0 0 35px var(--accent); } }
        .decision-path-visual { display: flex; flex-direction: column; align-items: center; gap: 1rem; margin-top: 1rem; }
        .tree-level { display: flex; justify-content: center; gap: 1rem; width: 100%; }
        .tree-level .tree-node { flex: 1; }
        .path-arrow { font-size: 1.5rem; color: var(--info); opacity: 0.5; transition: all 0.4s ease; }
        .path-arrow.active { opacity: 1; color: var(--accent); transform: scale(1.2); }
        
        /* Statistics Chart Styles */
        .chart-container { position: relative; height: 200px; margin-top: 1rem; display: flex; justify-content: space-around; align-items: flex-end; padding: 0 1rem; }
        .chart-bar { position: relative; width: 25%; background: linear-gradient(to top, var(--primary), var(--secondary)); border-radius: 5px 5px 0 0; transition: all 0.5s ease-out; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem; text-shadow: 1px 1px 2px black; }
        #optimal-bar { background: linear-gradient(to top, var(--accent), #a1d469); }
        #moderate-bar { background: linear-gradient(to top, var(--warning), #f3d55b); }
        #poor-bar { background: linear-gradient(to top, var(--danger), #ff7a7a); }
        .chart-label { position: absolute; bottom: -25px; left: 50%; transform: translateX(-50%); font-size: 0.9rem; color: white; white-space: nowrap; }

        /* Responsive design */
        @media (max-width: 992px) { .analysis-container { flex-direction: column; } }
        @media (max-width: 768px) { .harvest-message { font-size: 1.1rem; } .harvest-timer { font-size: 1rem; } .harvest-icon, .growth-icon, .new-icon { font-size: 1.5rem; } }
    </style>
</head>
<body>
    <div class="bg-animation"></div>
    <div class="particles"></div>

    <div class="container-fluid main-container">
        <h2 class="page-title" data-aos="zoom-in">
            <i class="fas fa-bug mr-2"></i> BSF Larva Monitoring
            <span style="font-size: 1rem; display: block; margin-top: 10px; opacity: 0.7;">Analisis Real-time dengan Decision Tree</span>
        </h2>
        
        <div class="text-center">
            <a href="https://magrowkit.my.id/index.php" class="back-btn"><i class="fas fa-arrow-left mr-2"></i> Back to Dashboard</a>
        </div>
        
        <div class="sensor-section" data-aos="fade-up">
            <h3 class="sensor-section-title"><i class="fas fa-microchip me-2"></i> Data Sensor Real-time</h3>
            <div class="card-container">
                <div class="card text-center sensor-card" data-aos="flip-left">
                    <div class="card-header"><i class="fas fa-thermometer-half me-2"></i> Temperature</div>
                    <div class="card-body">
                        <div class="data-indicator"><div class="data-indicator-progress" id="temp-indicator"></div></div>
                        <h1><span id="ceksuhu">0</span><span class="data-unit">C</span></h1>
                        <a href="detail_suhu.php" class="btn btn-primary mt-3">View Details</a>
                    </div>
                </div>
                <div class="card text-center sensor-card" data-aos="flip-right" data-aos-delay="200">
                    <div class="card-header"><i class="fas fa-tint me-2"></i> Humidity</div>
                    <div class="card-body">
                        <div class="data-indicator"><div class="data-indicator-progress" id="humidity-indicator"></div></div>
                        <h1><span id="cekkelembaban">0</span><span class="data-unit">%</span></h1>
                        <a href="detail_kelembaban.php" class="btn btn-primary mt-3">View Details</a>
                    </div>
                </div>
                <div class="card text-center sensor-card" data-aos="zoom-in-up" data-aos-delay="400">
                    <div class="card-header"><i class="fas fa-wind me-2"></i> CO2 Level</div>
                    <div class="card-body">
                        <div class="data-indicator"><div class="data-indicator-progress" id="gas-indicator"></div></div>
                        <h1><span id="cekgas">0</span><span class="data-unit">ppm</span></h1>
                        <a href="detail_gas.php" class="btn btn-primary mt-3">View Details</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="sensor-section" data-aos="fade-up" data-aos-delay="200">
            <h3 class="sensor-section-title"><i class="fas fa-brain me-2"></i> Analisis & Visualisasi Decision Tree</h3>
            <div class="d-flex flex-wrap analysis-container" style="gap: 20px;">
                <div class="analysis-column">
                    <div class="card text-center sensor-card" data-aos="zoom-in" style="flex: 1;">
                        <div class="card-header"><i class="fas fa-shield-alt me-2"></i> Prediksi Kondisi & pH</div>
                        <div class="card-body">
                            <div class="ph-display" id="ph-prediction">pH: -</div>
                            <h1 id="dt-verdict">- Menunggu Data -</h1>
                            <div class="confidence-meter"><div class="confidence-fill" id="confidence-fill"></div></div>
                            <div class="data-stats">
                                <div class="stat-item">
                                    <div class="stat-value" id="confidence-value">-%</div>
                                    <div class="stat-label">Confidence</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="accuracy-value">92%</div>
                                    <div class="stat-label">Akurasi Model</div>
                                </div>
                            </div>
                            <div class="decision-path-text" id="decision-path">Menunggu analisis...</div>
                        </div>
                    </div>
                    <div class="card text-center sensor-card" data-aos="fade-up">
                        <div class="card-header"><i class="fas fa-seedling me-2"></i> Status Pertumbuhan Larva</div>
                        <div class="card-body harvest-status" id="harvest-status">
                            <div class="harvest-icon" id="harvest-icon">🐛</div>
                            <div class="harvest-message" id="harvest-message">Menunggu analisis data...</div>
                            <div class="harvest-progress"><div class="harvest-progress-fill" id="harvest-progress-fill"></div></div>
                            <div class="harvest-timer" id="harvest-timer">Status akan diperbarui.</div>
                        </div>
                    </div>
                </div>
                <div class="analysis-column">
                    <div class="card text-center sensor-card" data-aos="zoom-in-up">
                        <div class="card-header"><i class="fas fa-chart-bar me-2"></i> Statistik Keputusan</div>
                        <div class="card-body">
                            <div class="chart-container" id="decision-chart">
                                <div class="chart-bar" id="optimal-bar" style="height: 0%;"><span id="optimal-count">0</span><span class="chart-label">Optimal</span></div>
                                <div class="chart-bar" id="moderate-bar" style="height: 0%;"><span id="moderate-count">0</span><span class="chart-label">Moderate</span></div>
                                <div class="chart-bar" id="poor-bar" style="height: 0%;"><span id="poor-count">0</span><span class="chart-label">Poor</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="card text-center sensor-card" data-aos="fade-up">
                        <div class="card-header"><i class="fas fa-sitemap me-2"></i> Alur Keputusan Real-time</div>
                        <div class="card-body">
                             <div class="decision-path-visual">
                                 <div class="tree-level">
                                     <div class="tree-node" id="root-node">
                                         <i class="fas fa-play"></i><br>Mulai
                                     </div>
                                 </div>
                                 <div class="path-arrow" id="arrow1">▼</div>
                                 <div class="tree-level">
                                     <div class="tree-node" id="temp-node">Suhu: <span id="temp-condition">-</span></div>
                                     <div class="tree-node" id="humidity-node">Lembap: <span id="humidity-condition">-</span></div>
                                     <div class="tree-node" id="co2-node">CO2: <span id="co2-condition">-</span></div>
                                 </div>
                                 <div class="path-arrow" id="arrow2">▼</div>
                                 <div class="tree-level">
                                     <div class="tree-node" id="decision-node">Hasil: <span id="final-decision">-</span></div>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <script>
    // ======================================================================
    // DECISION TREE IMPLEMENTATION
    // ======================================================================
    class DecisionTree {
        constructor() {
            this.trainingData = [
                {suhu: 28.0, kelembaban: 78.0, co2: 523, ph: 7.0},
                {suhu: 30.6, kelembaban: 79.0, co2: 672, ph: 5.9},
                {suhu: 32.6, kelembaban: 77.3, co2: 671, ph: 5.2},
                {suhu: 30.8, kelembaban: 80.0, co2: 639, ph: 5.6},
                {suhu: 28.7, kelembaban: 78.2, co2: 573, ph: 7.2},
                {suhu: 29.4, kelembaban: 70.3, co2: 581, ph: 6.4},
                {suhu: 30.1, kelembaban: 67.8, co2: 617, ph: 6.2},
                {suhu: 31.7, kelembaban: 77.4, co2: 683, ph: 6.0},
                {suhu: 34.5, kelembaban: 71.2, co2: 725, ph: 4.0},
                {suhu: 34.1, kelembaban: 72.0, co2: 710, ph: 4.2},
                {suhu: 33.8, kelembaban: 73.4, co2: 698, ph: 4.5},
                {suhu: 33.5, kelembaban: 74.1, co2: 691, ph: 4.8},
                {suhu: 33.1, kelembaban: 75.0, co2: 685, ph: 5.0},
                {suhu: 32.8, kelembaban: 76.5, co2: 678, ph: 5.1},
                {suhu: 31.2, kelembaban: 78.8, co2: 655, ph: 5.8},
                {suhu: 29.8, kelembaban: 68.5, co2: 605, ph: 6.3},
                {suhu: 29.0, kelembaban: 75.5, co2: 568, ph: 6.8},
                {suhu: 28.2, kelembaban: 79.4, co2: 540, ph: 6.9},
                {suhu: 27.8, kelembaban: 80.1, co2: 521, ph: 7.3},
                {suhu: 27.2, kelembaban: 81.5, co2: 505, ph: 7.5},
                {suhu: 32.0, kelembaban: 79.2, co2: 695, ph: 5.5},
                {suhu: 30.4, kelembaban: 77.7, co2: 633, ph: 6.1},
                {suhu: 28.4, kelembaban: 76.9, co2: 555, ph: 7.1},
                {suhu: 34.8, kelembaban: 70.8, co2: 740, ph: 3.9}
            ];
            this.buildDecisionTree();
        }
        buildDecisionTree() {
            this.rules = [
                { condition: (d) => d.suhu >= 34.0, prediction: () => ({ ph: 4.1, confidence: 0.95, path: "Suhu ekstrem tinggi → pH sangat berbahaya." }), category: "Poor" },
                { condition: (d) => d.suhu <= 28.0 && d.kelembaban >= 80.0, prediction: () => ({ ph: 7.4, confidence: 0.92, path: "Suhu sejuk, sangat lembap → pH mendekati optimal." }), category: "Optimal" },
                { condition: (d) => d.suhu >= 32.5 && d.co2 >= 670, prediction: () => ({ ph: 5.0, confidence: 0.88, path: "Suhu & CO2 tinggi → pH sangat asam." }), category: "Poor" },
                { condition: (d) => d.suhu >= 30.5 && d.suhu <= 31.5, prediction: () => ({ ph: 5.8, confidence: 0.82, path: "Suhu sedang → pH sedikit asam." }), category: "Moderate" },
            ];
        }
        predict(inputData) {
            for (let rule of this.rules) {
                if (rule.condition(inputData)) {
                    const result = rule.prediction();
                    result.category = rule.category;
                    return result;
                }
            }
            return this.nearestNeighborPrediction(inputData);
        }
        nearestNeighborPrediction(inputData) {
            let minDistance = Infinity;
            let nearestData = null;
            for (let data of this.trainingData) {
                const distance = Math.sqrt(Math.pow((inputData.suhu - data.suhu) / 10, 2) + Math.pow((inputData.kelembaban - data.kelembaban) / 20, 2) + Math.pow((inputData.co2 - data.co2) / 200, 2));
                if (distance < minDistance) {
                    minDistance = distance;
                    nearestData = data;
                }
            }
            const confidence = Math.max(0.6, 1 - (minDistance / 2));
            let category = "Moderate";
            // DIUBAH DI SINI
            if (nearestData.ph >= 7.0) category = "Optimal";
            else if (nearestData.ph <= 5.5) category = "Poor";
            return { ph: nearestData.ph, confidence: confidence, path: `Prediksi berdasarkan data terdekat.`, category: category };
        }
        calculateModelAccuracy() {
            let correct = 0;
            for (let data of this.trainingData) {
                const predicted = this.predict(data);
                // DIUBAH DI SINI
                const actualCategory = data.ph >= 7.0 ? "Optimal" : data.ph <= 5.5 ? "Poor" : "Moderate";
                if (predicted.category === actualCategory) correct++;
            }
            return (correct / this.trainingData.length) * 100;
        }
    }

    // ======================================================================
    // HARVEST STATUS MANAGER
    // ======================================================================
    class HarvestStatusManager {
        constructor() {
            this.statuses = {
                optimal: { icon: "🎉", message: "SIAP PANEN!", timer: "Kondisi optimal tercapai.", progress: 100, class: "ready-harvest", color: "var(--harvest)" },
                baik: { icon: "🌱", message: "Kondisi Baik", timer: "Pertumbuhan larva stabil.", progress: 75, class: "growing", color: "var(--accent)" },
                cukup: { icon: "🤔", message: "Kondisi Cukup", timer: "Perlu pemantauan lebih lanjut.", progress: 50, class: "moderate-growth", color: "var(--info)" },
                waspada: { icon: "⚠️", message: "Kondisi Asam!", timer: "Lingkungan mulai tidak sehat.", progress: 25, class: "warning-acid", color: "var(--warning)" },
                bahaya: { icon: "☠️", message: "SANGAT ASAM!", timer: "Kondisi berbahaya bagi larva!", progress: 10, class: "danger-acid", color: "var(--danger)" }
            };
        }
        updateStatus(ph) {
            let status;
            // DIUBAH DI SINI
            if (ph >= 7.0) {
                status = this.statuses.optimal;
            } else if (ph >= 6.0) {
                status = this.statuses.baik;
            } else if (ph >= 5.0) {
                status = this.statuses.cukup;
            } else if (ph >= 4.5) {
                status = this.statuses.waspada;
            } else {
                status = this.statuses.bahaya;
            }
            this.updateUI(status);
        }
        updateUI(status) {
            const harvestStatus = $('#harvest-status');
            harvestStatus.removeClass('ready-harvest growing moderate-growth warning-acid danger-acid').addClass(status.class);
            $('#harvest-icon').text(status.icon);
            $('#harvest-message').text(status.message);
            $('#harvest-timer').text(status.timer);
            $('#harvest-progress-fill').css({ 'width': status.progress + '%', 'background': `linear-gradient(90deg, ${status.color}, ${status.color}aa)` });
        }
    }

    // ======================================================================
    // MAIN APPLICATION LOGIC
    // ======================================================================
    $(document).ready(function() {
        AOS.init({ once: true, duration: 1000 });
        
        const decisionTree = new DecisionTree();
        const harvestManager = new HarvestStatusManager();
        const decisionCounts = { Optimal: 0, Moderate: 0, Poor: 0, total: 0 };

        $('#accuracy-value').text(Math.round(decisionTree.calculateModelAccuracy()) + '%');

        function runDecisionTreeAnalysis() {
            const suhu = parseFloat($('#ceksuhu').text());
            const kelembaban = parseFloat($('#cekkelembaban').text());
            const co2 = parseFloat($('#cekgas').text());
            
            if (isNaN(suhu) || isNaN(kelembaban) || isNaN(co2)) return;

            const inputData = { suhu, kelembaban, co2 };
            const prediction = decisionTree.predict(inputData);
            
            updatePredictionDisplay(prediction);
            harvestManager.updateStatus(prediction.ph);
            updateStatistics(prediction.category);
            animateDecisionTree(inputData, prediction);
        }

        function updatePredictionDisplay(prediction) {
            const { ph, category, path, confidence } = prediction;
            const phEl = $('#ph-prediction');
            const verdictEl = $('#dt-verdict');
            
            phEl.text(`pH: ${ph.toFixed(1)}`).addClass('value-change');
            verdictEl.text(category).addClass('value-change');
            setTimeout(() => {
                phEl.removeClass('value-change');
                verdictEl.removeClass('value-change');
            }, 1000);

            $('#decision-path').text(path);
            const confidencePercent = Math.round(confidence * 100);
            $('#confidence-value').text(confidencePercent + '%');
            $('#confidence-fill').css('width', confidencePercent + '%');

            let colorVar = category === 'Optimal' ? 'var(--accent)' : category === 'Moderate' ? 'var(--warning)' : 'var(--danger)';
            verdictEl.css('color', colorVar);
            phEl.css('color', colorVar);
        }

        function updateStatistics(category) {
            if (category in decisionCounts) {
                decisionCounts[category]++;
                decisionCounts.total++;
            }
            
            const { Optimal, Moderate, Poor, total } = decisionCounts;
            $('#optimal-count').text(Optimal);
            $('#moderate-count').text(Moderate);
            $('#poor-count').text(Poor);

            if (total > 0) {
                $('#optimal-bar').css('height', (Optimal / total) * 100 + '%');
                $('#moderate-bar').css('height', (Moderate / total) * 100 + '%');
                $('#poor-bar').css('height', (Poor / total) * 100 + '%');
            }
        }

        function animateDecisionTree(input, prediction) {
            $('.tree-node, .path-arrow').removeClass('active');
            
            setTimeout(() => $('#root-node').addClass('active'), 100);
            setTimeout(() => {
                $('#arrow1').addClass('active');
                $('#temp-node').addClass('active').find('#temp-condition').text(`${input.suhu}°C`);
                $('#humidity-node').addClass('active').find('#humidity-condition').text(`${input.kelembaban}%`);
                $('#co2-node').addClass('active').find('#co2-condition').text(`${input.co2}ppm`);
            }, 600);
            setTimeout(() => $('#arrow2').addClass('active'), 1100);
            setTimeout(() => {
                $('#decision-node').addClass('active').find('#final-decision').text(prediction.category);
            }, 1600);
        }
        
        function fetchDataAndAnalyze() {
            console.log("Mengambil data baru...");
            Promise.all([
                $.get('ceksuhu.php'),
                $.get('cekkelembaban.php'),
                $.get('cekgas.php')
            ]).then(function(values) {
                $('#ceksuhu').text(values[0]);
                $('#cekkelembaban').text(values[1]);
                $('#cekgas').text(values[2]);
                
                updateIndicator('temp-indicator', values[0], 45);
                updateIndicator('humidity-indicator', values[1], 100);
                updateIndicator('gas-indicator', values[2], 1500);
                
                animateValueChange('ceksuhu');
                animateValueChange('cekkelembaban');
                animateValueChange('cekgas');
                
                runDecisionTreeAnalysis();
            }).catch(function(error) {
                console.error("Gagal mengambil data sensor:", error);
                $('#dt-verdict').text('Gagal Memuat Data');
            });
        }

        setInterval(fetchDataAndAnalyze, 5000); 

        fetchDataAndAnalyze();
    });

    // UTILITY FUNCTIONS
    function updateIndicator(id, value, max) {
        let percent = (parseFloat(value) / max) * 100;
        $(`#${id}`).css('width', Math.min(100, Math.max(0, percent)) + '%');
    }
    function animateValueChange(id) {
        $(`#${id}`).parent().parent().addClass('value-change');
        setTimeout(() => $(`#${id}`).parent().parent().removeClass('value-change'), 1000);
    }
    function createParticles() {
        const container = document.querySelector('.particles');
        if(!container) return;
        for (let i = 0; i < 15; i++) {
            setTimeout(() => {
                const particle = document.createElement('div');
                particle.className = 'particle';
                const size = Math.random() * 5 + 1;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.animationDuration = `${Math.random() * 15 + 10}s`;
                container.appendChild(particle);
                setTimeout(() => particle.remove(), (parseInt(particle.style.animationDuration) * 1000));
            }, i * 500);
        }
    }
    createParticles();
    </script>
</body>
</html>
