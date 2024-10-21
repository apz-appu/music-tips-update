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
$redirect_flag = false;

// Handle news submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $content = trim($_POST['content'] ?? '');
        $media_type = $_FILES['media']['type'] ?? '';
        $media_path = '';

        if (empty($content)) {
            $error_message = "News content is required.";
        } else {
            // Handle file upload
            if (!empty($_FILES['media']['name'])) {
                $allowed_types = ['image/jpeg', 'image/png', 'video/mp4'];
                if (in_array($media_type, $allowed_types)) {
                    $upload_dir = 'uploads/';
                    $media_path = $upload_dir . basename($_FILES['media']['name']);
                    move_uploaded_file($_FILES['media']['tmp_name'], $media_path);
                } else {
                    $error_message = "Only JPG, PNG images and MP4 videos are allowed.";
                }
            }

            // Get admin_id from admin table using signup_id
            $admin_stmt = $conn->prepare("SELECT admin_id FROM admin WHERE signup_id = ?");
            $admin_stmt->bind_param("i", $admin_id);
            $admin_stmt->execute();
            $admin_result = $admin_stmt->get_result();
            $admin_row = $admin_result->fetch_assoc();

            if ($admin_row) {
                $actual_admin_id = $admin_row['admin_id'];

                // Insert news with optional media path
                $insert_stmt = $conn->prepare("INSERT INTO add_news (admin_id, content, media_path, added_at) VALUES (?, ?, ?, NOW())");
                $insert_stmt->bind_param("iss", $actual_admin_id, $content, $media_path);

                if ($insert_stmt->execute()) {
                    $success_message = "News added successfully!";
                    $redirect_flag = true;
                } else {
                    $error_message = "Failed to add news.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add News</title>
    <link rel="icon" type="image/png" href="../image/indexnbg.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .min-h-screen { min-height: 80vh; }
        .bg-gray-100 { background-color: black; }
        .tip-form-container { 
            max-width: 48rem; 
            margin: 10px auto; 
            background: black; 
            border-radius: 1.3rem; 
            box-shadow: 0 10px 30px rgba(20, 204, 255, 0.976); 
            overflow: hidden; 
        }
        .form-header { 
            padding: 1.5rem; 
            border-bottom: 1px solid black; 
        }
        .form-title { 
            font-size: 1.5rem; 
            font-weight: 700; 
            color: #22dbdf; 
            text-align: center; 
        }
        .form-content { padding: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-label { 
            display: block; 
            font-size: 0.875rem; 
            font-weight: 500; 
            color: #22dbdf; 
            margin-bottom: 0.5rem; 
        }
        .form-input, .form-textarea { 
            width: 100%; 
            padding: 0.5rem; 
            border: 1px solid #89fdff; 
            border-radius: 0.375rem; 
            background-color: #89fdff; 
            font-size: 1rem; 
            color: #374151; 
        }
        .form-textarea {
            min-height: 150px;
            resize: vertical;
        }
        .button-group { 
            display: flex; 
            justify-content: space-between; 
            margin-top: 1.5rem; 
        }
        .submit-btn, .cancel-btn { 
            padding: 0.75rem 1.5rem; 
            color: white; 
            border: none; 
            border-radius: 0.375rem; 
            font-size: 1rem; 
            font-weight: 500; 
            cursor: pointer; 
            transition: background-color 0.3s ease; 
            display: flex; 
            align-items: center; 
            text-decoration: none;
        }
        .submit-btn { background-color: #22c55e; }
        .submit-btn:hover { background-color: #16a34a; }
        .cancel-btn { background-color: #565f5a; }
        .cancel-btn:hover { background-color: rgb(154, 85, 85); }
        .error-message { 
            background-color: #fee2e2; 
            color: #dc2626; 
            padding: 0.75rem; 
            border-radius: 0.375rem; 
            margin-bottom: 1rem; 
        }
        .success-message { 
            background-color: #dcfce7; 
            color: #16a34a; 
            padding: 0.75rem; 
            border-radius: 0.375rem; 
            margin-bottom: 1rem; 
        }
        .file-upload-info {
            color: #22dbdf;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        @media (max-width: 640px) { 
            .tip-form-container { margin: 1rem; } 
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <br><br>
    <div class="tip-form-container">
        <div class="form-header">
            <h1 class="form-title">Add News</h1>
        </div>
        
        <div class="form-content">
            <?php if ($error_message): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="content" class="form-label">News Content:</label>
                    <textarea id="content" name="content" required class="form-textarea"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="media" class="form-label">Upload Image/Video:</label>
                    <input type="file" id="media" name="media" class="form-input">
                    <p class="file-upload-info">Allowed formats: JPG, PNG, MP4</p>
                </div>
                
                <div class="button-group">
                    <a href="admine.php" class="cancel-btn">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-plus"></i> Add News
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    <?php if ($redirect_flag): ?>
        setTimeout(function() {
            window.location.href = 'add_news.php';
        }, 2000);
    <?php endif; ?>
    </script>
</body>
</html>