<?php

session_start();

// Include database configuration
include("config.php");

// Define registration codes for security
$student_code = "STUD2025";   // Registration code for students
$professor_code = "PROF2025";  // Registration code for professors

// If user is already logged in, redirect to menu
if(isset($_SESSION["username"])) {
    header("Location:menu.php");
    exit();
}

// Include navigation bar
include("view/top.php");

// Include registration form HTML
include("view/signup.html");

// Check if form was submitted (POST request)
if(isset($_POST["uname"])) {
    // Sanitize and retrieve form data
    $uname = mysqli_real_escape_string($conn, $_POST["uname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    
    // Hash password for security (using bcrypt algorithm)
    $pwd = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
    
    // Get registration code
    $regcode = $_POST["regcode"];
    
    // Validate registration code and assign role
    $role_id = 0;
    if($regcode == $student_code) {
        // Student registration
        $role_id = 1;
    } else if($regcode == $professor_code) {
        // Professor registration
        $role_id = 2;
    } else {
        // Invalid registration code
        echo "<div class='container mt-3'><div class='alert alert-danger'>Λάθος κωδικός εγγραφής!</div></div>";
        mysqli_close($conn);
        include("view/bottom.php");
        exit;
    }
    
    // Check if email already exists in database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        // Email already registered
        echo "<div class='container mt-3'><div class='alert alert-danger'>Το email υπάρχει ήδη!</div></div>";
    } else {
        // Insert new user into database
        $sql = "INSERT INTO users (username, email, password, role_id) VALUES ('$uname', '$email', '$pwd', $role_id)";
        
        if(mysqli_query($conn, $sql)) {
            // Registration successful - create session and log user in
            
            // Get the newly created user ID
            $user_id = mysqli_insert_id($conn);
            
            // Store user information in session
            $_SESSION["username"] = $uname;
            $_SESSION["email"] = $email;
            $_SESSION["role_id"] = $role_id;
            $_SESSION["user_id"] = $user_id;
            
            // Set role name for display
            if($role_id == 1) {
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
            // Database error during insertion
            echo "<div class='container mt-3'><div class='alert alert-danger'>Σφάλμα: " . mysqli_error($conn) . "</div></div>";
        }
    }
    
    // Close database connection
    mysqli_close($conn);
}

// Include footer
include("view/bottom.php");
?>
