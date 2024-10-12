<?php
session_start(); // Start the session

// Check if the user is logged in by verifying if the session variable 'user_id' is set
if (!isset($_SESSION['signup_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: ../home/testhome.php");
    exit();
}
include('../home/table.php');

$user_id = $_SESSION['signup_id'];
$user_name = $_SESSION['user_name'];
$sql = "SELECT * FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc(); // Fetch the user details

$category = 3; 
// Prepare the query to fetch tips for the selected category
$query = "SELECT tips.tip_id, tips.tip_content, tips.created_at, tips.media_type, tips.media_path, user.username 
          FROM tips
          JOIN user ON tips.user_id = user.user_id
          WHERE tips.category_id = ?
          ORDER BY tips.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $category);
$stmt->execute();
$result = $stmt->get_result();

// Handle review fetching via AJAX
if(isset($_POST['get_reviews'])) {
    $tip_id = $_POST['tip_id'];
    $review_query = "SELECT reviews.review_content, reviews.created_at, user.username
                     FROM reviews 
                     JOIN user ON reviews.user_id = user.user_id 
                     WHERE reviews.tip_id = ?
                     ORDER BY reviews.created_at DESC";
    $review_stmt = $conn->prepare($review_query);
    $review_stmt->bind_param("i", $tip_id);
    $review_stmt->execute();
    $reviews = $review_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    echo json_encode($reviews);
    exit();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drums Tips</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style1.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
       

        .tip-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }

        .tip-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }

        .user-info {
            flex-grow: 1;
        }

        .user-name {
            margin: 0;
            color: #333;
            font-size: 1.1em;
            font-weight: bold;
        }

        .shared-time {
            color: #666;
            font-size: 0.8em;
        }

        .tip-body {
            margin: 15px 0;
            color: #444;
            line-height: 1.6;
        }

        .tip-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }

        .view-reviews-btn, .review-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .view-reviews-btn {
            background-color: #4CAF50;
        }

        .view-reviews-btn:hover {
            background-color: #45a049;
        }

        .review-btn {
            background-color: #2196F3;
        }

        .review-btn:hover {
            background-color: #1976D2;
        }

        .reviews-section {
            display: none;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .review-item {
            display: flex;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .review-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }

        .review-content-wrapper {
            flex-grow: 1;
            padding-left: 25px;
        }

        .review-header {
            margin-bottom: 8px;
        }

        .review-author {
            font-weight: bold;
            color: #333;
        }

        .review-date {
            font-size: 0.85em;
            color: #666;
            display: block;
            margin-top: 2px;
        }

        .review-content {
            color: #444;
            line-height: 1.5;
        }

        /* Modal styles for adding reviews */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            width: 50%;
            border-radius: 10px;
        }

        .close-modal {
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
        }

        .review-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
        }

        .submit-review {
            background-color: #2196F3;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .sear{
            padding-left: 72%;
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
                <li><a href="drum.php" class="active"><i class="fa-solid fa-drum"></i> Drums Tips</a></li>
                <li><a href="keyboard.php"><i class="fa-brands fa-soundcloud"></i> Keyboard Tips</a></li>
                <li class="usr"><a href="usere.php"><span class="ti-tips"><ion-icon name="shield"></ion-icon></span><span>User</span></a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <header>
            <h2>Drum Tips</h2>
            <div class="sear">
                <span class="ti-search"></span>
                <input type="search" placeholder="Search">
            </div>
            <div class="social-icons">
                <span class="ti-bell"></span>
                <span class="ti-comment"></span>
            </div>
        </header>

        <div class="tips-container">
            <?php 
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { 
            ?>
                <div class="tip-section">
                    <div class="tip-header">
                        <img src="<?php echo $row['avatar_url'] ?? 'images/default-avatar.png'; ?>" alt="User Avatar" class="avatar">
                        <div class="user-info">
                            <h3 class="user-name"><?php echo htmlspecialchars($row['username']); ?></h3>
                            <span class="shared-time"><?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?></span>
                        </div>
                    </div>
                    <div class="tip-body">
                        <p><?php echo nl2br(htmlspecialchars($row['tip_content'])); ?></p>

                        <?php if ($row['media_type'] == 'image' && !empty($row['media_path'])) { ?>
                            <img src="<?php echo htmlspecialchars($row['media_path']); ?>" alt="Tip Image" class="tip-media" style="max-width:100%; height:auto;">
                        <?php } elseif ($row['media_type'] == 'video' && !empty($row['media_path'])) { ?>
                            <video controls class="tip-media" style="max-width:100%;">
                                <source src="<?php echo htmlspecialchars($row['media_path']); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php } ?>
                    </div>
                    <div class="tip-footer">
                        <button class="view-reviews-btn" data-tip-id="<?php echo $row['tip_id']; ?>">View Reviews</button>
                        <button class="review-btn" data-tip-id="<?php echo $row['tip_id']; ?>">Review</button>
                    </div>
                    <div class="reviews-section" id="reviews-<?php echo $row['tip_id']; ?>">
                        <h4>Reviews</h4>
                        <div class="reviews-content"></div>
                    </div>
                </div>
            <?php 
                }
            } else {
                echo '<p>No tips available for this category.</p>';
            }
            ?>
        </div>
    </div>

    <!-- Modal for adding reviews -->
    <div class="modal" id="review-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Add Your Review</h3>
            <form class="review-form" id="review-form">
                <input type="hidden" id="tip-id" name="tip_id">
                <textarea name="review_content" rows="4" placeholder="Write your review here..." required></textarea>
                <button type="submit" class="submit-review">Submit Review</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('review-modal');
            const closeModal = document.querySelector('.close-modal');
            
            // View Reviews functionality
            document.querySelectorAll('.view-reviews-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const tipId = this.getAttribute('data-tip-id');
                    const reviewsSection = document.querySelector(`#reviews-${tipId}`);
                    
                    if (reviewsSection.style.display === 'none' || !reviewsSection.style.display) {
                        fetch('vocal.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `get_reviews=1&tip_id=${tipId}`
                        })
                        .then(response => response.json())
                        .then(reviews => {
                            const reviewsContent = reviewsSection.querySelector('.reviews-content');
                            reviewsContent.innerHTML = '';
                            
                            reviews.forEach(review => {
                                const reviewElement = `
                                    <div class="review-item">
                                        <img src="${review.avatar_url || 'images/default-avatar.png'}" alt="Reviewer Avatar" class="review-avatar">
                                        <div class="review-content-wrapper">
                                            <div class="review-header">
                                                <span class="review-author">${review.username}</span>
                                                <span class="review-date">${new Date(review.created_at).toLocaleString()}</span>
                                            </div>
                                            <div class="review-content">${review.review_content}</div>
                                        </div>
                                    </div>
                                `;
                                reviewsContent.innerHTML += reviewElement;
                            });
                            
                            reviewsSection.style.display = 'block';
                            button.textContent = 'Hide Reviews';
                        });
                    } else {
                        reviewsSection.style.display = 'none';
                        button.textContent = 'View Reviews';
                    }
                });
            });

            // Review button click handler
            document.querySelectorAll('.review-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const tipId = this.getAttribute('data-tip-id');
                    document.getElementById('tip-id').value = tipId;
                    modal.style.display = 'block';
                });
            });

            // Close modal
            closeModal.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            // Close modal when clicking outside
            window.addEventListener('click', (event) => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });

            // Handle review submission
            document.getElementById('review-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                fetch('add_review.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        modal.style.display = 'none';
                        // Refresh the reviews section
                        const tipId = document.getElementById('tip-id').value;
                        document.querySelector(`[data-tip-id="${tipId}"].view-reviews-btn`).click();
                    } else {
                        alert('Error adding review. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding review. Please try again.');
                });
            });
        });
    </script>
</body>
</html>