<?php
    // Koneksi ke database
    $konek = mysqli_connect("localhost", "xigagiwx_magrowkit", "Natuna.1234567890", "xigagiwx_db_multisensor");

    // =======================================================
    // == 1. DATA UNTUK GRAFIK 5 DATA TERAKHIR (KODE ASLI) ==
    // =======================================================
    $tanggal_5 = mysqli_query($konek, "SELECT tanggal from tb_lalat order by id DESC LIMIT 5");
    $temperature_5 = mysqli_query($konek, "SELECT temperature from tb_lalat order by id DESC LIMIT 5");

    // Kueri untuk statistik 5 data terakhir
    $stat_query = mysqli_query($konek, "SELECT MAX(temperature) as max_temp, MIN(temperature) as min_temp, AVG(temperature) as avg_temp FROM (SELECT temperature FROM tb_lalat ORDER BY id DESC LIMIT 5) as recent_data");
    $stats = mysqli_fetch_assoc($stat_query);
    $max_temp = $stats['max_temp'];
    $min_temp = $stats['min_temp'];
    $avg_temp = round($stats['avg_temp'], 1);
    
    $tanggalArray_5 = array();
    $temperatureArray_5 = array();


    // ===================================================
    // == 2. DATA BARU UNTUK GRAFIK 24 JAM TERAKHIR    ==
    // ===================================================
    $query_24h = mysqli_query($konek, "SELECT tanggal, temperature FROM tb_lalat WHERE tanggal >= NOW() - INTERVAL 24 HOUR ORDER BY tanggal ASC");
    
    $tanggalArray_24h = array();
    $temperatureArray_24h = array();

    while($row = mysqli_fetch_assoc($query_24h)) {
        // Format label agar hanya menampilkan jam dan menit (contoh: 14:30)
        $tanggalArray_24h[] = date("H:i", strtotime($row['tanggal']));
        $temperatureArray_24h[] = $row['temperature'];
    }
?>

<div class="panel panel-primary" style="margin-bottom: 30px;">
    <div class="panel-heading" style="text-align: center; background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; padding: 15px; border-radius: 5px 5px 0 0; font-weight: bold; font-size: 18px;">
        Grafik Sensor Suhu
        <p style="font-size: 14px; margin-top: 5px; font-weight: normal;">(5 Data Terakhir)</p>
    </div>

    <div class="panel-body" style="padding: 20px; background-color: white; border-radius: 0 0 5px 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <div style="position: relative; height: 400px;">
            <canvas id="tempChart"></canvas>
        </div>
        
        <div style="display: flex; justify-content: space-between; margin-top: 20px; text-align: center;">
            <div style="flex: 1; padding: 15px; background-color: #f8f9fa; border-radius: 10px; margin: 0 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h4 style="color: #c0392b; margin: 0 0 5px 0; font-size: 18px;">Suhu Tertinggi</h4>
                <p style="font-size: 24px; font-weight: bold; margin: 0; color: #e74c3c;"><?php echo $max_temp; ?>°C</p>
            </div>
            <div style="flex: 1; padding: 15px; background-color: #f8f9fa; border-radius: 10px; margin: 0 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h4 style="color: #c0392b; margin: 0 0 5px 0; font-size: 18px;">Suhu Terendah</h4>
                <p style="font-size: 24px; font-weight: bold; margin: 0; color: #3498db;"><?php echo $min_temp; ?>°C</p>
            </div>
            <div style="flex: 1; padding: 15px; background-color: #f8f9fa; border-radius: 10px; margin: 0 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h4 style="color: #c0392b; margin: 0 0 5px 0; font-size: 18px;">Suhu Rata-rata</h4>
                <p style="font-size: 24px; font-weight: bold; margin: 0; color: #2ecc71;"><?php echo $avg_temp; ?>°C</p>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading" style="text-align: center; background: linear-gradient(135deg, #c0392b, #922b21); color: white; padding: 15px; border-radius: 5px 5px 0 0; font-weight: bold; font-size: 18px;">
        Grafik Sensor Suhu
        <p style="font-size: 14px; margin-top: 5px; font-weight: normal;">(24 Jam Terakhir)</p>
    </div>

    <div class="panel-body" style="padding: 20px; background-color: white; border-radius: 0 0 5px 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <?php if (!empty($temperatureArray_24h)): ?>
            <div style="position: relative; height: 400px;">
                <canvas id="tempChart24h"></canvas>
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
                        return 'Suhu: ' + context.raw + ' °C';
                    }
                }
            }
        },
        scales: {
            y: {
                grid: { borderDash: [5, 5] },
                ticks: {
                    callback: function(value) { return value + ' °C'; },
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
    var canvas1 = document.getElementById('tempChart');
    if (canvas1) {
        var ctx1 = canvas1.getContext('2d');
        var gradient1 = ctx1.createLinearGradient(0, 0, 0, 400);
        gradient1.addColorStop(0, 'rgba(231, 76, 60, 0.6)');
        gradient1.addColorStop(1, 'rgba(192, 57, 43, 0.1)');
        
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
                    label: "Suhu (°C)",
                    fill: true,
                    backgroundColor: gradient1,
                    borderColor: "rgba(231, 76, 60, 1)",
                    borderWidth: 3,
                    cubicInterpolationMode: 'monotone',
                    pointRadius: 6,
                    data: [
                        <?php
                            while($data_temp = mysqli_fetch_array($temperature_5)) { $temperatureArray_5[] = $data_temp['temperature']; }
                            $temperatureArray_5 = array_reverse($temperatureArray_5);
                            foreach($temperatureArray_5 as $temp) { echo $temp.','; }
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
    var canvas2 = document.getElementById('tempChart24h');
    if (canvas2) {
        var ctx2 = canvas2.getContext('2d');
        var gradient2 = ctx2.createLinearGradient(0, 0, 0, 400);
        gradient2.addColorStop(0, 'rgba(192, 57, 43, 0.6)');
        gradient2.addColorStop(1, 'rgba(146, 43, 33, 0.1)');

        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($tanggalArray_24h); ?>,
                datasets: [{
                    label: "Suhu (°C)",
                    fill: true,
                    backgroundColor: gradient2,
                    borderColor: "rgba(192, 57, 43, 1)",
                    borderWidth: 2,
                    pointRadius: 2,
                    cubicInterpolationMode: 'monotone',
                    data: <?php echo json_encode($temperatureArray_24h); ?>
                }]
            },
            options: commonChartOptions
        });
    }

</script>
