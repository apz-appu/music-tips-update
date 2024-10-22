<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melophile</title>
    <link rel="icon" type="image/png" href="image/indexnbg.png">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: black;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .image-container {
            position: relative;
            animation: fadeIn 2s ease-in;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .loading-dots {
            display: flex;
            
        }

        .dot {
            width: 10px;
            height: 10px;
            margin: 0 5px;
            background-color: rgb(115, 204, 219);
            border-radius: 50%;
            animation: bounce 1s infinite;
        }

        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }
        .dot:nth-child(4) { animation-delay: 0.6s; }
        .dot:nth-child(5) { animation-delay: 0.8s; }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        img {
            max-width: 800px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="image-container">
        <img src="image/index.png" alt="Your Image">
    </div>
    
    <div class="loading-dots">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>

    <script>
        // Redirect to testhome.php after 5 seconds
        setTimeout(function() {
            window.location.href = "home/testhome.php";
        }, 5000);
    </script>
</body>
</html>
