<?php
session_start(); // Start the session

// Unset all session variables
session_unset();  

unset($user_data);
unset($stmt);
unset($tips_stmt);

// Destroy the session on the server
session_destroy(); 

// Redirect the user to the home page
header("Location: ../home/testhome.php"); 
exit();
?>
