<?php
session_start();

// Include the navigation bar
include("view/top.php");
?>

<!-- Include Bootstrap CSS for responsive design -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include custom CSS for styling -->
<link rel="stylesheet" href="css/index.css">

<div class="container mt-5">
    <div class="container mt-4">
        <div class="row">
            <!-- Welcome Text and Action Buttons -->
            <div class="col-md-6">
                <div class="data">
                    <h1>Μητροπολιτικό Κολλέγιο</h1>
                    <p>Καλώς ήρθατε στο σύστημα διαχείρισης πανεπιστημίου</p>
                    
                    <?php 
                    // Check if user is logged in by checking session variable
                    if(isset($_SESSION["username"])) {
                        // Display logged-in user information
                        echo "<p>Συνδεδεμένος ως: <strong>" . htmlspecialchars($_SESSION["username"]) . "</strong></p>";
                        echo "<p>Ρόλος: <strong>" . htmlspecialchars($_SESSION["role_name"]) . "</strong></p>";
                        echo '<a href="menu.php" class="btn btn-primary">Πίνακας Ελέγχου</a>';
                    } else {
                        // Display login and registration buttons for non-logged-in users
                        echo '<a href="login.php" class="btn sign-in">Σύνδεση</a> ';
                        echo '<a href="signup.php" class="btn sign-up">Εγγραφή</a>';
                    }
                    ?>
                </div>
            </div>
            
            <!--Image Gallery -->
            <div class="col-md-6">
                <div class="gallery-container">
                    <!-- Image Slideshow - Shows campus photos -->
                    <div class="mySlides">
                        <img src="images/campus1.jpg" alt="Κεντρικό Κτήριο">
                    </div>
                    <div class="mySlides">
                        <img src="images/campus2.jpg" alt="Κεντρικό Κτήριο">
                    </div>
                    <div class="mySlides">
                        <img src="images/campus3.jpg" alt="Κεντρικό Κτήριο">
                    </div>
                    <div class="mySlides">
                        <img src="images/library.jpg" alt="Βιβλιοθήκη">
                    </div>
                    <div class="mySlides">
                        <img src="images/library2.jpg" alt="Βιβλιοθήκη">
                    </div>
                    <div class="mySlides">
                        <img src="images/library3.jpg" alt="Βιβλιοθήκη">
                    </div>
                    <div class="mySlides">
                        <img src="images/labs.jpg" alt="Εργαστήρια Πληροφορικής">
                    </div>
                    <div class="mySlides">
                        <img src="images/labs2.jpg" alt="Εργαστήρια Διαιτολογίας">
                    </div>
                    <div class="mySlides">
                        <img src="images/labs3.jpg" alt="Σύγχρονα Εργαστήρια">
                    </div>

                    <!-- Previous and Next buttons for slideshow -->
                    <button class="prev" onclick="plusSlides(-1)">&#10094;</button>
                    <button class="next" onclick="plusSlides(1)">&#10095;</button>

                    <!-- Thumbnail navigation for slideshow -->
                    <div class="thumbnail-row">
                        <img class="thumbnail active" src="images/campus1.jpg" onclick="currentSlide(1)" alt="Thumbnail 1">
                        <img class="thumbnail" src="images/campus2.jpg" onclick="currentSlide(2)" alt="Thumbnail 2">
                        <img class="thumbnail" src="images/campus3.jpg" onclick="currentSlide(3)" alt="Thumbnail 3">
                        <img class="thumbnail" src="images/library.jpg" onclick="currentSlide(4)" alt="Thumbnail 4">
                        <img class="thumbnail" src="images/library2.jpg" onclick="currentSlide(5)" alt="Thumbnail 5">
                        <img class="thumbnail" src="images/library3.jpg" onclick="currentSlide(6)" alt="Thumbnail 6">
                        <img class="thumbnail" src="images/labs.jpg" onclick="currentSlide(7)" alt="Thumbnail 7">
                        <img class="thumbnail" src="images/labs2.jpg" onclick="currentSlide(8)" alt="Thumbnail 8">
                        <img class="thumbnail" src="images/labs3.jpg" onclick="currentSlide(9)" alt="Thumbnail 9">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Campus Location Map Section -->
    <div class="card mt-4">
        <div class="card-header bg-danger text-white">
            <h3>Το Campus μας</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Campus Information -->
                <div class="col-md-6">
                    <h4>Μητροπολιτικό Κολλέγιο</h4>
                    <p><i class="fas fa-map-marker-alt"></i> <strong>Διεύθυνση:</strong> Sorou 74, Marousi 151 25</p>
                    <p>Campus Αμαρουσίου<br>151 25 Μαρούσι<br>Τηλ: 213 3330300</p>
                    <!-- Button to center map on campus location -->
                    <button class="btn btn-info mt-3" onclick="centerMap()">
                        <i class="fas fa-crosshairs"></i> Κεντράρισμα Χάρτη
                    </button>
                </div>
                <!--Interactive Map -->
                <div class="col-md-6">
                    <!-- Map container - populated by JavaScript -->
                    <div id="map" style="height: 400px; border-radius: 10px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Include footer
include("view/bottom.php");
?>

<!-- Include Leaflet.js CSS for interactive maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Include Leaflet.js JavaScript library -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<!-- Include custom JavaScript for map and slideshow -->
<script src="js/main.js"></script>

</body>
</html>
