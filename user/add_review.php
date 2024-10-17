<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['signup_id'])) {
    header("Location: ../home/testhome.php");
    exit();
}

// Database connection
include('../home/table.php');

// Get tip_id from URL
$tip_id = isset($_GET['tip_id']) ? (int)$_GET['tip_id'] : 0;

// Validate tip_id
if ($tip_id <= 0) {
    die("Invalid tip ID");
}

// Capture the referring page URL
$previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'view_tip.php?tip_id=' . $tip_id;

// Handle AJAX form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax_submit'])) {
    $review_content = mysqli_real_escape_string($conn, $_POST['review_content']);
    $signup_id = $_SESSION['signup_id'];
    
    // Fetch user_id based on signup_id
    $user_query = "SELECT user_id FROM user WHERE signup_id = ?";
    $stmt = mysqli_prepare($conn, $user_query);
    mysqli_stmt_bind_param($stmt, "i", $signup_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['user_id'];
    mysqli_stmt_close($stmt);

    // Validate input
    if (empty($review_content)) {
        echo json_encode(['success' => false, 'message' => 'Review content cannot be empty']);
        exit();
    }

    // Insert review into database
    $sql = "INSERT INTO reviews (tip_id, user_id, review_content) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $tip_id, $user_id, $review_content);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Review submitted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error submitting review: ' . mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .review-form {
            max-width: 600px;
            width: 100%;
            background-color: black;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(20, 204, 255, 0.976);
            padding: 30px;
        }

        h2 {
            color: #22dbdf;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #22dbdf;
        }

        textarea {
            color: #22dbdf;
            background-color: #000;
            width: 100%;
            padding: 12px;
            border: 1px solid #22dbdf;
            border-radius: 4px;
            min-height: 150px;
            resize: vertical;
            font-family: Arial, sans-serif;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        textarea:focus {
            border-color: #22dbdf;
            outline: none;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.1s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .cancel-btn {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
        }

        .cancel-btn:hover {
            background-color: #5a6268;
        }

        .submit-btn {
            background-color: #22dbdf;
            color: black;
        }

        .submit-btn:hover {
            background-color: #1fa8ab;
        }

        .btn i {
            margin-right: 8px;
        }

        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: black;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 10px 30px rgba(20, 204, 255, 0.976);
            z-index: 1000;
            display: none;
        }

        .popup-content {
            text-align: center;
        }

        .popup-message {
            margin-bottom: 20px;
            font-size: 18px;
            color: #22dbdf;
        }

        .popup-close {
            display: inline-block;
            padding: 10px 20px;
            background-color: #22dbdf;
            color: black;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .popup-close:hover {
            background-color: #1fa8ab;
        }

        .error-message {
            color: #ff4136;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        .input-error {
            border-color: #ff4136 !important;
        }
    </style>
</head>
<body>
    <div class="review-form">
        <h2>Write Your Review</h2>
        
        <form id="reviewForm" method="POST">
            <div class="form-group">
                <label for="review_content">Your Review:</label>
                <textarea 
                    name="review_content" 
                    id="review_content" 
                    placeholder="Write your review here..."></textarea>
                <div id="reviewError" class="error-message">Please enter your review.</div>
            </div>

            <div class="button-group">
                <a href="<?php echo $previous_page; ?>" class="btn cancel-btn">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn submit-btn">
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>
            </div>
        </form>
    </div>

    <div id="popup" class="popup">
        <div class="popup-content">
            <p class="popup-message" id="popupMessage"></p>
            <a href="#" class="popup-close" id="popupClose">OK</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function validateForm() {
                var reviewContent = $('#review_content').val().trim();
                if (reviewContent === '') {
                    $('#review_content').addClass('input-error');
                    $('#reviewError').show();
                    return false;
                } else {
                    $('#review_content').removeClass('input-error');
                    $('#reviewError').hide();
                    return true;
                }
            }

            $('#reviewForm').on('submit', function(e) {
                e.preventDefault();
                
                if (validateForm()) {
                    $.ajax({
                        url: '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?tip_id=" . $tip_id; ?>',
                        type: 'POST',
                        data: $(this).serialize() + '&ajax_submit=1',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $('#popupMessage').text(response.message);
                                $('#popup').show();
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function() {
                            alert('An error occurred. Please try again.');
                        }
                    });
                }
            });

            $('#review_content').on('input', function() {
                if ($(this).val().trim() !== '') {
                    $(this).removeClass('input-error');
                    $('#reviewError').hide();
                }
            });

            $('#popupClose').on('click', function(e) {
                e.preventDefault();
                $('#popup').hide();
                window.location.href = '<?php echo $previous_page; ?>';
            });
        });
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>