<?php
session_start();

if (!isset($_SESSION['signup_id'])) {
    header("Location: ../home/testhome.php");
    exit();
}
include('../home/table.php');

// Fetch admin data
$admin_id = isset($_SESSION['signup_id']) ? $_SESSION['signup_id'] : 1;

// Modified SQL query to properly join tables and handle relationships
$admin_sql = "SELECT 
    a.admin_id,
    a.admin_name, 
    a.email, 
    a.added_at,
    s.signup_time,
    COALESCE((
        SELECT MAX(login_time) 
        FROM log_in l 
        WHERE l.email = a.email
    ), 'Never logged in') as last_login
FROM admin a
INNER JOIN sign_up s ON a.signup_id = s.signup_id
WHERE a.signup_id = ?";

try {
    $stmt = $conn->prepare($admin_sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $admin_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin_data = $result->fetch_assoc();
        
        // Format dates for better display
        $admin_data['added_at'] = date('F j, Y g:i A', strtotime($admin_data['added_at']));
        $admin_data['signup_time'] = date('F j, Y g:i A', strtotime($admin_data['signup_time']));
        if ($admin_data['last_login'] !== 'Never logged in') {
            $admin_data['last_login'] = date('F j, Y g:i A', strtotime($admin_data['last_login']));
        }
    } else {
        // Handle case where admin is not found
        $admin_data = [
            'admin_name' => 'Admin Not Found',
            'email' => 'N/A',
            'added_at' => 'N/A',
            'signup_time' => 'N/A',
            'last_login' => 'N/A'
        ];
        error_log("No admin found for signup_id: " . $admin_id);
    }
} catch (Exception $e) {
    error_log("Error in admin profile: " . $e->getMessage());
    $admin_data = [
        'admin_name' => 'Error Loading Profile',
        'email' => 'N/A',
        'added_at' => 'N/A',
        'signup_time' => 'N/A',
        'last_login' => 'N/A'
    ];
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="icon" type="image/png" href="../image/indexnbg.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style1.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .profile-container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 2rem auto;
        }

        .button-container {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }

        .btn-secondary {
            background-color: #2196F3;
            color: white;
        }

        .btn-danger {
            background-color: #f44336;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="slidebar">
        <div class="slidebar-header">
            <h3 class="brand">
                <span class="ti-music-alt"></span>
                <span>Melophile</span>
            </h3>
            <span class="ti-menu-alt"></span>
        </div>

        <div class="sidebar-menu">
            <ul>
                <li><a href="test.php"><span class="ti-home"></span><span>Home</span></a></li>
                <li><a href="feedback.php"><span class="ti-bar-chart"></span><span>Feedback</span></a></li>
                <li><a href="users.php"><span class=""><ion-icon name="person"></ion-icon></span><span>User</span></a></li>
                <li><a href="tip.php"><span class="ti-tips"><ion-icon name="bulb"></ion-icon></span><span>Tips</span></a></li>
                <li><a href="anews.php"><ion-icon name="newspaper"></ion-icon><span>News</span></a></li>
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

                <div class="button-container">
                    <button id="add-news-btn" class="btn btn-primary">Add News</button>
                    <button id="edit-profile-btn" class="btn btn-secondary">Edit Profile</button>
                    <button id="logout-btn" class="btn btn-danger">Log Out</button>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Add News button handler
        document.getElementById('add-news-btn').addEventListener('click', function() {
            window.location.href = 'add_news.php'; // Navigate to add news page
        });

        // Edit Profile button handler
        document.getElementById('edit-profile-btn').addEventListener('click', function() {
            window.location.href = 'edit_profile_admin.php';
        });

        // Existing Logout button handler
        document.getElementById('logout-btn').addEventListener('click', function() {
            fetch('logout.php', {
                method: 'POST'
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = '../home/testhome.php';
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