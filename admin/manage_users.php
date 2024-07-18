<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $delete_user_id = intval($_POST['delete_user_id']);
    
    // Delete user from the database
    $sql = "DELETE FROM users WHERE id = $delete_user_id";
    if ($conn->query($sql) === TRUE) {
        $message = "User deleted successfully.";
    } else {
        $message = "Error deleting user: " . $conn->error;
    }
}

// Fetch all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<?php include '../includes/header.php'; ?>
<div class="container">
    <h2>Manage Users</h2>
    <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['user_id']; ?></td>
                <td><?php echo $user['name']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td>
                    <a href="update_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">Update</a>
                    <a href="generate_library_card.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm">Generate Library Card</a>
                    <form method="POST" action="" style="display:inline-block;">
                        <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
