<?php
// verification.php
session_start();
include('table.php');

$error = "";

// Check if email and token exist in session
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_token'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $verification_code = $_POST['verification_code'];
    $email = $_SESSION['reset_email'];
    
    // Verify the code
    $stmt = $conn->prepare("SELECT * FROM sign_up WHERE email = ? AND verification_code = ? AND reset_token_expires > NOW()");
    $stmt->bind_param("ss", $email, $verification_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $reset_token = $_SESSION['reset_token'];
        header("Location: reset_password.php?token=" . $reset_token);
        exit();
    } else {
        $error = "Invalid or expired verification code";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
    <link rel="icon" type="image/png" href="../image/indexnbg.png">
    <style>
        /* Use the same styling as your forgot_password.php */
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .verification-container {
            background-color: black;
            padding: 2rem;
            border-radius: 20px;
            border: 2px solid rgba(255, 255, 255, .5);
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
            color: rgb(122, 189, 213);
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
            text-align: center;
            letter-spacing: 0.5em;
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
        .error {
            color: #f44336;
            text-align: center;
            margin-bottom: 1rem;
        }
        p {
            color: rgb(122, 189, 213);
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <h2>Verify Your Email</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <p>Please enter the verification code sent to your email</p>
        <form method="post">
            <div class="input-box">
                <input type="text" name="verification_code" maxlength="6" required>
            </div>
            <button type="submit" class="btn">Verify Code</button>
        </form>
    </div>
</body>
</html>