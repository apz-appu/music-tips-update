<?php
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

// Initialize status message
$statusMessage = '';

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete user's reviews first
        $stmt = $conn->prepare("DELETE FROM reviews WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Delete user's feedback
        $stmt = $conn->prepare("DELETE FROM feedback WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Get tips IDs posted by the user (we need these to delete related reviews)
        $stmt = $conn->prepare("SELECT tip_id FROM tips WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tip_ids = [];
        while ($row = $result->fetch_assoc()) {
            $tip_ids[] = $row['tip_id'];
        }
        $stmt->close();
        
        // Delete reviews on user's tips
        if (!empty($tip_ids)) {
            $tip_ids_str = implode(',', $tip_ids);
            $conn->query("DELETE FROM reviews WHERE tip_id IN ($tip_ids_str)");
        }
        
        // Delete user's tips
        $stmt = $conn->prepare("DELETE FROM tips WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Delete the user
        $stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        // If everything is successful, commit the transaction
        $conn->commit();
        $statusMessage = "<div class='alert alert-success'>User and all related data deleted successfully.</div>";
        
    } catch (Exception $e) {
        // If there's an error, roll back the changes
        $conn->rollback();
        $statusMessage = "<div class='alert alert-error'>Error deleting user: " . $e->getMessage() . "</div>";
    }
}

// Fetch data from user table with additional statistics
$sql = "SELECT 
            u.user_id,
            u.username,
            u.email,
            u.phone,
            COUNT(DISTINCT t.tip_id) as tips_count,
            COUNT(DISTINCT f.feedback_id) as feedback_count,
            COUNT(DISTINCT r.review_id) as reviews_count
        FROM user u
        LEFT JOIN tips t ON u.user_id = t.user_id
        LEFT JOIN feedback f ON u.user_id = f.user_id
        LEFT JOIN reviews r ON u.user_id = r.user_id
        GROUP BY u.user_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="icon" type="image/png" href="../image/indexnbg.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style1.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        /* Additional Styles */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .user-table th,
        .user-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .user-table th {
            background-color: black;
        }
        .user-action-btn {
            padding: 8px 12px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .user-action-btn:hover {
            background-color: #c82333;
        }
        .user-cnacel-btn {
            padding: 8px 12px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .user-cnacel-btn:hover {
            background-color: #c82333;
        }
        .stats-badge {
            display: inline-block;
            padding: 2px 6px;
            background-color: #007bff;
            color: white;
            border-radius: 12px;
            font-size: 0.8em;
        }
        .confirm-dialog {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
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
                <li><a href="users.php" class="active"><span><ion-icon name="person"></ion-icon></span><span>User</span></a></li>
                <li><a href="tip.php"><span class="ti-tips"></span><ion-icon name="bulb"></ion-icon><span>Tips</span></a></li>
                <li><a href="anews.php"><ion-icon name="newspaper"></ion-icon><span>News</span></a></li>
                <li class="add"><a href="admine.html"><span class="ti-tips"><ion-icon name="shield"></ion-icon></span><span>Admin</span></a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <header>
            <div>
                <span class="ti-search"></span>
                <input type="search" id="userSearch" placeholder="Search users..." onkeyup="searchUsers()">
            </div>
            <div class="social-icons">
                <span class="ti-bell"></span>
                <span class="ti-comment"></span>
            </div>
        </header>

        <main class="user-section">
            <h2>User Management</h2>
            <?php echo $statusMessage; ?>
            
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Tips</th>
                        <th>Feedback</th>
                        <th>Reviews</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["user_id"] . "</td>";
                            echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["phone"]) . "</td>";
                            echo "<td><span class='stats-badge'>" . $row["tips_count"] . "</span></td>";
                            echo "<td><span class='stats-badge'>" . $row["feedback_count"] . "</span></td>";
                            echo "<td><span class='stats-badge'>" . $row["reviews_count"] . "</span></td>";
                            echo '<td>
                                    <form method="POST" action="" onsubmit="return confirmDelete(' . $row["user_id"] . ')">
                                        <input type="hidden" name="user_id" value="' . $row["user_id"] . '">
                                        <button type="submit" class="user-action-btn">Remove</button>
                                    </form>
                                  </td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>

    <!-- Confirmation Dialog -->
    <div class="overlay" id="overlay"></div>
    <div class="confirm-dialog" id="confirmDialog">
        <h3>Confirm Deletion</h3>
        <p>Are you sure you want to delete this user? This action will remove all associated data including tips, feedback, and reviews.</p>
        <button onclick="proceedWithDelete()" class="user-action-btn">Delete</button>
        <button onclick="cancelDelete()" class="user-cnacel-btn">Cancel</button>
    </div>

    <script>
        let deleteForm = null;

        function confirmDelete(userId) {
            deleteForm = event.target;
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('confirmDialog').style.display = 'block';
            return false;
        }

        function proceedWithDelete() {
            if (deleteForm) {
                deleteForm.submit();
            }
            closeDialog();
        }

        function cancelDelete() {
            closeDialog();
        }

        function closeDialog() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('confirmDialog').style.display = 'none';
            deleteForm = null;
        }

        function searchUsers() {
            const input = document.getElementById('userSearch');
            const filter = input.value.toLowerCase();
            const table = document.querySelector('.user-table');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const text = cell.textContent || cell.innerText;
                        if (text.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        }
    </script>

    <?php
    // Close connection
    $conn->close();
    ?>
</body>
</html>