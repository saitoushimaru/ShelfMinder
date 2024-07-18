<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
startSession();

if (isLoggedIn()) {
    // Redirect to respective dashboard
    if (isAdmin()) {
        header("Location: admin/dashboard.php");
    } elseif (isStaff()) {
        header("Location: staff/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check in the users table
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['approved'] == 1) {
            // Set session variables for users
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            if ($user['is_admin']) {
                $_SESSION['admin_id'] = $user['id'];
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit();
        } else {
            $error = 'Your account is not approved by an admin yet.';
        }
    } else {
        // Check in the staff table
        $sql = "SELECT * FROM staff WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $staff = $result->fetch_assoc();
        $stmt->close();

        if ($staff && password_verify($password, $staff['password'])) {
            // Set session variables for staff
            $_SESSION['staff_id'] = $staff['id'];
            $_SESSION['email'] = $staff['email'];
            header("Location: staff/dashboard.php");
            exit();
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <header class="card-header">
                    <a href="signup.php" class="float-right btn btn-outline-primary mt-1">Sign up</a>
                    <h4 class="card-title mt-2">Login</h4>
                </header>
                <article class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                </article>
            </div>
        </div>
    </div>
</div>
<footer class="text-center mt-4">
        <p>&copy; 2024 All rights reserved, ShelfMinder (Kota Samarahan Public Library Management System) </p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
