<?php
// Connect to the database
include '../home/table.php';

$category = 2; 
// Prepare the query to fetch tips for the selected category
$query = "SELECT tips.tip_content, tips.created_at, user.username 
          FROM tips
          JOIN user ON tips.user_id = user.user_id
          WHERE tips.category_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $category); // Binding the category as an integer
$stmt->execute();
$result = $stmt->get_result();

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
                <li><a href="guitar.php" class="active"><i class="fa-solid fa-guitar"></i>Guitar Tips</a></li>
                <li><a href="drum.php"><i class="fa-solid fa-drum"></i> Drums Tips</a></li>
                <li><a href="keyboard.php"><i class="fa-brands fa-soundcloud"></i> Keyboard Tips</a></li>
                <li class="usr"><a href="usere.php"><span class="ti-tips"><ion-icon name="shield"></ion-icon></span><span>User</span></a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <header>
        <h2>Vocal Tips</h2>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp><nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
        <nbsp></nbsp>
   
            <div>
                <span class="ti-search"></span>
                <input type="search" placeholder="Search">
            </div>
            <div class="social-icons">
                <span class="ti-bell"></span>
                <span class="ti-comment"></span>
            </div>
        </header>

    <div class="tips-container">
        <!-- Example of a single tip section -->
     <?php if ($result->num_rows > 0) 
     {
           while ($row = $result->fetch_assoc()) 
           {
               echo '<div class="tip-section">';
               echo '<div class="tip-header">';
               echo '<h3 class="user-name">' . $row['username'] . '</h3>';
               echo '<span class="shared-time">Shared on: ' . $row['created_at'] . '</span>';
               echo '</div>';
               echo '<div class="tip-body">';
               echo '<p>' . $row['tip_content'] . '</p>';
               echo '</div>';
               echo '<div class="tip-footer">';
               echo '<button class="review-btn">Review</button>';
               echo '</div>';
               echo '</div>';
          }
     }
     else 
     {
         echo '<p>No tips available for this category.</p>';
     }
     ?>
     </div>

</body>
</html>
