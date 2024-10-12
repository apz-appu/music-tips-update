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

$success_message = '';
$error_message = '';

// Form submission processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $review_content = mysqli_real_escape_string($conn, $_POST['review_content']);
    $user_id = $_SESSION['user_id'];

    // Validate input
    if (empty($review_content)) {
        $error_message = "Review content cannot be empty";
    } else {
        // Insert review into database
        $sql = "INSERT INTO reviews (tip_id, user_id, review_content) 
                VALUES (?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $tip_id, $user_id, $review_content);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Review submitted successfully!";
            // Redirect back to the previous page after successful submission
            header("Location: " . $previous_page);
            exit();
        } else {
            $error_message = "Error submitting review: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    <style>
        .review-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 150px;
            resize: vertical;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        .alert {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .alert-error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        .cancel-btn {
            background-color: #6c757d;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .cancel-btn:hover {
            background-color: #5a6268;
        }

        .button-group {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="review-form">
        <h2>Write Your Review</h2>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?tip_id=" . $tip_id; ?>">
            <div class="form-group">
                <label for="review_content">Your Review:</label>
                <textarea 
                    name="review_content" 
                    id="review_content" 
                    required 
                    placeholder="Write your review here..."></textarea>
            </div>

            <div class="button-group">
                <a href="<?php echo $previous_page; ?>" class="cancel-btn">Cancel</a>
                <button type="submit" class="submit-btn">Submit Review</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
