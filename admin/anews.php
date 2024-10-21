<?php
session_start();

if (!isset($_SESSION['signup_id'])) {
    header("Location: ../home/testhome.php");
    exit();
}
include('../home/table.php');

// Handle news deletion
if (isset($_POST['delete_news'])) {
    $news_id = $_POST['news_id'];
    
    // First, delete the associated file if it exists
    $file_query = "SELECT media_path FROM add_news WHERE news_id = ?";
    $stmt = $conn->prepare($file_query);
    $stmt->bind_param("i", $news_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $news = $result->fetch_assoc();
        if (!empty($news['media_path'])) {
            $file_path = "../uploads/" . $news['media_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }
    
    // Then delete the news entry
    $delete_sql = "DELETE FROM add_news WHERE news_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $news_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "News deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting news";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all news items
$news_sql = "SELECT n.*, a.admin_name 
             FROM add_news n 
             JOIN admin a ON n.admin_id = a.admin_id 
             ORDER BY n.added_at DESC";
$result = $conn->query($news_sql);
$news_items = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $news_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin News Management</title>
    <link rel="icon" type="image/png" href="../image/indexnbg.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style1.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .news-container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 2rem;
        }

        .news-item {
            border: 1px solid #ddd;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            position: relative;
        }

        .news-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .news-meta {
            font-size: 0.9rem;
            color: #666;
        }

        .news-content {
            margin: 1rem 0;
        }

        .news-media {
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
        }

        .btn-remove {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-remove:hover {
            background-color: #d32f2f;
        }

        .btn-add {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .btn-add:hover {
            background-color: #45a049;
        }

        .message {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
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
                <li><a href="anews.php" class="active"><ion-icon name="newspaper"></ion-icon><span>News</span></a></li>
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

        <main>
            <h2>News Management</h2>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="message success">
                    <?php 
                        echo $_SESSION['message']; 
                        unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error">
                    <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="news-container">
                <button onclick="window.location.href='add_news.php'" class="btn-add">
                    <span class="ti-plus"></span> Add New News
                </button>

                <?php if (empty($news_items)): ?>
                    <p>No news items found.</p>
                <?php else: ?>
                    <?php foreach ($news_items as $news): ?>
                        <div class="news-item">
                            <div class="news-header">
                                <div class="news-meta">
                                    <strong>Posted by:</strong> <?php echo htmlspecialchars($news['admin_name']); ?><br>
                                    <strong>Date:</strong> <?php echo date('F j, Y g:i A', strtotime($news['added_at'])); ?>
                                </div>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this news item?');">
                                    <input type="hidden" name="news_id" value="<?php echo $news['news_id']; ?>">
                                    <button type="submit" name="delete_news" class="btn-remove">
                                        <span class="ti-trash"></span> Remove
                                    </button>
                                </form>
                            </div>
                            
                            <div class="news-content">
                                <?php echo nl2br(htmlspecialchars($news['content'])); ?>
                            </div>

                            <?php if (!empty($news['media_path'])): ?>
                                <?php if ($news['media_type'] == 'image'): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($news['media_path']); ?>" 
                                         alt="News image" class="news-media">
                                <?php elseif ($news['media_type'] == 'video'): ?>
                                    <video controls class="news-media">
                                        <source src="../uploads/<?php echo htmlspecialchars($news['media_path']); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Optional: Add confirmation for delete action
        document.querySelectorAll('.btn-remove').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this news item?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>