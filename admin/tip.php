<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "root";  // Adjust this based on your setup
$password = "";
$dbname = "mydb";  // Use the appropriate database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle tip deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_tip'])) {
    $tip_id = $_POST['tip_id'];
    $delete_sql = "DELETE FROM tips WHERE tip_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $tip_id);
    if ($stmt->execute()) {
        echo "Tip deleted successfully!";
    } else {
        echo "Error deleting tip: " . $conn->error;
    }
    $stmt->close();
}

// Fetch tips with user info and category
$sql = "SELECT t.tip_id, t.tip_content, t.media_type, t.media_path, t.created_at, u.username, c.category_name 
        FROM tips t
        JOIN category c ON t.category_id = c.category_id
        JOIN user u ON t.user_id = u.user_id";  // Adjust table and column names if needed
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tips</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style1.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .tips-section {
            padding: 20px;
        }
        .tips-list {
            list-style-type: none;
            padding: 0;
        }
        .tips-list li {
            background-color: #f9f9f9;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 10px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .tips-list li strong {
            font-size: 18px;
            color: #333;
        }
        .tips-list li .highlight {
            font-weight: bold;
            color: #2c3e50;
        }
        .tips-list li .time {
            font-size: 14px;
            color: #7f8c8d;
        }
        .tips-list li img, .tips-list li video {
            display: block;
            margin-top: 10px;
            max-width: 200px;
        }
        .delete-button {
            position: absolute;
            right: 10px;
            bottom: 10px;
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: #c0392b;
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
            <span class="ti-menu-alt"></span>
        </div>

        <div class="sidebar-menu">
            <ul>
                <li><a href="test.php"><span class="ti-home"></span><span>Home</span></a></li>
                <li><a href="feedback.php"><span class="ti-bar-chart"></span><span>Feedback</span></a></li>
                <li><a href="users.php"><span class=""><ion-icon name="person"></ion-icon></span><span>User</span></a></li>
                <li><a href="tip.php" class="active"><span class="ti-tips"><ion-icon name="bulb"></ion-icon></span><span>Tips</span></a></li>
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

        <main class="tips-section">
            <h2>Music Tips</h2>
            <ul class="tips-list">
                <?php
                if ($result->num_rows > 0) {
                    // Output each tip
                    while($row = $result->fetch_assoc()) {
                        echo '<li>';
                        echo '<strong class="highlight">Category: ' . $row['category_name'] . '</strong><br>';
                        echo '<span class="highlight">Posted by: ' . $row['username'] . '</span><br>';
                        echo '<span class="time">on ' . $row['created_at'] . '</span><br>';
                        echo '<p>' . $row['tip_content'] . '</p>';

                        // Show media if available
                        if ($row['media_type'] && $row['media_path']) {
                            if ($row['media_type'] == 'image') {
                                echo '<br><img src="' . $row['media_path'] . '" alt="Tip Image">';
                            } elseif ($row['media_type'] == 'video') {
                                echo '<br><video width="320" height="240" controls>
                                        <source src="' . $row['media_path'] . '" type="video/mp4">
                                        Your browser does not support the video tag.
                                      </video>';
                            }
                        }

                        // Delete button
                        echo '<form method="POST" action="">
                                <input type="hidden" name="tip_id" value="' . $row['tip_id'] . '">
                                <button type="submit" class="delete-button" name="delete_tip">Delete</button>
                              </form>';
                        echo '</li>';
                    }
                } else {
                    echo '<li>No tips available.</li>';
                }
                $conn->close();
                ?>
            </ul>
        </main>
    </div>
</body>
</html>
