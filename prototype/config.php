<?php

// Database connection parameters
$servername = "127.0.0.1";  // Database server address (localhost)
$username = "root";          // Database username
$password = "";          // Database password
$dbname = "university_system"; // Database name

// Create connection to MySQL database
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check if connection was successful
if (!$conn) {
    // If connection fails, terminate script and display error
    die("Connection failed: " . mysqli_connect_error());
}

// Set character encoding to UTF-8 for proper Greek language support
mysqli_set_charset($conn, "utf8mb4");
?>
