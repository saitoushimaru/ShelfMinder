<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
startSession();
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1>Welcome to Kota Samarahan Public Library Management System (ShelfMinder)</h1>
            <p>Your gateway to a world of books and resources.</p><br>
            <img src="assets/media/logo.png" width="200px" width="auto" alt="Library Logo" class="logo"><br>
            <?php if (!isLoggedIn()) { ?>
                <a href="signup.php" class="btn btn-primary btn-lg">Sign Up</a>
                <a href="login.php" class="btn btn-secondary btn-lg">Log In</a>
                <br><br>
                <a href="admin/login.php" class="btn btn-link">Admin Login</a>
            <?php } else { ?>
                <a href="user/dashboard.php" class="btn btn-primary btn-lg">Go to Dashboard</a>
            <?php } ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
