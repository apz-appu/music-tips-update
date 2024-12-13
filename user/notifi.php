<!-- <?php
session_start();
// require_once 'config.php';
// require_once 'auth_check.php';  // Ensure user is logged in

// Get user's notifications
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM notifications 
          WHERE user_id = ? 
          ORDER BY created_at DESC 
          LIMIT 10";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// // Mark notifications as read
// $update_query = "UPDATE notifications 
//                  SET is_read = TRUE 
//                  WHERE user_id = ? AND is_read = FALSE";
// $update_stmt = $conn->prepare($update_query);
// $update_stmt->bind_param("i", $user_id);
// $update_stmt->execute();
?> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <style>
        .notification-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        .notification {
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .notification.unread {
            background-color: #e3f2fd;
        }
        .notification-time {
            color: #666;
            font-size: 0.9em;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        .no-notifications {
            text-align: center;
            color: #666;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="notification-container">
        <a href="dashboard.php" class="back-button">← Back to Dashboard</a>
        
        <h1>Your Notifications</h1>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="notification <?php echo !$row['is_read'] ? 'unread' : ''; ?>">
                    <p><?php echo htmlspecialchars($row['message']); ?></p>
                    <div class="notification-time">
                        <?php echo date('F j, Y g:i a', strtotime($row['created_at'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-notifications">
                <p>No notifications yet!</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>