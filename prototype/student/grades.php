<?php
session_start();

// Check if user is logged in and is a student
if(!isset($_SESSION["username"]) || $_SESSION["role_id"] != 1) {
    header("Location: ../index.php");
    exit();
}

include("../config.php");

$student_id = $_SESSION["user_id"];

// Get all graded submissions for this student
$sql = "SELECT g.*, s.submission_text, s.submitted_at, 
               a.title as assignment_title, a.due_date,
               c.course_name, c.course_code,
               u.username as professor_name
        FROM grades g
        JOIN submissions s ON g.submission_id = s.submission_id
        JOIN assignments a ON s.assignment_id = a.assignment_id
        JOIN courses c ON a.course_id = c.course_id
        JOIN users u ON g.graded_by = u.user_id
        WHERE s.student_id = $student_id
        ORDER BY g.graded_at DESC";

$grades_result = mysqli_query($conn, $sql);

// Calculate statistics
$total_grades = 0;
$count = 0;
$grades_array = [];

$temp_result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($temp_result)) {
    $total_grades += $row['grade'];
    $count++;
    $grades_array[] = $row['grade'];
}

$average = $count > 0 ? round($total_grades / $count, 2) : 0;
$max_grade = $count > 0 ? max($grades_array) : 0;
$min_grade = $count > 0 ? min($grades_array) : 0;

include("../view/top.php");
?>

<link rel="stylesheet" href="../css/menu.css">

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h2><i class="fas fa-chart-line"></i> Οι Βαθμοί μου</h2>
        </div>
        <div class="card-body">
            <p class="text-muted">Εδώ μπορείτε να δείτε τους βαθμούς και τα σχόλια των καθηγητών σας.</p>
            
            <?php if($count > 0): ?>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $count; ?></h3>
                                <p class="mb-0">Βαθμολογημένες Εργασίες</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $average; ?></h3>
                                <p class="mb-0">Μέσος Όρος</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $max_grade; ?></h3>
                                <p class="mb-0">Υψηλότερος Βαθμός</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $min_grade; ?></h3>
                                <p class="mb-0">Χαμηλότερος Βαθμός</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Grades List -->
                <h5 class="mb-3"><i class="fas fa-list"></i> Λεπτομέρειες Βαθμολογιών</h5>
                
                <?php while($grade = mysqli_fetch_assoc($grades_result)): ?>
                    
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-0">
                                        <span class="badge badge-secondary"><?php echo htmlspecialchars($grade['course_code']); ?></span>
                                        <?php echo htmlspecialchars($grade['assignment_title']); ?>
                                    </h6>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($grade['course_name']); ?>
                                    </small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <?php
                                    $grade_value = $grade['grade'];
                                    $badge_class = 'badge-secondary';
                                    if($grade_value >= 85) {
                                        $badge_class = 'badge-success';
                                    } elseif($grade_value >= 70) {
                                        $badge_class = 'badge-primary';
                                    } elseif($grade_value >= 50) {
                                        $badge_class = 'badge-warning';
                                    } else {
                                        $badge_class = 'badge-danger';
                                    }
                                    ?>
                                    <h3 class="mb-0">
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo $grade_value; ?>/100
                                        </span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-user-tie"></i> Βαθμολογήθηκε από:</strong> 
                                       <?php echo htmlspecialchars($grade['professor_name']); ?></p>
                                    <p><strong><i class="fas fa-calendar-check"></i> Ημερομηνία Βαθμολόγησης:</strong> 
                                       <?php echo date('d/m/Y H:i', strtotime($grade['graded_at'])); ?></p>
                                    <p><strong><i class="fas fa-upload"></i> Υποβλήθηκε:</strong> 
                                       <?php echo date('d/m/Y H:i', strtotime($grade['submitted_at'])); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <?php if($grade['feedback']): ?>
                                        <div class="alert alert-info mb-0">
                                            <strong><i class="fas fa-comment"></i> Σχόλια Καθηγητή:</strong>
                                            <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($grade['feedback'])); ?></p>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted"><em>Δεν υπάρχουν σχόλια.</em></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Submission Preview -->
                            <hr>
                            <details>
                                <summary class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye"></i> Προβολή Υποβολής
                                </summary>
                                <div class="mt-3 p-3 bg-light border rounded">
                                    <?php echo nl2br(htmlspecialchars($grade['submission_text'])); ?>
                                </div>
                            </details>
                        </div>
                    </div>
                    
                <?php endwhile; ?>
                
            <?php else: ?>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Δεν έχετε ακόμα βαθμολογημένες εργασίες.
                </div>
                
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="../menu.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Επιστροφή στον Πίνακα Ελέγχου
                </a>
                <a href="courses.php" class="btn btn-primary">
                    <i class="fas fa-book"></i> Τα Μαθήματά μου
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
mysqli_close($conn);
include("../view/bottom.php"); 
?>
