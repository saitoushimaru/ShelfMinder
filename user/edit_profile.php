<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
</head>
<body>
<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
include '../includes/header.php';
startSession();

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $user['password'];

    $sql = "UPDATE users SET name = '$name', email = '$email', password = '$password' WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Profile updated successfully.</div>";
        $user = getUserDetails($user_id); // Refresh user details
    } else {
        echo "<div class='alert alert-danger'>Error updating profile: " . $conn->error . "</div>";
    }
}
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <header class="card-header">
                    <a href="#" class="float-right btn btn-outline-primary mt-1">Dashboard</a>
                    <h4 class="card-title mt-2">Edit Profile</h4>
                </header>
                <article class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $user['name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Password (leave blank to keep current password)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
                    </form>
                </article>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>
