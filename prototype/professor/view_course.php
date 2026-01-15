<?php

session_start();

if(!isset($_SESSION["username"]) || $_SESSION["role_id"] != 2) {
    header("Location: ../index.php");
    exit();
}

if(!isset($_GET['course_id'])) {
    header("Location: courses.php");
    exit();
}

include("../config.php");

$course_id = intval($_GET['course_id']);
$professor_id = $_SESSION["user_id"];

// Get course details
$sql = "SELECT * FROM courses WHERE course_id = $course_id AND professor_id = $professor_id";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0) {
    die("Forbidden");
}

$course = mysqli_fetch_assoc($result);

// Get assignments
$assignments_sql = "SELECT * FROM assignments WHERE course_id = $course_id ORDER BY due_date DESC";
$assignments_result = mysqli_query($conn, $assignments_sql);

include("../view/top.php");
?>

<link rel="stylesheet" href="../css/menu.css">

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h2><?php echo htmlspecialchars($course['course_code']) . ' - ' . htmlspecialchars($course['course_name']); ?></h2>
        </div>
        <div class="card-body">
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Εξάμηνο:</strong> <?php echo htmlspecialchars($course['semester']); ?></p>
                    <p><strong>Ακαδημαϊκό Έτος:</strong> <?php echo htmlspecialchars($course['academic_year']); ?></p>
                    <?php if($course['description']): ?>
                        <p><strong>Περιγραφή:</strong></p>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <hr>
            
            <h4><i class="fas fa-tasks"></i> Εργασίες Μαθήματος</h4>
            
            <?php if(mysqli_num_rows($assignments_result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Τίτλος</th>
                                <th>Προθεσμία</th>
                                <th>Υποβολές</th>
                                <th>Ενέργειες</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($assignment = mysqli_fetch_assoc($assignments_result)): ?>
                                <?php
                                $assignment_id = $assignment['assignment_id'];
                                $count_sql = "SELECT COUNT(*) as total FROM submissions WHERE assignment_id = $assignment_id";
                                $count = mysqli_fetch_assoc(mysqli_query($conn, $count_sql))['total'];
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($assignment['title']); ?></strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($assignment['due_date'])); ?></td>
                                    <td><span class="badge badge-info"><?php echo $count; ?> υποβολές</span></td>
                                    <td>
                                        <a href="submissions.php?course_id=<?php echo $course_id; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Προβολή
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">Δεν υπάρχουν εργασίες ακόμα.</p>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="create_assignment.php?course_id=<?php echo $course_id; ?>" class="btn btn-success">
                    <i class="fas fa-plus"></i> Νέα Εργασία
                </a>
                <a href="submissions.php?course_id=<?php echo $course_id; ?>" class="btn btn-info">
                    <i class="fas fa-file-alt"></i> Υποβολές
                </a>
                <a href="courses.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Πίσω
                </a>
            </div>
            
        </div>
    </div>
</div>

<?php 
mysqli_close($conn);
include("../view/bottom.php"); 
?>
