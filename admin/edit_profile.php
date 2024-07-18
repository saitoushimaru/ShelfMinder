<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin = getUserDetails($admin_id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $admin['password'];

    $sql = "UPDATE users SET name = '$name', email = '$email', password = '$password' WHERE id = $admin_id";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Profile updated successfully.</div>";
        $admin = getUserDetails($admin_id); // Refresh admin details
    } else {
        echo "<div class='alert alert-danger'>Error updating profile: " . $conn->error . "</div>";
    }
}
?>
<?php include '../includes/header.php'; ?>
<h2>Edit Profile</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo $admin['name']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo $admin['email']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password (leave blank to keep current password)</label>
        <input type="password" name="password" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>
<?php include '../includes/footer.php'; ?>
