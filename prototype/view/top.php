<!DOCTYPE html>
<html lang="el">
<head>
  <title>University System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Bootstrap CSS and JavaScript for responsive design -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom CSS for navbar styling -->
  <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
<?php
$base_url = '/prototype/';
?>

<!-- Bootstrap Navbar  -->
<nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow-sm">
  <div class="container">

    <!--Links to homepage -->
    <a class="navbar-brand font-weight-bold" href="<?php echo $base_url; ?>index.php">
      Μητροπολιτικό Κολλέγιο
    </a>

    <!-- Mobile menu toggle button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navigation Menu Items -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <?php if(isset($_SESSION["username"])): ?>
        <!-- LOGGED IN USER MENU -->
        
        <!-- Home link -->
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base_url; ?>index.php">Αρχική</a>
        </li>
        
        <!-- Dashboard link -->
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base_url; ?>menu.php">Πίνακας Ελέγχου</a>
        </li>
        
        <!-- Logout link -->
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base_url; ?>logout.php">Αποσύνδεση</a>
        </li>
        
        <!-- Display username -->
        <li class="nav-item ml-3">
          <span class="navbar-text text-white">
            <?php echo htmlspecialchars($_SESSION["username"]); ?>
          </span>
        </li>

        <?php else: ?>
        <!--NON-LOGGED IN USER MENU-->
        
        <!-- Home link -->
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base_url; ?>index.php">Αρχική</a>
        </li>
        
        <!-- Registration link -->
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base_url; ?>signup.php">Εγγραφή</a>
        </li>
        
        <!-- Login link -->
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base_url; ?>login.php">Σύνδεση</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
