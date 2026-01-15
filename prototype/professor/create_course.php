<?php
session_start();

// Check if user is logged in and is a professor
if(!isset($_SESSION["username"]) || $_SESSION["role_id"] != 2) {
    header("Location: ../index.php");
    exit();
}

include("../config.php");

$professor_id = $_SESSION["user_id"];

// Handle form submission
if(isset($_POST['create_course'])) {
    $course_code = mysqli_real_escape_string($conn, $_POST['course_code']);
    $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $academic_year = mysqli_real_escape_string($conn, $_POST['academic_year']);
    
    // Validation
    if(empty($course_code) || empty($course_name) || empty($semester) || empty($academic_year)) {
        $error = "Παρακαλώ συμπληρώστε όλα τα υποχρεωτικά πεδία.";
    } else {
        // Check if course code already exists
        $check_sql = "SELECT * FROM courses WHERE course_code = '$course_code'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if(mysqli_num_rows($check_result) > 0) {
            $error = "Ο κωδικός μαθήματος υπάρχει ήδη!";
        } else {
            $insert_sql = "INSERT INTO courses (course_code, course_name, description, professor_id, semester, academic_year) 
                          VALUES ('$course_code', '$course_name', '$description', $professor_id, '$semester', '$academic_year')";
            
            if(mysqli_query($conn, $insert_sql)) {
                $success = "Το μάθημα δημιουργήθηκε επιτυχώς!";
                // Redirect after 2 seconds
                header("refresh:2;url=courses.php");
            } else {
                $error = "Σφάλμα: " . mysqli_error($conn);
            }
        }
    }
}

include("../view/top.php");
?>

<link rel="stylesheet" href="../css/menu.css">

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h2><i class="fas fa-plus-circle"></i> Δημιουργία Νέου Μαθήματος</h2>
        </div>
        <div class="card-body">
            
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
            
            <form method="POST" class="needs-validation" novalidate>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="course_code"><strong>Κωδικός Μαθήματος:</strong> <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="course_code" 
                                   name="course_code" 
                                   required
                                   placeholder="π.χ. CN5006"
                                   value="<?php echo isset($_POST['course_code']) ? htmlspecialchars($_POST['course_code']) : ''; ?>">
                            <small class="form-text text-muted">Μοναδικός κωδικός μαθήματος</small>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="course_name"><strong>Τίτλος Μαθήματος:</strong> <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="course_name" 
                                   name="course_name" 
                                   required
                                   placeholder="π.χ. Web & Mobile App Development"
                                   value="<?php echo isset($_POST['course_name']) ? htmlspecialchars($_POST['course_name']) : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description"><strong>Περιγραφή Μαθήματος:</strong></label>
                    <textarea class="form-control" 
                              id="description" 
                              name="description" 
                              rows="4"
                              placeholder="Προαιρετική περιγραφή του μαθήματος..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="semester"><strong>Εξάμηνο:</strong> <span class="text-danger">*</span></label>
                            <select class="form-control" id="semester" name="semester" required>
                                <option value="">Επιλέξτε...</option>
                                <option value="Fall" <?php echo (isset($_POST['semester']) && $_POST['semester'] == 'Fall') ? 'selected' : ''; ?>>Χειμερινό (Fall)</option>
                                <option value="Spring" <?php echo (isset($_POST['semester']) && $_POST['semester'] == 'Spring') ? 'selected' : ''; ?>>Εαρινό (Spring)</option>
                                <option value="Summer" <?php echo (isset($_POST['semester']) && $_POST['semester'] == 'Summer') ? 'selected' : ''; ?>>Θερινό (Summer)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="academic_year"><strong>Ακαδημαϊκό Έτος:</strong> <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="academic_year" 
                                   name="academic_year" 
                                   required
                                   placeholder="π.χ. 2024-2025"
                                   value="<?php echo isset($_POST['academic_year']) ? htmlspecialchars($_POST['academic_year']) : '2024-2025'; ?>">
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <button type="submit" name="create_course" class="btn btn-success">
                    <i class="fas fa-save"></i> Δημιουργία Μαθήματος
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
