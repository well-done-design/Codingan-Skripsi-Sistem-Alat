<!DOCTYPE html>
<html>
<head>
    <title>Grafik Suhu</title>

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <script type="text/javascript" src="assets/js/jquery-3.4.0.min.js"></script>
    <script type="text/javascript" src="assets/js/mdb.min.js"></script>
    <script type="text/javascript" src="jquery-latest.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        body {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            font-family: 'Arial', sans-serif;
            color: white;
            min-height: 100vh;
            margin: 0;
            padding-top: 20px;
            position: relative;
            overflow-x: hidden;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 8s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
            }
        }

        .content-wrapper {
            background-color: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        h3 {
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            margin-bottom: 15px;
        }

        .back-btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(45deg, #3a1c71, #d76d77, #ffaf7b);
            color: white;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: none;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
        }

        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            color: white;
        }

        .back-btn::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #ffaf7b, #d76d77, #3a1c71);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
            border-radius: 50px;
        }

        .back-btn:hover::after {
            opacity: 1;
        }

        #responsecontainer {
            margin: 0 auto;
            transition: all 0.5s ease;
        }

        .subtitle {
            font-style: italic;
            margin-bottom: 25px;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .temp-icon {
            font-size: 40px;
            margin-bottom: 10px;
            color: #FFD700;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }
    </style>

    <script type="text/javascript">
        var refreshid = setInterval(function(){
            $('#responsgrafiksuhu').load('datagrafiksuhu.php');
        }, 3000); 
        
        // Script untuk membuat efek gelembung di background
        document.addEventListener("DOMContentLoaded", function() {
            createBubbles();
        });
        
        function createBubbles() {
            const container = document.body;
            const bubbleCount = 15;
            
            for (let i = 0; i < bubbleCount; i++) {
                const bubble = document.createElement('div');
                bubble.className = 'bubble';
                
                // Random size
                const size = Math.random() * 100 + 50;
                bubble.style.width = `${size}px`;
                bubble.style.height = `${size}px`;
                
                // Random position
                bubble.style.left = `${Math.random() * 100}%`;
                bubble.style.bottom = `-${size}px`;
                
                // Random animation duration
                const duration = Math.random() * 10 + 5;
                bubble.style.animationDuration = `${duration}s`;
                
                // Random delay
                const delay = Math.random() * 5;
                bubble.style.animationDelay = `${delay}s`;
                
                container.appendChild(bubble);
            }
        }
    </script>
</head>
<body>
    <!-- Container Utama -->
    <div class="container content-wrapper">
        <div class="logo-container">
            <i class="fas fa-temperature-high temp-icon"></i>
            <h3>Grafik Sensor Suhu</h3>
            <p class="subtitle">(Data yang ditampilkan 5 data terakhir)</p>
        </div>

        <div class="container">
            <div class="container" id="responsgrafiksuhu" style="width: 90%"></div>
        </div>
        
        <div class="text-center">
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
    
    <script>
        // Script untuk efek loading saat refresh data
        $(document).ready(function() {
            $("#responsgrafiksuhu").on("beforeSend", function() {
                $(this).fadeOut(300);
            }).on("complete", function() {
                $(this).fadeIn(300);
            });
        });
    </script>
</body>
</html>
