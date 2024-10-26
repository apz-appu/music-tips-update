<?php
$servername = "localhost";
$username = "root";
$password = "";

// connection check
$conn = mysqli_connect($servername, $username, $password);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// db creation
$sql = "CREATE DATABASE IF NOT EXISTS mydb";
if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating database: " . mysqli_error($conn);
}

// table-1 sign_up
mysqli_select_db($conn, "mydb");
$sql = "CREATE TABLE IF NOT EXISTS sign_up (
    signup_id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
    user_name VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type VARCHAR(10) NOT NULL DEFAULT 'user',
    signup_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    is_admin BOOLEAN DEFAULT FALSE,
    reset_token VARCHAR(65),
    reset_token_expires DATETIME
)";
if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating sign_up table: " . mysqli_error($conn);
}

// table-2 log_in
$sql = "CREATE TABLE IF NOT EXISTS log_in (
    login_id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
    email VARCHAR(100) NOT NULL,
    login_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
)";
if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating log_in table: " . mysqli_error($conn);
}

// table-3 admin
$sql = "CREATE TABLE IF NOT EXISTS admin(
    admin_id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
    signup_id INT(11) UNSIGNED NOT NULL,
    admin_name VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    added_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
)";
if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating admin table: " . mysqli_error($conn);
}

// table-4 user
$sql = "CREATE TABLE IF NOT EXISTS user(
    user_id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
    signup_id INT(11) UNSIGNED NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    added_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    reset_token VARCHAR(65),
    reset_token_expires DATETIME
)";
if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating user table: " . mysqli_error($conn);
}

// table-5 category
$sql = "CREATE TABLE IF NOT EXISTS category(
    category_id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
    category_name VARCHAR(100) NOT NULL
)";
if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating category table: " . mysqli_error($conn);
}

// Insert if not exists into category
$categories = ['Vocal', 'Guitar', 'Drum', 'Keyboard'];
foreach ($categories as $category) {
    $insert_sql = "INSERT INTO category (category_name) 
                   SELECT * FROM (SELECT '$category') AS tmp
                   WHERE NOT EXISTS (
                       SELECT category_name FROM category WHERE category_name = '$category'
                   ) LIMIT 1";

    if (mysqli_query($conn, $insert_sql)) {
        echo "";
    } else {
        echo "<br>Error inserting category: " . mysqli_error($conn);
    }
}

// table-6 tips
$sql = "CREATE TABLE IF NOT EXISTS tips(
    tip_id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
    user_id INT(11) UNSIGNED NOT NULL,
    category_id INT(11) UNSIGNED NOT NULL,
    tip_content TEXT NOT NULL,
    media_type ENUM('image', 'video') DEFAULT NULL,
    media_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    FOREIGN KEY (category_id) REFERENCES category(category_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
)";
if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating tips table: " . mysqli_error($conn);
}

// table-7 feedback
$sql = "CREATE TABLE IF NOT EXISTS feedback(
    feedback_id INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
    user_id INT(11) UNSIGNED NOT NULL,
    feedback_text TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
)";
if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating feedback table: " . mysqli_error($conn);
}

// Table-8 reviews
$sql = "CREATE TABLE IF NOT EXISTS reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    tip_id INT,
    user_id INT,
    review_content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tip_id) REFERENCES tips(tip_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
)";
if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating review table: " . mysqli_error($conn);
}

//Table-9 news by admin
$sql = "CREATE TABLE IF NOT EXISTS add_news (
    news_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    content TEXT NOT NULL,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    media_path VARCHAR(255), 
    media_type ENUM('image', 'video'),  
    FOREIGN KEY (admin_id) REFERENCES admin(admin_id) ON DELETE CASCADE
)";


if ($conn->query($sql) === TRUE) {
    echo "";
} else {
    echo "Error creating table: " . $conn->error;
}

// Create trigger for admin insert into sign_up
$sql = "
CREATE TRIGGER IF NOT EXISTS after_admin_insert
AFTER INSERT ON admin
FOR EACH ROW
BEGIN
    INSERT INTO sign_up (user_name, email, password, user_type, signup_time, is_admin)
    VALUES (NEW.admin_name, NEW.email, NEW.password, 'admin', NOW(), 1);
END;
";

if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating admin insert trigger: " . mysqli_error($conn);
}

// Create trigger for admin delete from sign_up
$sql = "
CREATE TRIGGER IF NOT EXISTS after_admin_delete
AFTER DELETE ON admin
FOR EACH ROW
BEGIN
    DELETE FROM sign_up WHERE email = OLD.email;
END;
";

if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating admin delete trigger: " . mysqli_error($conn);
}

// Create trigger for adding user to user table
$sql = "
CREATE TRIGGER IF NOT EXISTS after_sign_up_insert
AFTER INSERT ON sign_up
FOR EACH ROW
BEGIN
    IF NEW.user_type = 'user' THEN
        INSERT INTO user (username, email, phone, password, added_at, signup_id)
        VALUES (NEW.user_name, NEW.email, NEW.phone, NEW.password, NOW(), NEW.signup_id);
    END IF;
END;
";

if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating user insert trigger: " . mysqli_error($conn);
}

// Create trigger for removing user from user table
$sql = "
CREATE TRIGGER IF NOT EXISTS after_user_delete
AFTER DELETE ON user
FOR EACH ROW
BEGIN
    DELETE FROM sign_up WHERE email = OLD.email;
END;
";

if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating user delete trigger: " . mysqli_error($conn);
}

// Create trigger for cleaning up user data before deletion
$sql = "
CREATE TRIGGER IF NOT EXISTS before_user_delete
BEFORE DELETE ON user
FOR EACH ROW
BEGIN
    -- Delete all reviews associated with user's tips
    DELETE FROM reviews 
    WHERE tip_id IN (SELECT tip_id FROM tips WHERE user_id = OLD.user_id);
    
    -- Delete all reviews made by the user
    DELETE FROM reviews 
    WHERE user_id = OLD.user_id;
    
    -- Delete all tips created by the user
    DELETE FROM tips 
    WHERE user_id = OLD.user_id;
    
    -- Delete all feedback given by the user
    DELETE FROM feedback 
    WHERE user_id = OLD.user_id;
END;
";

if (mysqli_query($conn, $sql)) {
    echo "";
} else {
    echo "<br>Error creating user cleanup trigger: " . mysqli_error($conn);
}
?>