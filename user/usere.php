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

// Fetch user data (assuming user_id is stored in session)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to 1 if not set for testing

// Prepare the SQL query to fetch user data from the `user` table
$user_sql = "SELECT username, email, phone, added_at FROM user WHERE user_id = ?";

$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
} else {
    // Handle case where user is not found
    $user_data = [
        'username' => 'User Not Found',
        'email' => 'N/A',
        'phone' => 'N/A',
        'added_at' => 'N/A'
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
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style1.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="user">
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
                <li><a href="user_dashboard.php"><ion-icon name="home"></ion-icon>Home</a></li>
                <li><a href="vocal.php"><i class="fa-solid fa-microphone-lines"></i>Vocal Tips</a></li>
                <li><a href="guitar.php"><i class="fa-solid fa-guitar"></i>Guitar Tips</a></li>
                <li><a href="drum.php"><i class="fa-solid fa-drum"></i> Drums Tips</a></li>
                <li><a href="keyboard.php"><i class="fa-brands fa-soundcloud"></i> Keyboard Tips</a></li>
                <li><a href="#"><i class="fa-solid fa-music"></i>Music Theory</a></li>
                <li class="usr"><a href="usere.php" class="active"><span class="ti-tips"><ion-icon name="shield"></ion-icon></span><span>User</span></a></li>
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
            <h2>User Profile</h2>

            <div class="profile-container">
                <div class="profile-header">
                    <img src="/api/placeholder/100/100" alt="User Avatar" class="profile-avatar">
                    <div>
                        <h3 class="profile-name"><?php echo htmlspecialchars($user_data['username']); ?></h3>
                        <p class="profile-role">Regular User</p>
                    </div>
                </div>
                <div class="profile-details">
                    <div class="profile-item">
                        <span>Email:</span> <span id="email-display"><?php echo htmlspecialchars($user_data['email']); ?></span>
                    </div>
                    <div class="profile-item">
                        <span>Username:</span> <span id="username-display"><?php echo htmlspecialchars($user_data['username']); ?></span>
                    </div>
                    <div class="profile-item">
                        <span>Phone:</span> <span id="phone-display"><?php echo htmlspecialchars($user_data['phone']); ?></span>
                    </div>
                    <div class="profile-item">
                        <span>Joined:</span> <?php echo htmlspecialchars($user_data['added_at']); ?>
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
