<?php
session_start();

// Check if user is logged in and is a professor
if(!isset($_SESSION["username"]) || $_SESSION["role_id"] != 2) {
    header("Location: ../index.php");
    exit();
}

include("../config.php");

$professor_id = $_SESSION["user_id"];

// Get professor's courses
$sql = "SELECT * FROM courses WHERE professor_id = $professor_id ORDER BY created_at DESC";
$courses_result = mysqli_query($conn, $sql);

include("../view/top.php");
?>

<link rel="stylesheet" href="../css/menu.css">

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-chalkboard-teacher"></i> Τα Μαθήματά μου</h2>
                <a href="create_course.php" class="btn btn-light">
                    <i class="fas fa-plus"></i> Νέο Μάθημα
                </a>
            </div>
        </div>
        <div class="card-body">
            <p class="text-muted">Διαχειριστείτε τα μαθήματά σας, τις εργασίες και τις υποβολές των φοιτητών.</p>
            
            <?php if(mysqli_num_rows($courses_result) > 0): ?>
                
                <div class="row mt-4">
                    <?php while($course = mysqli_fetch_assoc($courses_result)): ?>
                        <?php
                        $course_id = $course['course_id'];
                        
                        // Count assignments
                        $count_assignments = mysqli_query($conn, "SELECT COUNT(*) as total FROM assignments WHERE course_id = $course_id");
                        $assignments_count = mysqli_fetch_assoc($count_assignments)['total'];
                        
                        // Count submissions
                        $count_submissions = mysqli_query($conn, 
                            "SELECT COUNT(*) as total FROM submissions s 
                             JOIN assignments a ON s.assignment_id = a.assignment_id 
                             WHERE a.course_id = $course_id");
                        $submissions_count = mysqli_fetch_assoc($count_submissions)['total'];
                        ?>
                        
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
                                    <p><strong>Εξάμηνο:</strong> <?php echo htmlspecialchars($course['semester']); ?></p>
                                    <p><strong>Ακαδημαϊκό Έτος:</strong> <?php echo htmlspecialchars($course['academic_year']); ?></p>
                                    
                                    <?php if($course['description']): ?>
                                        <p class="text-muted"><em><?php echo htmlspecialchars($course['description']); ?></em></p>
                                    <?php endif; ?>
                                    
                                    <hr>
                                    
                                    <!-- Statistics -->
                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body p-2">
                                                    <h4 class="mb-0"><?php echo $assignments_count; ?></h4>
                                                    <small>Εργασίες</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-light">
                                                <div class="card-body p-2">
                                                    <h4 class="mb-0"><?php echo $submissions_count; ?></h4>
                                                    <small>Υποβολές</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="btn-group-vertical w-100" role="group">
                                        <a href="view_course.php?course_id=<?php echo $course_id; ?>" 
                                           class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i> Προβολή Μαθήματος
                                        </a>
                                        <a href="create_assignment.php?course_id=<?php echo $course_id; ?>" 
                                           class="btn btn-outline-success">
                                            <i class="fas fa-plus"></i> Νέα Εργασία
                                        </a>
                                        <a href="submissions.php?course_id=<?php echo $course_id; ?>" 
                                           class="btn btn-outline-info">
                                            <i class="fas fa-file-alt"></i> Υποβολές Φοιτητών
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    <?php endwhile; ?>
                </div>
                
            <?php else: ?>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Δεν έχετε δημιουργήσει ακόμα κανένα μάθημα.
                    <br><br>
                    <a href="create_course.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Δημιουργία Πρώτου Μαθήματος
                    </a>
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
