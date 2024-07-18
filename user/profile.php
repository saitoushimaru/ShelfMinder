<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION['user_id'];

$user = getUserDetails($user_id);
if (!$user) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit();
}

$user_name = $user['name'];
$user_email = $user['email'];
?>

<?php include '../includes/header.php'; ?>
<h2>User Profile</h2>
<p><strong>Name:</strong> <?php echo $user_name; ?></p>
<p><strong>Email:</strong> <?php echo $user_email; ?></p>
<?php include '../includes/footer.php'; ?>
