<?php
session_start();
session_unset(); 
session_destroy();

// Redirect to home page or login page
header('Location: ../home/testhome.php');
exit();
?>
