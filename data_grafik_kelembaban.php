<?php
    // Koneksi ke database
    $konek = mysqli_connect("localhost", "xigagiwx_magrowkit", "Natuna.1234567890", "xigagiwx_db_multisensor");

    // =======================================================
    // == 1. DATA UNTUK GRAFIK 5 DATA TERAKHIR (KODE ASLI) ==
    // =======================================================
    $tanggal_5 = mysqli_query($konek, "SELECT tanggal from tb_lalat order by id DESC LIMIT 5");
    $humidity_5 = mysqli_query($konek, "SELECT humidity from tb_lalat order by id DESC LIMIT 5");

    // Kueri untuk statistik 5 data terakhir
    $stat_query = mysqli_query($konek, "SELECT MAX(humidity) as max_hum, MIN(humidity) as min_hum, AVG(humidity) as avg_hum FROM (SELECT humidity FROM tb_lalat ORDER BY id DESC LIMIT 5) as recent_data");
    $stats = mysqli_fetch_assoc($stat_query);
    $max_hum = $stats['max_hum'];
    $min_hum = $stats['min_hum'];
    $avg_hum = round($stats['avg_hum'], 1);
    
    $tanggalArray_5 = array();
    $humidityArray_5 = array();


    // ===================================================
    // == 2. DATA BARU UNTUK GRAFIK 24 JAM TERAKHIR    ==
    // ===================================================
    $query_24h = mysqli_query($konek, "SELECT tanggal, humidity FROM tb_lalat WHERE tanggal >= NOW() - INTERVAL 24 HOUR ORDER BY tanggal ASC");
    
    $tanggalArray_24h = array();
    $humidityArray_24h = array();

    while($row = mysqli_fetch_assoc($query_24h)) {
        // Format label agar hanya menampilkan jam dan menit (contoh: 14:30)
        $tanggalArray_24h[] = date("H:i", strtotime($row['tanggal']));
        $humidityArray_24h[] = $row['humidity'];
    }
?>

<div class="panel panel-primary" style="margin-bottom: 30px;">
    <div class="panel-heading" style="text-align: center; background: linear-gradient(135deg, #3498db, #2c3e50); color: white; padding: 15px; border-radius: 5px 5px 0 0; font-weight: bold; font-size: 18px;">
        Grafik Sensor Kelembaban
        <p style="font-size: 14px; margin-top: 5px; font-weight: normal;">(5 Data Terakhir)</p>
    </div>

    <div class="panel-body" style="padding: 20px; background-color: white; border-radius: 0 0 5px 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <div style="position: relative; height: 400px;">
            <canvas id="humidityChart"></canvas>
        </div>
        
        <div style="display: flex; justify-content: space-between; margin-top: 20px; text-align: center;">
            <div style="flex: 1; padding: 15px; background-color: #f8f9fa; border-radius: 10px; margin: 0 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h4 style="color: #3498db; margin: 0 0 5px 0; font-size: 18px;">Kelembaban Tertinggi</h4>
                <p style="font-size: 24px; font-weight: bold; margin: 0; color: #3498db;"><?php echo $max_hum; ?>%</p>
            </div>
            <div style="flex: 1; padding: 15px; background-color: #f8f9fa; border-radius: 10px; margin: 0 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h4 style="color: #3498db; margin: 0 0 5px 0; font-size: 18px;">Kelembaban Terendah</h4>
                <p style="font-size: 24px; font-weight: bold; margin: 0; color: #2c3e50;"><?php echo $min_hum; ?>%</p>
            </div>
            <div style="flex: 1; padding: 15px; background-color: #f8f9fa; border-radius: 10px; margin: 0 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h4 style="color: #3498db; margin: 0 0 5px 0; font-size: 18px;">Kelembaban Rata-rata</h4>
                <p style="font-size: 24px; font-weight: bold; margin: 0; color: #16a085;"><?php echo $avg_hum; ?>%</p>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading" style="text-align: center; background: linear-gradient(135deg, #5dade2, #2874a6); color: white; padding: 15px; border-radius: 5px 5px 0 0; font-weight: bold; font-size: 18px;">
        Grafik Sensor Kelembaban
        <p style="font-size: 14px; margin-top: 5px; font-weight: normal;">(24 Jam Terakhir)</p>
    </div>

    <div class="panel-body" style="padding: 20px; background-color: white; border-radius: 0 0 5px 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <?php if (!empty($humidityArray_24h)): ?>
            <div style="position: relative; height: 400px;">
                <canvas id="humidityChart24h"></canvas>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; font-size: 16px; color: #777;">
                Tidak ada data dalam 24 jam terakhir.
            </div>
        <?php endif; ?>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script type="text/javascript">
    // --- Opsi Umum untuk Grafik ---
    const commonChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        showLines: true,
        animation: {
            duration: 1500,
            easing: 'easeOutQuart'
        },
        plugins: {
            legend: {
                labels: { font: { size: 14, weight: 'bold' } }
            },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleFont: { size: 16 },
                bodyFont: { size: 14 },
                padding: 12,
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        return 'Kelembaban: ' + context.raw + ' %';
                    }
                }
            }
        },
        scales: {
            y: {
                grid: { borderDash: [5, 5] },
                ticks: {
                    callback: function(value) { return value + ' %'; },
                    font: { size: 12 }
                }
            },
            x: {
                grid: { display: false },
                ticks: {
                    maxRotation: 45,
                    minRotation: 45,
                    font: { size: 12 }
                }
            }
        },
        interaction: {
            mode: 'index',
            intersect: false
        },
    };

    // ===================================
    // == Inisialisasi Grafik 1 (5 Data) ==
    // ===================================
    var canvas1 = document.getElementById('humidityChart');
    if (canvas1) {
        var ctx1 = canvas1.getContext('2d');
        var gradient1 = ctx1.createLinearGradient(0, 0, 0, 400);
        gradient1.addColorStop(0, 'rgba(52, 152, 219, 0.6)');
        gradient1.addColorStop(1, 'rgba(52, 152, 219, 0.1)');
        
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: [
                    <?php
                        while($data_tanggal = mysqli_fetch_array($tanggal_5)) { $tanggalArray_5[] = $data_tanggal['tanggal']; }
                        $tanggalArray_5 = array_reverse($tanggalArray_5);
                        foreach($tanggalArray_5 as $tgl) { echo '"'.$tgl.'",'; }
                    ?>
                ],
                datasets: [{
                    label: "Kelembaban (%)",
                    fill: true,
                    backgroundColor: gradient1,
                    borderColor: "rgba(52, 152, 219, 1)",
                    borderWidth: 3,
                    cubicInterpolationMode: 'monotone',
                    pointRadius: 6,
                    data: [
                        <?php
                            while($data_humidity = mysqli_fetch_array($humidity_5)) { $humidityArray_5[] = $data_humidity['humidity']; }
                            $humidityArray_5 = array_reverse($humidityArray_5);
                            foreach($humidityArray_5 as $hum) { echo $hum.','; }
                        ?>
                    ]
                }]
            },
            options: commonChartOptions
        });
    }


    // =======================================
    // == Inisialisasi Grafik 2 (24 Jam)    ==
    // =======================================
    var canvas2 = document.getElementById('humidityChart24h');
    if (canvas2) {
        var ctx2 = canvas2.getContext('2d');
        var gradient2 = ctx2.createLinearGradient(0, 0, 0, 400);
        gradient2.addColorStop(0, 'rgba(93, 173, 226, 0.6)');
        gradient2.addColorStop(1, 'rgba(40, 116, 166, 0.1)');

        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($tanggalArray_24h); ?>,
                datasets: [{
                    label: "Kelembaban (%)",
                    fill: true,
                    backgroundColor: gradient2,
                    borderColor: "rgba(40, 116, 166, 1)",
                    borderWidth: 2,
                    pointRadius: 2,
                    cubicInterpolationMode: 'monotone',
                    data: <?php echo json_encode($humidityArray_24h); ?>
                }]
            },
            options: commonChartOptions
        });
    }

</script>
