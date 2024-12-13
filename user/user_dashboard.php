<?php
session_start();

if (!isset($_SESSION['signup_id'])) {
    header("Location: ../home/testhome.php");
    exit();
}
include('../home/table.php');

// Fetch user details
$user_id = $_SESSION['signup_id'];
$user_name = $_SESSION['user_name'];
$sql = "SELECT * FROM sign_up WHERE signup_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch news items
$news_sql = "SELECT n.*, a.admin_name 
             FROM add_news n 
             JOIN admin a ON n.admin_id = a.admin_id 
             ORDER BY n.added_at DESC";
$news_result = $conn->query($news_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style1.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>User Dashboard</title>
    <link rel="icon" type="image/png" href="../image/indexnbg.png">
    <style>
        .news-container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }

        .news-item {
            border: 1px solid #ddd;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            background: #fff;
            transition: transform 0.2s;
        }

     
        .news-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .news-meta {
            font-size: 0.9rem;
            color: #666;
        }

        .news-content {
            margin: 1rem 0;
            line-height: 1.6;
            color: #333;
        }

        .news-media {
            max-width: 60%;
            height: auto;
            margin: 1rem 0;
            border-radius: 4px;
        }

        .news-section-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .news-section-header h3 {
            margin: 0;
            color: #333;
        }

        .news-section-header ion-icon {
            margin-right: 0.5rem;
            font-size: 1.5rem;
            color: #4a90e2;
        }

        .no-news {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-style: italic;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .news-container {
                margin: 1rem;
                padding: 1rem;
            }

            .news-item {
                padding: 1rem;
            }

            .news-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .news-meta {
                margin-top: 0.5rem;
            }
        }

        .dashbord-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); 
            grid-gap: 2rem;
            margin: 2rem 3rem;
            width: calc(100% - 6rem); 
        }

        .card-single {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            min-width: 350px; 
        }

        .card-body {
            padding: 2rem; 
            display: flex;
            align-items: center;
            gap: 2rem; 
        }

        .card-single:hover {
            transform: translateY(-5px);
        }
        .card-body span {
            font-size: 2rem;
            color: #4a90e2;
            padding: 0.5rem;
            border-radius: 50%;
            background: rgba(74, 144, 226, 0.1);
        }

        .card-body div {
            flex: 1;
        }

        .card-body h5 {
            margin: 0;
            font-size: 1.25rem;
            color: #333;
        }

        .card-body small {
            color: #666;
            font-size: 0.875rem;
        }

        .card-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #eee;
        }

        .card-footer a {
            color: #4a90e2;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s ease;
        }

        .card-footer a:hover {
            color: #357abd;
        }

        @media (max-width: 768px) {
            .dashbord-cards {
                grid-template-columns: 1fr;
                margin: 1rem;
            }
        }
    </style>
</head>
<body class="user">
    <div class="slidebar">
        <div class="slidebar-header">
            <h2>User Menu</h2>
        </div>
        <div class="sidebar-menu">
            <ul><br>
                <li><a href="user_dashboard.php" class="active"><ion-icon name="home"></ion-icon>Home</a></li>
                <li><a href="vocal.php"><i class="fa-solid fa-microphone-lines"></i>Vocal Tips</a></li>
                <li><a href="guitar.php"><i class="fa-solid fa-guitar"></i>Guitar Tips</a></li>
                <li><a href="drum.php"><i class="fa-solid fa-drum"></i>Drums Tips</a></li>
                <li><a href="keyboard.php"><i class="fa-brands fa-soundcloud"></i>Keyboard Tips</a></li>
                <li class="usr"><a href="usere.php"><span class="ti-tips"><ion-icon name="shield"></ion-icon></span><span>User</span></a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <header>
            <h2>User Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($user['user_name']); ?>!</p>
            <form method="GET" action="search_category_spl.php">
                <input type="search" name="query" placeholder="Search..." >
                <button type="submit"><span class="ti-search"></span></button>
            </form>
        </header>

        <div class="main">
            <!-- Add Dashboard Cards Section -->
            <div class="dashbord-cards">
                <div class="card-single">
                    <div class="card-body">
                        <span class="ti-user"></span>
                        <div>
                            <h5>Profile</h5>
                            <small>View</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="usere.php">View Profile</a>
                    </div>
                </div>
                <div class="card-single">
                    <div class="card-body">
                        <span class="ti-bell"></span>
                        <div>
                            <h5>Notifications</h5>
                            <small>Check Alerts</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="#">View Notifications</a>
                    </div>
                </div>
            </div>
            <div class="news-container">
                <div class="news-section-header">
                    <ion-icon name="newspaper-outline"></ion-icon>
                    <h3>Latest News</h3>
                </div>

                <?php if ($news_result->num_rows > 0): ?>
                    <?php while ($news = $news_result->fetch_assoc()): ?>
                        <div class="news-item">
                            <div class="news-header">
                                <h4><?php echo htmlspecialchars('Admin ®'); ?></h4>
                                <div class="news-meta">
                                    Posted on: <?php echo date('F j, Y g:i A', strtotime($news['added_at'])); ?>
                                </div>
                            </div>
                            
                            <div class="news-content">
                                <?php echo nl2br(htmlspecialchars($news['content'])); ?>
                            </div>

                            <?php if (!empty($news['media_path'])): ?>
                                <?php if ($news['media_type'] == 'image'): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($news['media_path']); ?>" 
                                         alt="News image" class="news-media">
                                <?php elseif ($news['media_type'] == 'video'): ?>
                                    <video controls class="news-media">
                                        <source src="../uploads/<?php echo htmlspecialchars($news['media_path']); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-news">
                        <p>No news items available at this time.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>