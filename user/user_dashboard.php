<?php
session_start(); // Start the session

// Check if the user is logged in by verifying if the session variable 'user_id' is set
if (!isset($_SESSION['signup_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: ../home/testhome.php");
    exit();
}

// You can fetch more user details from the database if needed using the session user_id
// Assuming you have a connection file for the database
include('../home/table.php');

// Fetch user details from the database using session user_id
$user_id = $_SESSION['signup_id'];
$user_name = $_SESSION['user_name'];
$sql = "SELECT * FROM sign_up WHERE signup_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc(); // Fetch the user details

// Close the database connection
$stmt->close();
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
</head>
<body class="user">

    <!-- Slidebar -->
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

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h2>User Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($user_name); ?>!</p> <!-- Display user name from session -->
            <input type="search" placeholder="Search">
        </header>

        <div class="main">
            <!-- Dashboard Cards -->
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

            <!-- Additional Sections -->
            <div class="user-section">
                <h2>Your Courses</h2>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Vocal Training</td>
                            <td>Ongoing</td>
                            <td>75%</td>
                        </tr>
                        <tr>
                            <td>Guitar Basics</td>
                            <td>Completed</td>
                            <td>100%</td>
                        </tr>
                        <tr>
                            <td>Music Theory</td>
                            <td>Ongoing</td>
                            <td>40%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
