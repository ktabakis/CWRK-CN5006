<?php
session_start();

// Check if user is logged in and is a student
if(!isset($_SESSION["username"]) || $_SESSION["role_id"] != 1) {
    header("Location: ../index.php");
    exit();
}

// Check if assignment_id is provided
if(!isset($_GET['assignment_id'])) {
    header("Location: courses.php");
    exit();
}

include("../config.php");

$assignment_id = intval($_GET['assignment_id']);
$student_id = $_SESSION["user_id"];

// Get assignment details
$sql = "SELECT a.*, c.course_name, c.course_code 
        FROM assignments a 
        JOIN courses c ON a.course_id = c.course_id 
        WHERE a.assignment_id = $assignment_id";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0) {
    header("Location: courses.php");
    exit();
}

$assignment = mysqli_fetch_assoc($result);

// Check if already submitted
$check_sql = "SELECT * FROM submissions WHERE assignment_id = $assignment_id AND student_id = $student_id";
$check_result = mysqli_query($conn, $check_sql);
$already_submitted = mysqli_num_rows($check_result) > 0;

// Handle form submission
if(isset($_POST['submit_assignment'])) {
    if($already_submitted) {
        $error = "Έχετε ήδη υποβάλει αυτή την εργασία!";
    } else {
        $submission_text = mysqli_real_escape_string($conn, $_POST['submission_text']);
        
        if(empty($submission_text)) {
            $error = "Παρακαλώ εισάγετε το κείμενο της εργασίας σας.";
        } else {
            $insert_sql = "INSERT INTO submissions (assignment_id, student_id, submission_text, status) 
                          VALUES ($assignment_id, $student_id, '$submission_text', 'submitted')";
            
            if(mysqli_query($conn, $insert_sql)) {
                // Redirect για να φορτώσει τη σελίδα από την αρχή με το νέο submission
                header("Location: submit.php?assignment_id=" . $assignment_id . "&success=1");
                exit();
            } else {
                $error = "Σφάλμα κατά την υποβολή: " . mysqli_error($conn);
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
            <h2><i class="fas fa-upload"></i> Υποβολή Εργασίας</h2>
        </div>
        <div class="card-body">
            
            <!-- Assignment Details -->
            <div class="alert alert-info">
                <h5><?php echo htmlspecialchars($assignment['title']); ?></h5>
                <p><strong>Μάθημα:</strong> <?php echo htmlspecialchars($assignment['course_code']) . ' - ' . htmlspecialchars($assignment['course_name']); ?></p>
                <p><strong>Προθεσμία:</strong> <?php echo date('d/m/Y H:i', strtotime($assignment['due_date'])); ?></p>
                <?php if($assignment['description']): ?>
                    <hr>
                    <p><strong>Περιγραφή:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
                <?php endif; ?>
            </div>
            
            <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Η εργασία σας υποβλήθηκε επιτυχώς!
                </div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($already_submitted): ?>
                
                <!-- Show existing submission -->
                <?php
                $submission = mysqli_fetch_assoc($check_result);
                ?>
                <div class="alert alert-warning">
                    <h5><i class="fas fa-info-circle"></i> Έχετε ήδη υποβάλει αυτή την εργασία</h5>
                    <p><strong>Ημερομηνία υποβολής:</strong> <?php echo date('d/m/Y H:i', strtotime($submission['submitted_at'])); ?></p>
                    <p><strong>Κατάσταση:</strong> <?php echo htmlspecialchars($submission['status']); ?></p>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h6>Η Υποβολή σας:</h6>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($submission['submission_text'])); ?></p>
                    </div>
                </div>
                
            <?php else: ?>
                
                <!-- Submission Form -->
                <form method="POST" class="mt-3">
                    <div class="form-group">
                        <label for="submission_text"><strong>Κείμενο Εργασίας:</strong></label>
                        <textarea class="form-control" 
                                  id="submission_text" 
                                  name="submission_text" 
                                  rows="10" 
                                  required
                                  placeholder="Εισάγετε το κείμενο της εργασίας σας εδώ..."></textarea>
                        <small class="form-text text-muted">
                            Σημείωση: Μπορείτε να επικολλήσετε το κείμενό σας ή να το γράψετε απευθείας εδώ.
                        </small>
                    </div>
                    
                    <button type="submit" name="submit_assignment" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Υποβολή Εργασίας
                    </button>
                    
                    <a href="courses.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Ακύρωση
                    </a>
                </form>
                
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="courses.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Επιστροφή στα Μαθήματα
                </a>
            </div>
            
        </div>
    </div>
</div>

<?php 
mysqli_close($conn);
include("../view/bottom.php"); 
?>