<?php

    // 1. KONEKSI DAN PERSIAPAN DATA
    // ==========================================================
    $konek = mysqli_connect("localhost", "xigagiwx_magrowkit", "Natuna.1234567890", "xigagiwx_db_multisensor");

    // ==========================================================
    // DATA UNTUK PANEL 1 (5 DATA TERAKHIR)
    // ==========================================================
    
    // Ambil 5 data terakhir (lebih efisien dengan satu kueri)
    $query_5_data = mysqli_query($konek, "SELECT tanggal, airQuality FROM tb_larva ORDER BY id DESC LIMIT 5");

    // Siapkan array untuk menampung data mentah
    $tanggalArray_raw = [];
    $gasArray_raw = [];
    while($row = mysqli_fetch_assoc($query_5_data)) {
        $tanggalArray_raw[] = '"' . $row['tanggal'] . '"'; // Langsung tambahkan kutip untuk JS
        $gasArray_raw[] = $row['airQuality'];
    }

    // Balik urutan array agar kronologis (data terlama ke terbaru)
    $js_labels_5_data = implode(',', array_reverse($tanggalArray_raw));
    $js_data_5_data = implode(',', array_reverse($gasArray_raw));
    
    // Kueri statistik untuk 5 data terakhir (kode Anda sudah benar)
    $stat_query = mysqli_query($konek, "SELECT MAX(airQuality) as max_gas, MIN(airQuality) as min_gas, AVG(airQuality) as avg_gas FROM (SELECT airQuality FROM tb_larva ORDER BY id DESC LIMIT 5) as recent_data");
    $stats = mysqli_fetch_assoc($stat_query);
    $max_gas = $stats['max_gas'] ?? 0;
    $min_gas = $stats['min_gas'] ?? 0;
    $avg_gas = round($stats['avg_gas'] ?? 0, 1);


    // ==========================================================
    // DATA UNTUK PANEL 2 (24 JAM TERAKHIR)
    // ==========================================================
    
    // Siapkan array untuk menampung data yang sudah diformat untuk JS
    $labels_24h_array = [];
    $data_gas_24h_array = [];

    // Kueri untuk data 24 jam terakhir
    $query_24h = "SELECT tanggal, airQuality FROM tb_larva 
                  WHERE tanggal >= DATE_SUB(NOW(), INTERVAL 24 HOUR) 
                  ORDER BY tanggal ASC";
    $result_24h = mysqli_query($konek, $query_24h);

    // Loop HANYA SEKALI di sini untuk mengisi kedua array
    if ($result_24h) {
        while($row = mysqli_fetch_assoc($result_24h)) {
            $timestamp = strtotime($row['tanggal']);
            $labels_24h_array[] = '"' . date('H:i', $timestamp) . '"'; // Format tanggal (H:i) dan tambahkan kutip
            $data_gas_24h_array[] = $row['airQuality']; // Simpan data kualitas udara
        }
    }

    // Gabungkan array menjadi string yang siap dicetak di JS
    $js_labels_24h = implode(',', $labels_24h_array);
    $js_data_24h = implode(',', $data_gas_24h_array);
    
    // Kueri statistik untuk data 24 jam terakhir (kode Anda sudah benar)
    $stat_query_24h = mysqli_query($konek, "SELECT MAX(airQuality) as max_gas_24h, MIN(airQuality) as min_gas_24h, AVG(airQuality) as avg_gas_24h 
                                           FROM tb_larva
                                           WHERE tanggal >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stats_24h = mysqli_fetch_assoc($stat_query_24h);
    $max_gas_24h = $stats_24h['max_gas_24h'] ?? 0;
    $min_gas_24h = $stats_24h['min_gas_24h'] ?? 0;
    $avg_gas_24h = round($stats_24h['avg_gas_24h'] ?? 0, 1);

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading" style="text-align: center; background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white; padding: 15px; border-radius: 5px 5px 0 0; font-weight: bold; font-size: 18px;">
                    Grafik Sensor Gas (Kualitas Udara)
                    <p style="font-size: 14px; margin-top: 5px; font-weight: normal;">(Data yang ditampilkan 5 data terakhir)</p>
                </div>

                <div class="panel-body" style="padding: 20px; background-color: white; border-radius: 0 0 5px 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div style="position: relative; height: 300px;">
                        <canvas id="gasChart"></canvas>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-top: 20px; text-align: center;">
                        <div style="flex: 1; padding: 15px; background-color: #f8f9fa; border-radius: 10px; margin: 0 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h4 style="color: #9b59b6; margin: 0 0 5px 0; font-size: 16px;">Kualitas Udara Tertinggi</h4>
                            <p style="font-size: 20px; font-weight: bold; margin: 0; color: #9b59b6;"><?php echo $max_gas; ?> ppm</p>
                        </div>
                        <div style="flex: 1; padding: 15px; background-color: #f8f9fa; border-radius: 10px; margin: 0 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h4 style="color: #9b59b6; margin: 0 0 5px 0; font-size: 16px;">Kualitas Udara Terendah</h4>
                            <p style="font-size: 20px; font-weight: bold; margin: 0; color: #8e44ad;"><?php echo $min_gas; ?> ppm</p>
                        </div>
                        <div style="flex: 1; padding: 15px; background-color: #f8f9fa; border-radius: 10px; margin: 0 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h4 style="color: #9b59b6; margin: 0 0 5px 0; font-size: 16px;">Kualitas Udara Rata-rata</h4>
                            <p style="font-size: 20px; font-weight: bold; margin: 0; color: #6c3483;"><?php echo $avg_gas; ?> ppm</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading" style="text-align: center; background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white; padding: 15px; border-radius: 5px 5px 0 0; font-weight: bold; font-size: 18px;">
                    Grafik Sensor Gas (Kualitas Udara) 24 Jam Terakhir
                    <p style="font-size: 14px; margin-top: 5px; font-weight: normal;">(Data trend 24 jam)</p>
                </div>

                <div class="panel-body" style="padding: 20px; background-color: white; border-radius: 0 0 5px 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div style="position: relative; height: 300px;">
                        <canvas id="gasChart24h"></canvas>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-top: 20px; text-align: center;">
                        <div style="flex: 1; padding: 15px; background-color: #f8f9fa; border-radius: 10px; margin: 0 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h4 style="color: #9b59b6; margin: 0 0 5px 0; font-size: 16px;">Tertinggi 24 Jam</h4>
                            <p style="font-size: 20px; font-weight: bold; margin: 0; color: #9b59b6;"><?php echo $max_gas_24h; ?> ppm</p>
                        </div>
                        <div style="flex: 1; padding: 15px; background-color: #f8f9fa; border-radius: 10px; margin: 0 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h4 style="color: #9b59b6; margin: 0 0 5px 0; font-size: 16px;">Terendah 24 Jam</h4>
                            <p style="font-size: 20px; font-weight: bold; margin: 0; color: #8e44ad;"><?php echo $min_gas_24h; ?> ppm</p>
                        </div>
                        <div style="flex: 1; padding: 15px; background-color: #f8f9fa; border-radius: 10px; margin: 0 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h4 style="color: #9b59b6; margin: 0 0 5px 0; font-size: 16px;">Rata-rata 24 Jam</h4>
                            <p style="font-size: 20px; font-weight: bold; margin: 0; color: #6c3483;"><?php echo $avg_gas_24h; ?> ppm</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script type="text/javascript">
    // Grafik untuk 5 data terakhir
    var canvas = document.getElementById('gasChart');
    var ctx = canvas.getContext('2d');
    
    var gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(155, 89, 182, 0.6)');
    gradient.addColorStop(1, 'rgba(155, 89, 182, 0.1)');
    
    var data = {
        labels : [
            <?php echo $js_labels_5_data; ?>
        ], 
        datasets : [{
            label : "Kualitas Udara (ppm)",
            fill : true,
            backgroundColor : gradient,
            borderColor : "rgba(155, 89, 182, 1)",
            borderWidth: 3,
            cubicInterpolationMode: 'monotone',
            pointRadius : 6,
            pointBackgroundColor: "#fff",
            pointBorderColor: "rgba(155, 89, 182, 1)",
            pointBorderWidth: 2,
            pointHoverRadius: 9,
            pointHoverBackgroundColor: "rgba(155, 89, 182, 1)",
            pointHoverBorderColor: "#fff",
            pointHoverBorderWidth: 2,
            data : [
                <?php echo $js_data_5_data; ?>
            ]
        }]
    };

    var option = {
        responsive: true,
        maintainAspectRatio: false,
        showLines : true,
        animation : {
            duration : 2000,
            easing: 'easeOutQuart'
        },
        plugins: {
            legend: {
                labels: {
                    font: {
                        size: 12,
                        weight: 'bold'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleFont: { size: 14 },
                bodyFont: { size: 12 },
                padding: 12,
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        return 'Kualitas Udara: ' + context.raw + ' ppm';
                    }
                }
            }
        },
        scales: {
            y: {
                grid: { borderDash: [5, 5] },
                ticks: {
                    callback: function(value) { return value + ' ppm'; },
                    font: { size: 11 }
                }
            },
            x: {
                grid: { display: false },
                ticks: {
                    maxRotation: 45,
                    minRotation: 45,
                    font: { size: 10 }
                }
            }
        },
        interaction: { mode: 'index', intersect: false },
        hover: { mode: 'nearest', intersect: true, animationDuration: 300 }
    };

    var myLineChart = new Chart(ctx, {
        type: 'line',
        data : data,
        options : option
    });
    
    // ==========================================================
    // Grafik untuk data 24 jam terakhir (SUDAH DIPERBAIKI)
    // ==========================================================
    var canvas24h = document.getElementById('gasChart24h');
    var ctx24h = canvas24h.getContext('2d');
    
    var gradient24h = ctx24h.createLinearGradient(0, 0, 0, 300);
    gradient24h.addColorStop(0, 'rgba(142, 68, 173, 0.6)');
    gradient24h.addColorStop(1, 'rgba(142, 68, 173, 0.1)');
    
    var data24h = {
        labels : [
            <?php echo $js_labels_24h; ?>
        ], 
        datasets : [{
            label : "Kualitas Udara 24 Jam (ppm)",
            fill : true,
            backgroundColor : gradient24h,
            borderColor : "rgba(142, 68, 173, 1)",
            borderWidth: 2,
            cubicInterpolationMode: 'monotone',
            pointRadius : 3,
            pointBackgroundColor: "#fff",
            pointBorderColor: "rgba(142, 68, 173, 1)",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(142, 68, 173, 1)",
            pointHoverBorderColor: "#fff",
            pointHoverBorderWidth: 2,
            data : [
                <?php echo $js_data_24h; ?>
            ]
        }]
    };

    var option24h = {
        responsive: true,
        maintainAspectRatio: false,
        showLines : true,
        animation : {
            duration : 2000,
            easing: 'easeOutQuart'
        },
        plugins: {
            legend: {
                labels: {
                    font: {
                        size: 12,
                        weight: 'bold'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleFont: { size: 14 },
                bodyFont: { size: 12 },
                padding: 12,
                displayColors: false,
                callbacks: {
                    label: function(context) {
                        return 'Kualitas Udara: ' + context.raw + ' ppm';
                    }
                }
            }
        },
        scales: {
            y: {
                grid: { borderDash: [5, 5] },
                ticks: {
                    callback: function(value) { return value + ' ppm'; },
                    font: { size: 11 }
                }
            },
            x: {
                grid: { display: false },
                ticks: {
                    maxRotation: 45,
                    minRotation: 45,
                    font: { size: 10 },
                    autoSkip: true,
                    maxTicksLimit: 12
                }
            }
        },
        interaction: { mode: 'index', intersect: false },
        hover: { mode: 'nearest', intersect: true, animationDuration: 300 }
    };

    var myLineChart24h = new Chart(ctx24h, {
        type: 'line',
        data : data24h,
        options : option24h
    });

    // Animasi hover pada panel
    var panels = document.querySelectorAll('.panel');
    panels.forEach(function(panel) {
        panel.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
            this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.2)';
        });
        
        panel.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
        });
    });
    
    // Animasi kedip tidak perlu diubah
    // Namun, pastikan ada data sebelum mencoba mengaksesnya
    if (myLineChart.data.datasets[0].data.length > 0) {
        setTimeout(function blinkLastPoint() {
            var lastPoint = myLineChart.data.datasets[0].pointBackgroundColor[myLineChart.data.datasets[0].data.length - 1];
            if (lastPoint === '#fff') {
                myLineChart.data.datasets[0].pointBackgroundColor[myLineChart.data.datasets[0].data.length - 1] = 'rgba(155, 89, 182, 1)';
            } else {
                myLineChart.data.datasets[0].pointBackgroundColor[myLineChart.data.datasets[0].data.length - 1] = '#fff';
            }
            myLineChart.update();
            setTimeout(blinkLastPoint, 1000);
        }, 2000);
    }

</script>
