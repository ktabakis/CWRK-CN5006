<?php
session_start();

// Check if user is logged in and is a professor
if(!isset($_SESSION["username"]) || $_SESSION["role_id"] != 2) {
    header("Location: ../index.php");
    exit();
}

// Check if course_id is provided
if(!isset($_GET['course_id'])) {
    header("Location: courses.php");
    exit();
}

include("../config.php");

$course_id = intval($_GET['course_id']);
$professor_id = $_SESSION["user_id"];

// Verify that this course belongs to this professor
$verify_sql = "SELECT * FROM courses WHERE course_id = $course_id AND professor_id = $professor_id";
$verify_result = mysqli_query($conn, $verify_sql);

if(mysqli_num_rows($verify_result) == 0) {
    die("Forbidden: Δεν έχετε δικαίωμα πρόσβασης σε αυτό το μάθημα.");
}

$course = mysqli_fetch_assoc($verify_result);

// Handle form submission
if(isset($_POST['create_assignment'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    
    if(empty($title) || empty($due_date)) {
        $error = "Παρακαλώ συμπληρώστε όλα τα υποχρεωτικά πεδία.";
    } else {
        $insert_sql = "INSERT INTO assignments (course_id, title, description, due_date) 
                      VALUES ($course_id, '$title', '$description', '$due_date')";
        
        if(mysqli_query($conn, $insert_sql)) {
            $success = "Η εργασία δημιουργήθηκε επιτυχώς!";
            header("refresh:2;url=courses.php");
        } else {
            $error = "Σφάλμα: " . mysqli_error($conn);
        }
    }
}

include("../view/top.php");
?>

<link rel="stylesheet" href="../css/menu.css">

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h2><i class="fas fa-plus-circle"></i> Δημιουργία Νέας Εργασίας</h2>
        </div>
        <div class="card-body">
            
            <!-- Course Info -->
            <div class="alert alert-info">
                <strong>Μάθημα:</strong> 
                <?php echo htmlspecialchars($course['course_code']) . ' - ' . htmlspecialchars($course['course_name']); ?>
            </div>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <br>Ανακατεύθυνση...
                </div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                
                <div class="form-group">
                    <label for="title"><strong>Τίτλος Εργασίας:</strong> <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control" 
                           id="title" 
                           name="title" 
                           required
                           placeholder="π.χ. Assignment 1: Binary Trees"
                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="description"><strong>Περιγραφή Εργασίας:</strong></label>
                    <textarea class="form-control" 
                              id="description" 
                              name="description" 
                              rows="6"
                              placeholder="Περιγράψτε τις απαιτήσεις της εργασίας..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    <small class="form-text text-muted">Προαιρετικό: Οδηγίες και απαιτήσεις για τους φοιτητές</small>
                </div>
                
                <div class="form-group">
                    <label for="due_date"><strong>Προθεσμία Υποβολής:</strong> <span class="text-danger">*</span></label>
                    <input type="datetime-local" 
                           class="form-control" 
                           id="due_date" 
                           name="due_date" 
                           required
                           value="<?php echo isset($_POST['due_date']) ? htmlspecialchars($_POST['due_date']) : ''; ?>">
                    <small class="form-text text-muted">Επιλέξτε ημερομηνία και ώρα λήξης</small>
                </div>
                
                <hr>
                
                <button type="submit" name="create_assignment" class="btn btn-success">
                    <i class="fas fa-save"></i> Δημιουργία Εργασίας
                </button>
                
                <a href="courses.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Ακύρωση
                </a>
                
            </form>
            
        </div>
    </div>
</div>

<?php 
mysqli_close($conn);
include("../view/bottom.php"); 
?>
