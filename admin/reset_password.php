<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $sql = "UPDATE users SET password = '$new_password' WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Password reset successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error resetting password: " . $conn->error . "</div>";
    }
}

// Fetch all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>
<?php include '../includes/header.php'; ?>
<h2>Reset User Password</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label for="user_id" class="form-label">Select User</label>
        <select name="user_id" class="form-control" required>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?> (<?php echo $row['email']; ?>)</option>
            <?php } ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="new_password" class="form-label">New Password</label>
        <input type="password" name="new_password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Reset Password</button>
</form>
<?php include '../includes/footer.php'; ?>
