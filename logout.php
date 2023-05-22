<?php
// logout.php
// Includes the configuration file
include "includes/config.php";

// Starts the user session
session_start();

// Clears and destroys the user session
session_unset();
session_destroy();

// Redirects to the login page
header("Location: index.php");
exit();
?>