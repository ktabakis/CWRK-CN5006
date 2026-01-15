<?php
session_start();

// Include database configuration
include("config.php");

// If user is already logged in, redirect to menu
if(isset($_SESSION["username"])) {
    header("Location:menu.php");
    exit();
}

// Include navigation bar
include("view/top.php");

// Include login form HTML
include("view/login.html");

// Check if form was submitted (POST request)
if(isset($_POST["email"])) {
    // Sanitize form inputs to prevent SQL injection
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $pwd = $_POST["pwd"];
    
    // Query database for user with provided email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    // Check if user exists
    if(mysqli_num_rows($result) > 0) {
        // User found - retrieve user data
        $row = mysqli_fetch_assoc($result);
        
        // Verify password using bcrypt
        if(password_verify($pwd, $row['password'])) {
            // Password correct - create session
            
            // Store user information in session variables
            $_SESSION["username"] = $row["username"];
            $_SESSION["email"] = $row["email"];
            $_SESSION["role_id"] = $row["role_id"];
            $_SESSION["user_id"] = $row["user_id"];
            
            // Set role name for display purposes
            if ($row["role_id"] == 1) {
                $_SESSION["role_name"] = "Student";
            } else {
                $_SESSION["role_name"] = "Professor";
            }
            
            // Close database connection
            mysqli_close($conn);
            
            // Redirect to dashboard
            header("Location:menu.php");
            exit();
        } else {
            // Incorrect password
            echo "<div class='container mt-3'><div class='alert alert-danger'>Λάθος κωδικός!</div></div>";
        }
    } else {
        // Email not found in database
        echo "<div class='container mt-3'><div class='alert alert-danger'>Το email δεν βρέθηκε!</div></div>";
    }
    
    // Close database connection
    mysqli_close($conn);
}

// Include footer
include("view/bottom.php");
?>
