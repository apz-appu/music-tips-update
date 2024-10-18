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

// Fetch feedbacks from the database
$feedback_sql = "SELECT f.feedback_id, u.username, c.category_name, f.feedback_text, f.created_at 
                 FROM feedback f 
                 JOIN user u ON f.user_id = u.user_id 
                 JOIN category c ON f.category_id = c.category_id 
                 ORDER BY f.created_at DESC";
$feedback_result = $conn->query($feedback_sql);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Feedback</title>
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
            <span class="ti-menu-alt"></span>
        </div>

        <div class="sidebar-menu">
            <ul>
                <li><a href="test.php"><span class="ti-home"></span><span>Home</span></a></li>
                <li><a href="feedback.php" class="active"><span class="ti-bar-chart"></span><span>Feedback</span></a></li>
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
            <h2>User Feedback</h2>

            <div class="feedback-container">
                <?php if ($feedback_result->num_rows > 0): ?>
                    <table class="feedback-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Category</th>
                                <th>Feedback</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $feedback_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['feedback_text']); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No feedback available at this time.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>