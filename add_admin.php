<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: admin/login.php");
    exit();
}
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    $sql = "INSERT INTO users (name, email, password, approved, is_admin) VALUES ('$name', '$email', '$password', 1, 1)";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Admin user added successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }
}
?>
<?php include 'includes/header.php'; ?>
<h2>Add Admin User</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Admin</button>
</form>
<?php include 'includes/footer.php'; ?>
