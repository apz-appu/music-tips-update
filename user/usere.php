<?php
session_start(); // Start the session

// Check if the user is logged in by verifying if the session variable 'user_id' is set
if (!isset($_SESSION['signup_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: ../home/testhome.php");
    exit();
}
include('../home/table.php');

// Fetch user data (assuming user_id is stored in session)
$user_id = $_SESSION['signup_id']; // Default to 1 if not set for testing

// Prepare the SQL query to fetch user data from the `user` table
$user_sql = "SELECT user_id,username, email, phone, added_at FROM user WHERE signup_id = ?";

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


function deleteTip($tip_id, $user_id) {
    global $conn;
    $delete_sql = "DELETE FROM tips WHERE tip_id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $tip_id, $user_id);
    $result = $delete_stmt->execute();
    $delete_stmt->close();
    return $result;
}

// Handle tip deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tip'])) {
    $tip_id = $_POST['tip_id'];
    if (deleteTip($tip_id, $user_id)) {
        // Refresh the page to show updated tips list
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $delete_error = "Failed to delete tip. Please try again.";
    }
}
$uid=$user_data['user_id'];
// Fetch user's tips from all categories
$tips_sql = "SELECT t.*, c.category_name 
             FROM tips t 
             JOIN category c ON t.category_id = c.category_id 
             WHERE t.user_id = ? 
             ORDER BY t.created_at DESC";

$tips_stmt = $conn->prepare($tips_sql);
$tips_stmt->bind_param("i", $uid);
$tips_stmt->execute();
$tips_result = $tips_stmt->get_result();

$user_tips = [];
while ($tip = $tips_result->fetch_assoc()) {
    $user_tips[] = $tip;
}

$stmt->close();
$tips_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User </title>
    <link rel="icon" type="image/png" href="../image/indexnbg.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style1.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .button-container {
            display: flex;
            gap: 200px;
            justify-content: center;
            margin-top: 60px;
        }

        .profile-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .logout-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
            background-color:red;
            color: white;
        }

        .logout-btn:hover {
            background-color:red ;
            opacity: 0.8;
        }

        .edit-btn {
            background-color: #007bff;
            color: white;
        }

        .feedback-btn {
            background-color: #007bff;
            color: white;
        }

        .add-tip-btn {
            background-color: #007bff;
            color: white;
        }

        .profile-btn:hover {
            background-color:#0060c6 ;
            opacity: 0.9;
        }

        
        .user-tips-section {
            margin-top: 40px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .tip-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .tip-card h4 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .tip-category {
            display: inline-block;
            padding: 3px 8px;
            background: #007bff;
            color: white;
            border-radius: 4px;
            font-size: 0.8em;
            margin-bottom: 10px;
        }

        .tip-date {
            color: #666;
            font-size: 0.9em;
            margin-top: 10px;
        }

        .tip-media {
            margin-top: 10px;
        }

        .tip-media img, .tip-media video {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .no-tips {
            text-align: center;
            color: #666;
            padding: 20px;
        }
        .delete-tip-btn {
            background-color: #dc3545;
            margin-top: auto;
            align-self: flex-end; 
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s;
        }

        .delete-tip-btn:hover {
            background-color: #c82333;
        }

        .tip-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .delete-tip-form {
            display: inline;
        }
    </style>
</head>
<body class="user">
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
                <li><a href="user_dashboard.php"><ion-icon name="home"></ion-icon>Home</a></li>
                <li><a href="vocal.php"><i class="fa-solid fa-microphone-lines"></i>Vocal Tips</a></li>
                <li><a href="guitar.php"><i class="fa-solid fa-guitar"></i>Guitar Tips</a></li>
                <li><a href="drum.php"><i class="fa-solid fa-drum"></i> Drums Tips</a></li>
                <li><a href="keyboard.php"><i class="fa-brands fa-soundcloud"></i> Keyboard Tips</a></li>
                <li class="usr"><a href="usere.php" class="active"><span class="ti-tips"><ion-icon name="shield"></ion-icon></span><span>User</span></a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <header>
            <div>
                <!-- Search Form -->
            <form method="GET" action="search_category_spl.php">
                <input type="search" name="query" placeholder="Search..." >
                <button type="submit"><span class="ti-search"></span></button>
            </form>
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
                        <span>Joined:</span> <?php echo date('F j, Y, g:i a', strtotime($user_data['added_at'])); ?>
                    </div>

                </div>

                <div class="button-container">
                    <button id="edit-profile-btn" class="profile-btn edit-btn">Edit Profile</button>
                    <button id="add-tip-btn" class="profile-btn add-tip-btn">Add Tip</button>
                    <button id="feedback-btn" class="profile-btn feedback-btn">Feedback</button>
                    <button id="logout-btn" class="logout-btn">Log Out</button>
                </div>
                
            </div>
                <div class="user-tips-section">
                    <h3 class="t"><br>My Tips</h3>
                    <?php if (isset($delete_error)): ?>
                        <div class="error-message"><?php echo $delete_error; ?></div>
                    <?php endif; ?>
                    <?php if (empty($user_tips)): ?>
                        <div class="no-tips">
                            <p>You haven't added any tips yet. Click "Add Tip" to share your knowledge!</p>
                        </div>
                    <?php else: ?>
                        <div class="tips-grid">
                            <?php foreach ($user_tips as $tip): ?>
                                <div class="tip-card">
                                    <span class="tip-category"><?php echo htmlspecialchars($tip['category_name']); ?></span>
                                    <h4><?php echo htmlspecialchars($tip['tip_content']); ?></h4>
                                    <?php if ($tip['media_type'] && $tip['media_path']): ?>
                                        <div class="tip-media">
                                            <?php if ($tip['media_type'] == 'image'): ?>
                                                <img src="<?php echo htmlspecialchars($tip['media_path']); ?>" alt="Tip Image">
                                            <?php elseif ($tip['media_type'] == 'video'): ?>
                                                <video controls>
                                                    <source src="<?php echo htmlspecialchars($tip['media_path']); ?>" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            <?php endif; ?>
                                        </div>
                                        <div class="tip-date">
                                            Added on: <?php echo date('F j, Y', strtotime($tip['created_at'])); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="tip-actions">
                                        
                                        <form class="delete-tip-form" method="POST" onsubmit="return confirm('Are you sure you want to delete this tip?');">
                                            <input type="hidden" name="tip_id" value="<?php echo $tip['tip_id']; ?>">
                                            <button type="submit" name="delete_tip" class="delete-tip-btn">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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

        document.getElementById('edit-profile-btn').addEventListener('click', function() {
            window.location.href = 'edit_urs_profile.php';
        });

        document.getElementById('feedback-btn').addEventListener('click', function() {
            window.location.href = 'feedback.php';
        });

        document.getElementById('add-tip-btn').addEventListener('click', function() {
            window.location.href = 'add_tip.php';
        });
    </script>
</body>
</html>
