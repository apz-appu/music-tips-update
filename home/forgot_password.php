<?php
// Start the session
session_start();
include('table.php');
$error = $success = $reset_link = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if the email exists in sign_up table
    $sql = "SELECT * FROM sign_up WHERE email = '$email' AND user_type = 'user'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(16)); // 32 characters long
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Update sign_up table
        $sql = "UPDATE sign_up SET reset_token = '$token', reset_token_expires = '$expires' WHERE email = '$email'";
        mysqli_query($conn, $sql);
        
        // Update user table
        $sql = "UPDATE user SET reset_token = '$token', reset_token_expires = '$expires' WHERE email = '$email'";
        mysqli_query($conn, $sql);
        
        // Store email in session for future use
        $_SESSION['reset_email'] = $email;
        
        // Generate reset link
        $reset_link = "http://localhost/php/home/reset_password.php?token=" . $token;
        $success = "Password reset link generated successfully. Redirecting...";
    } else {
        $error = "No user account found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <h2>Forgot Password</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
            <script>
                // Redirect to the reset link after 3 seconds
                setTimeout(function() {
                    window.location.href = "<?php echo $reset_link; ?>";
                }, 3000);
            </script>
        <?php endif; ?>
        <?php if (!$success): ?>
            <form method="post" id="forgotForm" name="forg" onclick="return validateforgot()">
                <div class="input-box">
                    <input type="email" name="email">
                    <label>Email</label>
                </div>
                <button type="submit" class="btn">Generate Reset Link</button>
            </form>
        <?php endif; ?>
        <p><a href="testhome.php">Back to Login</a></p>
    </div>
</body>
<script src="Js/scriptlog.js"></script> 
</html>
