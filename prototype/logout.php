<?php

session_start();

// Destroy all session data (logs out the user)
session_destroy();

// Redirect to homepage
header("Location:index.php");
exit();
?>
