<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
startSession();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    // Verify the token and update the user's password
    $sql = "UPDATE users SET password='$new_password' WHERE reset_token='$token'";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Password reset successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error resetting password: " . $conn->error . "</div>";
    }
}
?>
<?php include 'includes/header.php'; ?>
<h2>Reset Password</h2>
<form method="POST" action="">
    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
    <div class="mb-3">
        <label for="new_password" class="form-label">New Password</label>
        <input type="password" name="new_password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Reset Password</button>
</form>
<?php include 'includes/footer.php'; ?>
