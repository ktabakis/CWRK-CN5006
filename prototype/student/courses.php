<?php

session_start();

// Check if user is logged in and is a student
if(!isset($_SESSION["username"]) || $_SESSION["role_id"] != 1) {
    header("Location: ../index.php");
    exit();
}

include("../config.php");
include("../view/top.php");

// Get student's enrolled courses with professor info
$student_id = $_SESSION["user_id"];

// For demo purposes, we'll show all available courses
// In a real system, there would be an enrollment table
$sql = "SELECT c.*, u.username as professor_name 
        FROM courses c 
        JOIN users u ON c.professor_id = u.user_id 
        ORDER BY c.created_at DESC";

$courses_result = mysqli_query($conn, $sql);
?>

<link rel="stylesheet" href="../css/menu.css">

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h2><i class="fas fa-book"></i> Τα Μαθήματά μου</h2>
        </div>
        <div class="card-body">
            <p class="text-muted">Εδώ μπορείτε να δείτε τα μαθήματα στα οποία είστε εγγεγραμμένοι και τις εργασίες τους.</p>
            
            <?php if(mysqli_num_rows($courses_result) > 0): ?>
                
                <div class="row mt-4">
                    <?php while($course = mysqli_fetch_assoc($courses_result)): ?>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card border-primary">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <span class="badge badge-primary"><?php echo htmlspecialchars($course['course_code']); ?></span>
                                        <?php echo htmlspecialchars($course['course_name']); ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Course Info -->
                                    <p><strong>Καθηγητής:</strong> <?php echo htmlspecialchars($course['professor_name']); ?></p>
                                    <p><strong>Εξάμηνο:</strong> <?php echo htmlspecialchars($course['semester']); ?></p>
                                    <p><strong>Ακαδημαϊκό Έτος:</strong> <?php echo htmlspecialchars($course['academic_year']); ?></p>
                                    
                                    <?php if($course['description']): ?>
                                        <p class="text-muted"><em><?php echo htmlspecialchars($course['description']); ?></em></p>
                                    <?php endif; ?>
                                    
                                    <hr>
                                    
                                    <!-- Get assignments for this course -->
                                    <?php
                                    $course_id = $course['course_id'];
                                    $assignments_sql = "SELECT * FROM assignments WHERE course_id = $course_id ORDER BY due_date ASC";
                                    $assignments_result = mysqli_query($conn, $assignments_sql);
                                    ?>
                                    
                                    <h6><i class="fas fa-tasks"></i> Εργασίες (<?php echo mysqli_num_rows($assignments_result); ?>):</h6>
                                    
                                    <?php if(mysqli_num_rows($assignments_result) > 0): ?>
                                        <ul class="list-group mt-2">
                                            <?php while($assignment = mysqli_fetch_assoc($assignments_result)): ?>
                                                <?php
                                                // Check if student has submitted this assignment
                                                $assignment_id = $assignment['assignment_id'];
                                                $check_sql = "SELECT * FROM submissions WHERE assignment_id = $assignment_id AND student_id = $student_id";
                                                $check_result = mysqli_query($conn, $check_sql);
                                                $has_submitted = mysqli_num_rows($check_result) > 0;
                                                ?>
                                                
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($assignment['title']); ?></strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar"></i> 
                                                                Προθεσμία: <?php echo date('d/m/Y H:i', strtotime($assignment['due_date'])); ?>
                                                            </small>
                                                        </div>
                                                        <div>
                                                            <?php if($has_submitted): ?>
                                                                <span class="badge badge-success">Υποβλήθηκε</span>
                                                            <?php else: ?>
                                                                <a href="submit.php?assignment_id=<?php echo $assignment_id; ?>" 
                                                                   class="btn btn-sm btn-primary">
                                                                    Υποβολή
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted"><em>Δεν υπάρχουν εργασίες για αυτό το μάθημα.</em></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                    <?php endwhile; ?>
                </div>
                
            <?php else: ?>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> 
                    Δεν υπάρχουν διαθέσιμα μαθήματα αυτή τη στιγμή.
                </div>
                
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="../menu.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Επιστροφή στον Πίνακα Ελέγχου
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
mysqli_close($conn);
include("../view/bottom.php"); 
?>
