<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM users WHERE approved=0";
$result = $conn->query($sql);
?>
<?php include '../includes/header.php'; ?>
<h2>Approve Users</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="approve" class="btn btn-success">Approve</button>
                    <button type="submit" name="reject" class="btn btn-danger">Reject</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    if (isset($_POST['approve'])) {
        $sql = "UPDATE users SET approved=1 WHERE id='$user_id'";
    } else {
        $sql = "DELETE FROM users WHERE id='$user_id'";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Action successful.</div>";
        header("Location: approve_user.php");
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>
<?php include '../includes/footer.php'; ?>
