<?php
session_start();
include('../home/table.php');

if (!isset($_SESSION['signup_id'])) {
    header("Location: ../home/testhome.php");
    exit();
}

$admin_id = $_SESSION['signup_id'];
$success_message = '';
$error_message = '';

// Fetch current admin data
$stmt = $conn->prepare("SELECT * FROM admin WHERE signup_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin_data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_name = trim($_POST['admin_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($admin_name) || empty($email)) {
        $error_message = "Name and email are required fields.";
    } else {
        $conn->begin_transaction();

        try {
            // Check if password change is requested
            if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
                // All password fields must be filled if any are filled
                if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                    throw new Exception("All password fields must be filled to change password");
                }

                // Verify current password - FIXED VERSION
                $verify_pwd = $conn->prepare("SELECT password FROM admin WHERE signup_id = ?");
                $verify_pwd->bind_param("i", $admin_id);
                $verify_pwd->execute();
                $pwd_result = $verify_pwd->get_result();
                $row = $pwd_result->fetch_assoc();
                
                if (!$row || !password_verify($current_password, $row['password'])) {
                    throw new Exception("Current password is incorrect");
                }

                if ($new_password !== $confirm_password) {
                    throw new Exception("New passwords do not match");
                }

                if (strlen($new_password) < 6) {
                    throw new Exception("New password must be at least 6 characters long");
                }

                // Update password in both tables
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                
                $update_admin_password = $conn->prepare("UPDATE admin SET password = ? WHERE signup_id = ?");
                $update_admin_password->bind_param("si", $new_hash, $admin_id);
                if (!$update_admin_password->execute()) {
                    throw new Exception("Error updating admin password");
                }
                
                $update_signup_password = $conn->prepare("UPDATE sign_up SET password = ? WHERE signup_id = ?");
                $update_signup_password->bind_param("si", $new_hash, $admin_id);
                if (!$update_signup_password->execute()) {
                    throw new Exception("Error updating signup password");
                }
            }

            // Check if email already exists for another user
            $check_email = $conn->prepare("SELECT signup_id FROM admin WHERE email = ? AND signup_id != ?");
            $check_email->bind_param("si", $email, $admin_id);
            $check_email->execute();
            if ($check_email->get_result()->num_rows > 0) {
                throw new Exception("Email already exists");
            }

            // Check if username already exists for another user
            $check_username = $conn->prepare("SELECT signup_id FROM admin WHERE admin_name = ? AND signup_id != ?");
            $check_username->bind_param("si", $admin_name, $admin_id);
            $check_username->execute();
            if ($check_username->get_result()->num_rows > 0) {
                throw new Exception("Username already exists");
            }

            // Update admin table
            $update_admin = $conn->prepare("UPDATE admin SET admin_name = ?, email = ? WHERE signup_id = ?");
            $update_admin->bind_param("ssi", $admin_name, $email, $admin_id);
            $update_admin->execute();
            
            // Update sign_up table
            $update_signup = $conn->prepare("UPDATE sign_up SET user_name = ?, email = ? WHERE signup_id = ?");
            $update_signup->bind_param("ssi", $admin_name, $email, $admin_id);
            $update_signup->execute();

            $conn->commit();
            $success_message = "Profile updated successfully!";
            
            // Refresh admin data
            $stmt->execute();
            $admin_data = $stmt->get_result()->fetch_assoc();
            
            echo "<script>var redirectFlag = true;</script>";

        } catch (Exception $e) {
            $conn->rollback();
            $error_message = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin Profile</title>
    <link rel="icon" type="image/png" href="../image/indexnbg.png">
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
        .password-section { border-top: 1px solid #374151; margin-top: 2rem; padding-top: 1rem; }
        @media (max-width: 640px) { .tip-form-container { margin: 1rem; } }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <br><br>
    <div class="tip-form-container">
        <div class="form-header">
            <h1 class="form-title">Edit Admin Profile</h1>
        </div>
        
        <div class="form-content">
            <?php if ($error_message): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="admin_name" class="form-label">Admin Username:</label>
                    <input type="text" id="admin_name" name="admin_name" 
                           value="<?= htmlspecialchars($admin_data['admin_name'] ?? '') ?>" 
                           required class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($admin_data['email'] ?? '') ?>" 
                           required class="form-input">
                </div>

                <div class="password-section">
                    <h2 class="form-title">Change Password</h2>
                    <div class="form-group">
                        <label for="current_password" class="form-label">Current Password:</label>
                        <input type="password" id="current_password" name="current_password" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password" class="form-label">New Password:</label>
                        <input type="password" id="new_password" name="new_password" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm New Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input">
                    </div>
                </div>
                
                <div class="button-group">
                    <a href="admine.php" class="cancel-btn"><i class="fas fa-times"></i> Cancel</a>
                    <button type="submit" class="submit-btn"><i class="fas fa-check"></i> Save Changes</button>
                </div>
            </form>
            
            <div class="preview">
                <h2>Current Information</h2>
                <p><strong>Admin Username:</strong> <?= htmlspecialchars($admin_data['admin_name'] ?? 'N/A') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($admin_data['email'] ?? 'N/A') ?></p>
            </div>
        </div>
    </div>

    <script>
    if (typeof redirectFlag !== 'undefined' && redirectFlag) {
        setTimeout(function() {
            window.location.href = 'admine.php';
        }, 2000); // Redirect after 2 seconds
    }
    </script>
</body>
</html>