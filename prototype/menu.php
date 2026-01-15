<?php

session_start();

// Check if user is logged in
// If not authenticated, redirect to homepage
if(!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}

// Include navigation bar
include("view/top.php");
?>

<!-- Include custom CSS for dashboard styling -->
<link rel="stylesheet" href="css/menu.css">

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h1>Dashboard</h1>
                    
                    <!-- User Information  -->
                    <div class="alert alert-success mt-4">
                        <h4>Στοιχεία Χρήστη</h4>
                        <!-- Display username from session -->
                        <p><strong>Όνομα:</strong> <?php echo htmlspecialchars($_SESSION["username"]); ?></p>
                        <!-- Display email from session -->
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION["email"]); ?></p>
                        <!-- Display role from session -->
                        <p><strong>Ρόλος:</strong> <?php echo htmlspecialchars($_SESSION["role_name"]); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($_SESSION["role_id"] == 1): ?>
    <!--  STUDENT DASHBOARD  -->
    <div class="row mt-4">
        <div class="col-12">
            <h3>Περιοχή Φοιτητή</h3>
        </div>
        
        <!-- My Courses Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Τα Μαθήματά μου</h5>
                    <p>Δείτε τα μαθήματα στα οποία είστε εγγεγραμμένοι</p>
                    <a href="student/courses.php" class="btn btn-primary">Προβολή Μαθημάτων</a>
                </div>
            </div>
        </div>
        
        <!-- My Grades Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Οι Βαθμοί μου</h5>
                    <p>Δείτε τους βαθμούς και τα σχόλια σας</p>
                    <a href="student/grades.php" class="btn btn-success">Προβολή Βαθμών</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!--  PROFESSOR DASHBOARD  -->
    <div class="row mt-4">
        <div class="col-12">
            <h3>Περιοχή Καθηγητή</h3>
        </div>
       
        <!-- Manage Courses Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h5>Τα Μαθήματά μου</h5>
                    <p>Διαχειριστείτε τα μαθήματά σας</p>
                    <a href="professor/courses.php" class="btn btn-primary">Διαχείριση Μαθημάτων</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php 
// Include footer
include("view/bottom.php"); 
?>
