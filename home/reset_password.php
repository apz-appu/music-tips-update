<?php
// Start session
session_start();
include('table.php');

$error = $success = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Use prepared statement to verify token in sign_up table
    $stmt = $conn->prepare("SELECT * FROM sign_up WHERE reset_token = ? AND user_type = 'user'");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Fetch user details
        $user = $result->fetch_assoc();
        
        // Store the user's email in session to ensure security
        $_SESSION['reset_email'] = $user['email'];
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
            $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
            
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Use prepared statement to update sign_up table
                $stmt = $conn->prepare("UPDATE sign_up SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE email = ?");
                $stmt->bind_param("ss", $hashed_password, $_SESSION['reset_email']);
                $stmt->execute();
                
                // Use prepared statement to update user table
                $stmt = $conn->prepare("UPDATE user SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE email = ?");
                $stmt->bind_param("ss", $hashed_password, $_SESSION['reset_email']);
                $stmt->execute();
                
                $success = "Password updated successfully. You can now login with your new password.";
            } else {
                $error = "Passwords do not match.";
            }
        }
    } else {
        $error = "Invalid or expired reset token.";
    }
} else {
    $error = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password </title>
    <link rel="icon" type="image/png" href="../image/indexnbg.png">
    <style>
       
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .forgot-password-container {
            background-color: black;
            padding: 2rem;
            border-radius: 20px;
            border:2px solid rgba( 255, 255, 255,.5);
            box-shadow: 0 10px 30px rgba(20, 204, 255, 0.976);
            width: 300px;
        }
        h2 {
            color: rgb(122, 189, 213);
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .input-box {
            position: relative;
            margin-bottom: 1.5rem;
            color:rgb(122, 189, 213);
        }
        .input-box input {
            width: 100%;
            padding: 10px 0;
            font-size: 16px;
            color: rgb(122, 189, 213);
            border: none;
            border-bottom: 1px solid #333;
            outline: none;
            background: transparent;
        }
        .input-box label {
            position: absolute;
            top: 0;
            left: 0;
            padding: 10px 0;
            font-size: 16px;
            color: rgb(122, 189, 213);
            pointer-events: none;
            transition: 0.5s;
        }
        .input-box input:focus ~ label,
        .input-box input:valid ~ label {
            top: -20px;
            left: 0;
            color: #03a9f4;
            font-size: 12px;
        }
        .btn {
            width: 100%;
            background-color: #03a9f4;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #0288d1;
        }
        p {
            text-align: center;
            margin-top: 1rem;
        }
        a {
            color: #03a9f4;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .error {
            color: #f44336;
            text-align: center;
            margin-bottom: 1rem;
        }
        .success {
            color: #4CAF50;
            text-align: center;
            margin-bottom: 1rem;
        }
        .reset-link {
            word-break: break-all;
            background-color: #e0e0e0;
            padding: 10px;
            border-radius: 4px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <h2>Reset Password</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php else: ?>
            <form method="post">
                <div class="input-box">
                    <input type="password" name="new_password" required>
                    <label>New Password</label>
                </div>
                <div class="input-box">
                    <input type="password" name="confirm_password" required>
                    <label>Confirm New Password</label>
                </div>
                <button type="submit" class="btn">Reset Password</button>
            </form>
        <?php endif; ?>
        <p><a href="testhome.php">Back to Login</a></p>
    </div>
</body>
</html>
