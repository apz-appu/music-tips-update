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

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style1.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
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
                        <a href="tip.html">View All</a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="Js/scriptlog.js"></script> 
</body>
</html>
