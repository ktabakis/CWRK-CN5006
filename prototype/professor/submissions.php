<?php

session_start();

// Check if user is logged in and is a professor
if(!isset($_SESSION["username"]) || $_SESSION["role_id"] != 2) {
    header("Location: ../index.php");
    exit();
}

include("../config.php");

$professor_id = $_SESSION["user_id"];
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Verify course belongs to professor
$verify_sql = "SELECT * FROM courses WHERE course_id = $course_id AND professor_id = $professor_id";
$verify_result = mysqli_query($conn, $verify_sql);

if(mysqli_num_rows($verify_result) == 0) {
    die("Forbidden: Δεν έχετε δικαίωμα πρόσβασης.");
}

$course = mysqli_fetch_assoc($verify_result);

// Get all submissions for this course
$sql = "SELECT s.*, a.title as assignment_title, a.due_date,
               u.username as student_name,
               g.grade_id, g.grade, g.feedback
        FROM submissions s
        JOIN assignments a ON s.assignment_id = a.assignment_id
        JOIN users u ON s.student_id = u.user_id
        LEFT JOIN grades g ON s.submission_id = g.submission_id
        WHERE a.course_id = $course_id
        ORDER BY s.submitted_at DESC";

$submissions_result = mysqli_query($conn, $sql);

include("../view/top.php");
?>

<link rel="stylesheet" href="../css/menu.css">

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h2><i class="fas fa-file-alt"></i> Υποβολές Φοιτητών</h2>
        </div>
        <div class="card-body">
            
            <!-- Course Info -->
            <div class="alert alert-primary">
                <h5><?php echo htmlspecialchars($course['course_code']) . ' - ' . htmlspecialchars($course['course_name']); ?></h5>
            </div>
            
            <?php if(mysqli_num_rows($submissions_result) > 0): ?>
                
                <p class="text-muted">
                    Σύνολο Υποβολών: <strong><?php echo mysqli_num_rows($submissions_result); ?></strong>
                </p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Εργασία</th>
                                <th>Φοιτητής</th>
                                <th>Ημερομηνία Υποβολής</th>
                                <th>Βαθμός</th>
                                <th>Ενέργειες</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($submission = mysqli_fetch_assoc($submissions_result)): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($submission['assignment_title']); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            Προθεσμία: <?php echo date('d/m/Y', strtotime($submission['due_date'])); ?>
                                        </small>
                                    </td>
                                    <td><?php echo htmlspecialchars($submission['student_name']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($submission['submitted_at'])); ?></td>
                                    <td>
                                        <?php if($submission['grade_id']): ?>
                                            <span class="badge badge-success">
                                                <?php echo $submission['grade']; ?>/100
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Μη βαθμολογημένο</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="grade.php?submission_id=<?php echo $submission['submission_id']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> 
                                            <?php echo $submission['grade_id'] ? 'Επεξεργασία' : 'Βαθμολόγηση'; ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php else: ?>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Δεν υπάρχουν υποβολές για αυτό το μάθημα ακόμα.
                </div>
                
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="courses.php" class="btn btn-secondary">
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
