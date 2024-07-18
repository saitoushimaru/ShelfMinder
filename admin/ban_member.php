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
    $action = $_POST['action'];

    if ($action == 'ban') {
        // Ban the user
        $sql = "UPDATE users SET banned = 1 WHERE id = $user_id";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>Member banned successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error banning member: " . $conn->error . "</div>";
        }
    } elseif ($action == 'lift_ban') {
        // Lift the ban
        $sql = "UPDATE users SET banned = 0 WHERE id = $user_id";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>Ban lifted successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error lifting ban: " . $conn->error . "</div>";
        }
    }
}

// Fetch all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>
<?php include '../includes/header.php'; ?>
<h2>Ban or Lift Ban on Members</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['banned'] ? 'Banned' : 'Active'; ?></td>
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                    <?php if ($row['banned']) { ?>
                        <button type="submit" name="action" value="lift_ban" class="btn btn-success">Lift Ban</button>
                    <?php } else { ?>
                        <button type="submit" name="action" value="ban" class="btn btn-danger">Ban</button>
                    <?php } ?>
                </form>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>
