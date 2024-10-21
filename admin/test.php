<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Count users from the user table
$user_count_sql = "SELECT COUNT(user_id) AS user_count FROM user";
$user_count_result = $conn->query($user_count_sql);
$user_count = 0;
if ($user_count_result->num_rows > 0) {
    $user_count = $user_count_result->fetch_assoc()['user_count'];
}

// Count tips from the tips table
$tip_count_sql = "SELECT COUNT(tip_id) AS tip_count FROM tips";
$tip_count_result = $conn->query($tip_count_sql);
$tip_count = 0;
if ($tip_count_result->num_rows > 0) {
    $tip_count = $tip_count_result->fetch_assoc()['tip_count'];
}

function getUserStats($conn) {
    $stats = [];
    
    // Today's signups
    $sql = "SELECT COUNT(*) as count FROM sign_up WHERE DATE(signup_time) = CURDATE()";
    $result = $conn->query($sql);
    $stats['today'] = $result->fetch_assoc()['count'];
    
    // Week's signups
    $sql = "SELECT COUNT(*) as count FROM sign_up WHERE signup_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $result = $conn->query($sql);
    $stats['week'] = $result->fetch_assoc()['count'];
    
    // Month's signups
    $sql = "SELECT COUNT(*) as count FROM sign_up WHERE signup_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $result = $conn->query($sql);
    $stats['month'] = $result->fetch_assoc()['count'];
    
    // Total users
    $sql = "SELECT COUNT(*) as count FROM user";
    $result = $conn->query($sql);
    $stats['total'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

function getTipStats($conn) {
    $stats = [];
    
    // Tips by category
    $sql = "SELECT c.category_name, COUNT(t.tip_id) as count 
            FROM category c 
            LEFT JOIN tips t ON c.category_id = t.category_id 
            GROUP BY c.category_id";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        $stats['by_category'][$row['category_name']] = $row['count'];
    }
    
    // Recent tips
    $sql = "SELECT COUNT(*) as count FROM tips WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $result = $conn->query($sql);
    $stats['recent'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

function getLoginStats($conn) {
    $stats = [];
    
    // Today's logins
    $sql = "SELECT COUNT(*) as count FROM log_in WHERE DATE(login_time) = CURDATE()";
    $result = $conn->query($sql);
    $stats['today'] = $result->fetch_assoc()['count'];
    
    // Week's logins
    $sql = "SELECT COUNT(*) as count FROM log_in WHERE login_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $result = $conn->query($sql);
    $stats['week'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

// Get all stats
$userStats = getUserStats($conn);
$tipStats = getTipStats($conn);
$loginStats = getLoginStats($conn);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="icon" type="image/png" href="../image/indexnbg.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style1.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <style>
        .chart-container {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        .metrics-dashboard {
            padding: 20px;
            background-color: #f5f5f5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 1.2rem;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .stat-item.total {
            margin-top: 10px;
            border-bottom: none;
            font-weight: bold;
        }

        .number {
            font-weight: 600;
            color: #2196F3;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 300px;
        }
        </style>
</head>
<body>
    <div class="slidebar">
        <div class="slidebar-header">
            <h3 class="brand">
                <span class="ti-music-alt"></span>
                <span>Music Tips and Tricks</span>
            </h3>
            <button class="ti-menu-alt"></button>
        </div>

        <div class="sidebar-menu">
            <ul>
                <li><a href="test.php" class="active"><span class="ti-home"></span><span>Home</span></a></li>
                <li><a href="feedback.php"><span class="ti-bar-chart"></span><span>Feedback</span></a></li>
                <li><a href="users.php"><span class=""><ion-icon name="person"></ion-icon></span><span>User</span></a></li>
                <li><a href="tip.php"><span class="ti-tips"><ion-icon name="bulb"></ion-icon></span><span>Tips</span></a></li>
                <li><a href="anews.php"><ion-icon name="newspaper"></ion-icon><span>News</span></a></li>
                <li class="add"><a href="admine.php"><span class="ti-tips"><ion-icon name="shield"></ion-icon></span><span>Admin</span></a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <header>
            <div>
                <span class="ti-search"></span>
                <input type="search" placeholder="Search">
            </div>
            <div class="social-icons">
                <span class="ti-bell"></span>
                <span class="ti-comment"></span>
            </div>
        </header>

        <main>
            <h2>Overview</h2>

            <div class="dashbord-cards">
                <div class="card-single">
                    <div class="card-body">
                        <span class=""><ion-icon name="person"></ion-icon></span>
                        <div>
                            <h5>Users</h5>
                            <h4><?php echo $user_count; ?></h4>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="users.php">View All</a>
                    </div>
                </div>

                <div class="card-single">
                    <div class="card-body">
                        <span class=""><ion-icon name="bulb"></ion-icon></span>
                        <div>
                            <h5>Tips</h5>
                            <h4><?php echo $tip_count; ?></h4>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="tip.php">View All</a>
                    </div>
                </div>
            </div>
            <div class="metrics-dashboard">
    <!-- Main stats cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>User Statistics</h3>
            <div class="stat-item">
                <span>Today's Signups</span>
                <span class="number"><?php echo $userStats['today']; ?></span>
            </div>
            <div class="stat-item">
                <span>This Week</span>
                <span class="number"><?php echo $userStats['week']; ?></span>
            </div>
            <div class="stat-item">
                <span>This Month</span>
                <span class="number"><?php echo $userStats['month']; ?></span>
            </div>
            <div class="stat-item total">
                <span>Total Users</span>
                <span class="number"><?php echo $userStats['total']; ?></span>
            </div>
        </div>
        
        <div class="stat-card">
            <h3>Login Activity</h3>
            <div class="stat-item">
                <span>Today's Logins</span>
                <span class="number"><?php echo $loginStats['today']; ?></span>
            </div>
            <div class="stat-item">
                <span>This Week</span>
                <span class="number"><?php echo $loginStats['week']; ?></span>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <!-- User Growth Chart -->
        <div class="chart-container">
            <canvas id="userGrowthChart"></canvas>
        </div>
        
        <!-- Tips by Category Chart -->
        <div class="chart-container">
            <canvas id="tipsCategoryChart"></canvas>
        </div>
    </div>
</div>
        </main>
    </div>
    <script src="Js/scriptlog.js"></script> 
</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Growth Chart
    var userCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userCtx, {
        type: 'line',
        data: {
            labels: ['Today', 'This Week', 'This Month', 'Total'],
            datasets: [{
                label: 'User Growth',
                data: [
                    <?php echo $userStats['today']; ?>,
                    <?php echo $userStats['week']; ?>,
                    <?php echo $userStats['month']; ?>,
                    <?php echo $userStats['total']; ?>
                ],
                borderColor: '#2196F3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            title: {
                display: true,
                text: 'User Growth Trends'
            }
        }
    });

    // Tips by Category Chart
    var tipsCtx = document.getElementById('tipsCategoryChart').getContext('2d');
    new Chart(tipsCtx, {
        type: 'doughnut',
        data: {
            labels: [<?php echo "'" . implode("','", array_keys($tipStats['by_category'])) . "'"; ?>],
            datasets: [{
                data: [<?php echo implode(',', $tipStats['by_category']); ?>],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            title: {
                display: true,
                text: 'Tips by Category'
            }
        }
    });
});
</script>