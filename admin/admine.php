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

// Fetch admin data (assuming admin_id is stored in session)
$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 1; // Default to 1 if not set

// Prepare the SQL query to join the necessary tables
$admin_sql = "SELECT a.admin_name, a.email, a.added_at, 
               IFNULL(s.signup_time, 'N/A') as signup_time, 
               (SELECT MAX(login_time) FROM log_in l WHERE l.email = a.email) as last_login
               FROM admin a
               LEFT JOIN sign_up s ON a.email = s.email
               WHERE a.admin_id = ?";



$stmt = $conn->prepare($admin_sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin_data = $result->fetch_assoc();
} else {
    // Handle case where admin is not found
    $admin_data = [
        'admin_name' => 'Admin Not Found',
        'email' => 'N/A',
        'added_at' => 'N/A',
        'signup_time' => 'N/A',
        'last_login' => 'N/A'
    ];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
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
                <li><a href="feedback.php"><span class="ti-bar-chart"></span><span>Feedback</span></a></li>
                <li><a href="users.php"><span class=""><ion-icon name="person"></ion-icon></span><span>User</span></a></li>
                <li><a href="tip.html"><span class="ti-tips"><ion-icon name="bulb"></ion-icon></span><span>Tips</span></a></li>
                <li class="add"><a href="admine.php" class="active"><span class="ti-tips"><ion-icon name="shield"></ion-icon></span><span>Admin</span></a></li>
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
            <h2>Admin Profile</h2>

            <div class="profile-container">
                <div class="profile-header">
                    <img src="/api/placeholder/100/100" alt="Admin Avatar" class="profile-avatar">
                    <div>
                        <h3 class="profile-name"><?php echo htmlspecialchars($admin_data['admin_name']); ?></h3>
                        <p class="profile-role">Super Admin</p>
                    </div>
                </div>
                <div class="profile-details">
                    <div class="profile-item">
                        <span>Email:</span> <span id="email-display"><?php echo htmlspecialchars($admin_data['email']); ?></span>
                    </div>
                    <div class="profile-item">
                        <span>Name:</span> <span id="name-display"><?php echo htmlspecialchars($admin_data['admin_name']); ?></span>
                    </div>
                    <div class="profile-item">
                        <span>Joined:</span> <?php echo htmlspecialchars($admin_data['signup_time']); ?>
                    </div>
                    <div class="profile-item">
                        <span>Last Login:</span> <?php echo htmlspecialchars($admin_data['last_login']); ?>
                    </div>
                    <div class="profile-item">
                        <span>Added At:</span> <?php echo htmlspecialchars($admin_data['added_at']); ?>
                    </div>
                    <div class="profile-item">
                        <span>Access Level:</span> Full Access
                    </div>
                </div>

                
                <!-- Log Out Button -->
                <button id="logout-btn" class="logout-btn">Log Out</button>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('logout-btn').addEventListener('click', function() {
            // Send request to logout.php
            fetch('logout.php', {
                method: 'POST'
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = '../home/testhome.php'; // Redirect to homepage after logout
                } else {
                    alert('Error logging out');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
