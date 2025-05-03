<?php
session_start();

// Destroy the session to log the user out
session_unset();  // Clears all session variables
session_destroy();  // Destroys the session

// Redirect to the login page
header('Location: index.php');
exit();
?>
