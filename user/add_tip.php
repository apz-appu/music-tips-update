<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['signup_id'])) {
    header("Location: ../home/testhome.php");
    exit();
}

include('../home/table.php');

// Get user_id from the user table based on email
$email = $_SESSION['email'];
$user_query = "SELECT user_id FROM user WHERE email = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['user_id'];

// Fetch categories for dropdown
$category_query = "SELECT * FROM category";
$categories = $conn->query($category_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['category_id'];
    $tip_content = $_POST['tip_content'];
    $media_type = null;
    $media_path = null;

    // Check if a file was uploaded
    if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
        $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif'];
        $allowed_video_types = ['video/mp4', 'video/webm', 'video/ogg'];
        
        $file_type = $_FILES['media']['type'];
        $file_size = $_FILES['media']['size'];
        $file_name = $_FILES['media']['name'];
        $file_tmp = $_FILES['media']['tmp_name'];
        
        // Generate unique filename
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $unique_filename = uniqid() . '.' . $extension;
        
        // Determine media type and set upload directory
        if (in_array($file_type, $allowed_image_types)) {
            $media_type = 'image';
            $upload_dir = '../uploads/images/';
        } elseif (in_array($file_type, $allowed_video_types)) {
            $media_type = 'video';
            $upload_dir = '../uploads/videos/';
        } else {
            $_SESSION['error'] = "Invalid file type. Please upload an image (JPEG, PNG, GIF) or video (MP4, WebM, OGG).";
            header("Location: add_tip.php");
            exit();
        }

        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Move uploaded file
        if (move_uploaded_file($file_tmp, $upload_dir . $unique_filename)) {
            $media_path = $upload_dir . $unique_filename;
        } else {
            $_SESSION['error'] = "Failed to upload file.";
            header("Location: add_tip.php");
            exit();
        }
    }

    // Insert tip into database
    $insert_query = "INSERT INTO tips (user_id, category_id, tip_content, media_type, media_path) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iisss", $user_id, $category_id, $tip_content, $media_type, $media_path);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Tip added successfully!";
        header("Location: user_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding tip: " . $conn->error;
        header("Location: add_tip.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tip</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="css/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .min-h-screen {
            min-height: 100vh;
        }

        .bg-gray-100 {
            background-color: black;
        }

        .tip-form-container {
            max-width: 48rem;
            margin: .5rem auto;
            background: black;
            border-radius: 0.5rem;
            box-shadow:  0 10px 30px rgba(20, 204, 255, 0.976);
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

        .form-content {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #22dbdf;
            margin-bottom: 0.5rem;
        }

        .form-select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #89fdff;
            border-radius: 0.375rem;
            background-color: ##89fdff;
            font-size: 1rem;
            color: #374151;
        }

        .form-textarea {
            width: 100%;
            min-height: 150px;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: #89fdff;
            font-size: 1rem;
            color: #374151;
            resize: vertical;
        }

        .upload-area {
            border: 2px dashed #22dbdf;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            border-color: #22dbdf;
            background-color: #22dbdf;
        }

        .upload-icon {
            font-size: 2rem;
            color: #9ca3af;
            margin-bottom: 0.5rem;
        }

        .upload-text {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .upload-subtext {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .media-preview {
            max-width: 300px;
            margin-top: 1rem;
            border-radius: 0.5rem;
            overflow: hidden;
            display: none;
        }

        .submit-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #22c55e;
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #16a34a;
        }

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

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .tip-form-container {
                margin: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="main-content">
        <div class="tip-form-container">
            <div class="form-header">
                <h2 class="form-title">Add a New Tip</h2>
            </div>
            
            <div class="form-content">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="error-message">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="success-message">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <form action="add_tip.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label" for="category_id">Category:</label>
                        <select class="form-select" name="category_id" id="category_id" required>
                            <option value="">Select a category</option>
                            <?php while($category = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $category['category_id']; ?>">
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="tip_content">Tip Content:</label>
                        <textarea class="form-textarea" name="tip_content" id="tip_content" required 
                                placeholder="Share your musical tip here..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Add Media (Optional):</label>
                        <div class="upload-area" onclick="document.getElementById('media').click()">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <p class="upload-text">Drag and drop or click to upload</p>
                            <p class="upload-subtext">Supports images and videos</p>
                            <input type="file" name="media" id="media" accept="image/*,video/*" style="display: none;">
                        </div>
                        <div id="media-preview" class="media-preview"></div>
                    </div>

                    <button type="submit" class="submit-btn">Submit Tip</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Preview uploaded media
        document.getElementById('media').addEventListener('change', function(e) {
            const preview = document.getElementById('media-preview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.style.display = 'block';
                    if (file.type.startsWith('image/')) {
                        preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; border-radius: 0.5rem;">`;
                    } else if (file.type.startsWith('video/')) {
                        preview.innerHTML = `<video controls style="max-width: 100%; border-radius: 0.5rem;">
                            <source src="${e.target.result}" type="${file.type}">
                        </video>`;
                    }
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                preview.innerHTML = '';
            }
        });

        // Drag and drop functionality
        const uploadArea = document.querySelector('.upload-area');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            uploadArea.style.backgroundColor = '#f9fafb';
            uploadArea.style.borderColor = '#9ca3af';
        }

        function unhighlight(e) {
            uploadArea.style.backgroundColor = '';
            uploadArea.style.borderColor = '#d1d5db';
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const file = dt.files[0];
            
            const input = document.getElementById('media');
            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
        }
    </script>
</body>
</html>