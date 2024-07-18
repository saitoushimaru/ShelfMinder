<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

// Handle delete request
if (isset($_POST['delete_shelf_id'])) {
    $delete_shelf_id = $_POST['delete_shelf_id'];
    
    $sql = "DELETE FROM shelves WHERE id = $delete_shelf_id";
    if ($conn->query($sql) === TRUE) {
        $message = "Shelf deleted successfully.";
    } else {
        $message = "Error deleting shelf: " . $conn->error;
    }
}

// Fetch all shelves
$sql = "SELECT * FROM shelves";
$result = $conn->query($sql);
$shelves = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shelves[] = $row;
    }
}
?>
<?php include '../includes/header.php'; ?>
<div class="container">
    <h2>Manage Shelves</h2>
    <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
    <a href="add_shelf.php" class="btn btn-success mb-3">Add Shelf</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Shelf Number</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($shelves as $shelf): ?>
            <tr>
                <td><?php echo $shelf['id']; ?></td>
                <td><?php echo $shelf['shelf_number']; ?></td>
                <td><?php echo $shelf['location']; ?></td>
                <td>
                    <a href="update_shelf.php?id=<?php echo $shelf['id']; ?>" class="btn btn-primary btn-sm">Update</a>
                    <form method="POST" action="" style="display:inline-block;">
                        <input type="hidden" name="delete_shelf_id" value="<?php echo $shelf['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
