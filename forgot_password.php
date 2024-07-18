<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
startSession();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    // Assuming there's a function to send email and reset password.
    // This part needs to be implemented: sending an email with a reset link.
    echo "<div class='alert alert-success'>If the email is registered, you will receive a password reset link.</div>";
}
?>
<?php include 'includes/header.php'; ?>
<h2>Forgot Password</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
<?php include 'includes/footer.php'; ?>
