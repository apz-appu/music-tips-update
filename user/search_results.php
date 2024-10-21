<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['signup_id'])) {
    header("Location: ../home/testhome.php");
    exit();
}

include('../home/table.php');

// Get the search query
$search_query = isset($_GET['query']) ? $_GET['query'] : '';

// Fetch search results from the database
$sql = "SELECT content, added_at FROM add_news WHERE content LIKE ? ";
$stmt = $conn->prepare($sql);
$search_term = '%' . $search_query . '%';
$stmt->bind_param("s", $search_term);
$stmt->execute();
$results = $stmt->get_result();

// Close the statement
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
    <title>Search Results</title>
</head>
<body class="user">

    <!-- Slidebar -->
    <div class="slidebar">
        <div class="slidebar-header">
            <h2>User Menu</h2>
        </div>
        <div class="sidebar-menu">
            <ul><br>
                <li><a href="user_dashboard.php"><ion-icon name="home"></ion-icon>Home</a></li>
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
            <h2>Search Results</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p> <!-- Display user name from session -->
        </header>

        <div class="main">
            <?php if ($results->num_rows > 0): ?>
                <h3>Search Results for '<?php echo htmlspecialchars($search_query); ?>':</h3>
                <?php while ($row = $results->fetch_assoc()): ?>
                    <div class="search-result">
                        <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <small>Shared by <?php echo htmlspecialchars($row['user_name']); ?> on <?php echo htmlspecialchars($row['created_at']); ?></small>
                    </div>
                    <hr>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No results found for '<?php echo htmlspecialchars($search_query); ?>'.</p>
            <?php endif; ?>
            
            <!-- Back to Dashboard Section -->
            <div class="back-btn">
                <button name="back-btn" onclick="window.history.back()">Go Back</button> <!-- Go back to the previous page -->
            </div>
        </div>
    </div>

</body>
</html>
