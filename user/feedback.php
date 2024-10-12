<?php
session_start();
include('../home/table.php'); // Adjust the path as needed

// Check if the user is logged in
if (!isset($_SESSION['signup_id'])) {
    header("Location: ../home/testhome.php");
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['signup_id'];
    $feedback_text = $_POST['feedback_text'] ?? '';

    if (empty($feedback_text)) {
        $error_message = 'Feedback text is required';
    } else {
        // Prepare and execute the SQL statement
        $sql = "INSERT INTO feedback (user_id, feedback_text) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $feedback_text);

        if ($stmt->execute()) {
            $success_message = 'Thank you for your feedback!';
        } else {
            $error_message = 'Error inserting feedback: ' . $conn->error;
        }

        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            height: 100px;
            resize: vertical;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        input[type="submit"], button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        button {
            background-color: #f44336;
            color: white;
        }
        button:hover {
            background-color: #d32f2f;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Feedback Form</h1>
        <?php
        if (!empty($success_message)) {
            echo "<div class='message success'>$success_message</div>";
            echo "<script>
                setTimeout(function() {
                    window.history.back();
                }, 2000);
            </script>";
        } elseif (!empty($error_message)) {
            echo "<div class='message error'>$error_message</div>";
        }
        ?>
        <form method="post">
            <label for="feedback_text">Your Feedback:</label>
            <textarea id="feedback_text" name="feedback_text" required></textarea>

            <div class="button-container">
                <button type="button" onclick="window.history.back();">Back</button>
                <input type="submit" value="Submit Feedback">
            </div>
        </form>
    </div>

    <script>
        <?php if (!empty($success_message)): ?>
        setTimeout(function() {
            window.history.back();
        }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>