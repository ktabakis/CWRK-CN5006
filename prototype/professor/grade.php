<?php
session_start();

// Check if user is logged in and is a professor
if(!isset($_SESSION["username"]) || $_SESSION["role_id"] != 2) {
    header("Location: ../index.php");
    exit();
}

// Check if submission_id is provided
if(!isset($_GET['submission_id'])) {
    header("Location: courses.php");
    exit();
}

include("../config.php");

$submission_id = intval($_GET['submission_id']);
$professor_id = $_SESSION["user_id"];

// Get submission details with verification
$sql = "SELECT s.*, 
               a.title as assignment_title, a.description as assignment_description, a.due_date,
               c.course_id, c.course_name, c.course_code, c.professor_id,
               u.username as student_name,
               g.grade_id, g.grade, g.feedback
        FROM submissions s
        JOIN assignments a ON s.assignment_id = a.assignment_id
        JOIN courses c ON a.course_id = c.course_id
        JOIN users u ON s.student_id = u.user_id
        LEFT JOIN grades g ON s.submission_id = g.submission_id
        WHERE s.submission_id = $submission_id AND c.professor_id = $professor_id";

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0) {
    die("Forbidden: Δεν έχετε δικαίωμα πρόσβασης.");
}

$submission = mysqli_fetch_assoc($result);

// Handle grading form submission
if(isset($_POST['grade_submission'])) {
    $grade = floatval($_POST['grade']);
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
    
    // Validate grade
    if($grade < 0 || $grade > 100) {
        $error = "Ο βαθμός πρέπει να είναι μεταξύ 0 και 100.";
    } else {
        // Check if grade already exists
        if($submission['grade_id']) {
            // Update existing grade
            $update_sql = "UPDATE grades 
                          SET grade = $grade, 
                              feedback = '$feedback', 
                              graded_at = CURRENT_TIMESTAMP,
                              graded_by = $professor_id
                          WHERE grade_id = " . $submission['grade_id'];
            
            if(mysqli_query($conn, $update_sql)) {
                $success = "Ο βαθμός ενημερώθηκε επιτυχώς!";
                // Refresh data
                $result = mysqli_query($conn, $sql);
                $submission = mysqli_fetch_assoc($result);
            } else {
                $error = "Σφάλμα: " . mysqli_error($conn);
            }
        } else {
            // Insert new grade
            $insert_sql = "INSERT INTO grades (submission_id, grade, feedback, graded_by) 
                          VALUES ($submission_id, $grade, '$feedback', $professor_id)";
            
            if(mysqli_query($conn, $insert_sql)) {
                $success = "Ο βαθμός καταχωρήθηκε επιτυχώς!";
                // Refresh data
                $result = mysqli_query($conn, $sql);
                $submission = mysqli_fetch_assoc($result);
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
        <div class="card-header bg-warning">
            <h2><i class="fas fa-edit"></i> Βαθμολόγηση Υποβολής</h2>
        </div>
        <div class="card-body">
            
            <!-- Assignment & Course Info -->
            <div class="alert alert-primary">
                <h5>
                    <span class="badge badge-secondary"><?php echo htmlspecialchars($submission['course_code']); ?></span>
                    <?php echo htmlspecialchars($submission['assignment_title']); ?>
                </h5>
                <p class="mb-0">
                    <strong>Μάθημα:</strong> <?php echo htmlspecialchars($submission['course_name']); ?> | 
                    <strong>Φοιτητής:</strong> <?php echo htmlspecialchars($submission['student_name']); ?>
                </p>
            </div>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Submission Details -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6><i class="fas fa-info-circle"></i> Λεπτομέρειες Υποβολής</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Υποβλήθηκε:</strong> <?php echo date('d/m/Y H:i', strtotime($submission['submitted_at'])); ?></p>
                            <p><strong>Προθεσμία:</strong> <?php echo date('d/m/Y H:i', strtotime($submission['due_date'])); ?></p>
                            <p><strong>Κατάσταση:</strong> 
                                <?php
                                $late = strtotime($submission['submitted_at']) > strtotime($submission['due_date']);
                                if($late) {
                                    echo '<span class="badge badge-warning">Εκπρόθεσμη</span>';
                                } else {
                                    echo '<span class="badge badge-success">Εγκαίρως</span>';
                                }
                                ?>
                            </p>
                            
                            <?php if($submission['assignment_description']): ?>
                                <hr>
                                <p><strong>Περιγραφή Εργασίας:</strong></p>
                                <p class="text-muted"><?php echo nl2br(htmlspecialchars($submission['assignment_description'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Student Submission Text -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6><i class="fas fa-file-alt"></i> Υποβολή Φοιτητή</h6>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <?php echo nl2br(htmlspecialchars($submission['submission_text'])); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Grading Form -->
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning">
                            <h6><i class="fas fa-star"></i> Βαθμολόγηση</h6>
                        </div>
                        <div class="card-body">
                            
                            <form method="POST">
                                
                                <div class="form-group">
                                    <label for="grade"><strong>Βαθμός (0-100):</strong> <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control form-control-lg" 
                                           id="grade" 
                                           name="grade" 
                                           min="0" 
                                           max="100" 
                                           step="0.01"
                                           required
                                           value="<?php echo $submission['grade'] ?? ''; ?>"
                                           style="font-size: 2rem; text-align: center;">
                                    <small class="form-text text-muted">Εισάγετε βαθμό από 0 έως 100</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="feedback"><strong>Σχόλια / Feedback:</strong></label>
                                    <textarea class="form-control" 
                                              id="feedback" 
                                              name="feedback" 
                                              rows="8"
                                              placeholder="Προσθέστε τα σχόλιά σας για τον φοιτητή..."><?php echo $submission['feedback'] ?? ''; ?></textarea>
                                    <small class="form-text text-muted">Προαιρετικό: Παρατηρήσεις και συμβουλές</small>
                                </div>
                                
                                <hr>
                                
                                <button type="submit" name="grade_submission" class="btn btn-success btn-lg btn-block">
                                    <i class="fas fa-save"></i> 
                                    <?php echo $submission['grade_id'] ? 'Ενημέρωση Βαθμού' : 'Καταχώρηση Βαθμού'; ?>
                                </button>
                                
                            </form>
                            
                            <?php if($submission['grade_id']): ?>
                                <div class="alert alert-info mt-3">
                                    <small>
                                        <strong>Τελευταία ενημέρωση:</strong> 
                                        <?php 
                                        $grade_info = mysqli_fetch_assoc(mysqli_query($conn, 
                                            "SELECT graded_at FROM grades WHERE grade_id = " . $submission['grade_id']));
                                        echo date('d/m/Y H:i', strtotime($grade_info['graded_at'])); 
                                        ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="submissions.php?course_id=<?php echo $submission['course_id']; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Επιστροφή στις Υποβολές
                </a>
                <a href="courses.php" class="btn btn-primary">
                    <i class="fas fa-chalkboard-teacher"></i> Τα Μαθήματά μου
                </a>
            </div>
            
        </div>
    </div>
</div>

<?php 
mysqli_close($conn);
include("../view/bottom.php"); 
?>
