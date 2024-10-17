<?php
session_start();
include('../home/table.php');

if (!isset($_SESSION['signup_id'])) {
    header("Location: ../home/testhome.php");
    exit();
}

$user_id = $_SESSION['signup_id'];
$success_message = '';
$error_message = '';

$stmt = $conn->prepare("SELECT user_name, email, phone FROM sign_up WHERE signup_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$ur = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    if (empty($user_name) || empty($email) || empty($phone)) {
        $error_message = "All fields are required.";
    } else {
        $conn->begin_transaction();

        try {
            // Update sign_up table
            $update_signup = $conn->prepare("UPDATE sign_up SET user_name = ?, email = ?, phone = ? WHERE signup_id = ?");
            $update_signup->bind_param("sssi", $user_name, $email, $phone, $user_id);
            $update_signup->execute();
        
            // Update user table
            $update_user = $conn->prepare("UPDATE user SET username = ?, email = ?, phone = ? WHERE signup_id = ?");
            $update_user->bind_param("sssi", $user_name, $email, $phone, $user_id);
            $update_user->execute();
            $success_message = "Profile updated successfully!";
        
            // Commit transaction
            $conn->commit();
            echo "<script>var redirectFlag = true;</script>";
            // ... success handling ...
        } catch (Exception $e) {
            // An error occurred, rollback changes
            $conn->rollback();
            $error_message = "Error updating profile: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .min-h-screen { min-height: 80vh; }
        .bg-gray-100 { background-color: black; }
        .tip-form-container { max-width: 48rem; margin: 10px auto; background: black; border-radius: 1.3rem; box-shadow: 0 10px 30px rgba(20, 204, 255, 0.976); overflow: hidden; }
        .form-header { padding: 1.5rem; border-bottom: 1px solid black; }
        .form-title { font-size: 1.5rem; font-weight: 700; color: #22dbdf; text-align: center; }
        .form-content { padding: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 500; color: #22dbdf; margin-bottom: 0.5rem; }
        .form-input { width: 100%; padding: 0.5rem; border: 1px solid #89fdff; border-radius: 0.375rem; background-color: #89fdff; font-size: 1rem; color: #374151; }
        .button-group { display: flex; justify-content: space-between; margin-top: 1.5rem; }
        .submit-btn, .cancel-btn { padding: 0.75rem 1.5rem; color: white; border: none; border-radius: 0.375rem; font-size: 1rem; font-weight: 500; cursor: pointer; transition: background-color 0.3s ease; display: flex; align-items: center; }
        .submit-btn { background-color: #22c55e; }
        .submit-btn:hover { background-color: #16a34a; }
        .cancel-btn { background-color: #565f5a; }
        .cancel-btn:hover { background-color: rgb(154, 85, 85); }
        .error-message { background-color: #fee2e2; color: #dc2626; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem; }
        .success-message { background-color: #dcfce7; color: #16a34a; padding: 0.75rem; border-radius: 0.375rem; margin-bottom: 1rem; }
        .preview { background-color: #1f2937; color: #d1d5db; padding: 1rem; border-radius: 0.375rem; margin-top: 1.5rem; }
        .preview h2 { color: #22dbdf; }
        @media (max-width: 640px) { .tip-form-container { margin: 1rem; } }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <br><br>
    <div class="tip-form-container">
        <div class="form-header">
            <h1 class="form-title">Edit Profile</h1>
        </div>
        
        <div class="form-content">
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="user_name" class="form-label">Username:</label>
                    <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($ur['user_name']); ?>" required class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($ur['email']); ?>" required class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">Phone:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($ur['phone']); ?>" required class="form-input">
                </div>
                
                <div class="button-group">
                    <a href="usere.php" class="cancel-btn"><i class="fas fa-times"></i> Cancel</a>
                    <button type="submit" class="submit-btn"><i class="fas fa-check"></i> Submit</button>
                </div>
            </form>
            
            <div class="preview">
                <h2>Preview</h2>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($ur['user_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($ur['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($ur['phone']); ?></p>
            </div>
        </div>
    </div>

    <script>
    if (typeof redirectFlag !== 'undefined' && redirectFlag) {
        setTimeout(function() {
            window.location.href = 'usere.php';
        }, 3000); // Redirect after 3 seconds
    }
    </script>
</body>
</html>