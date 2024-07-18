<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shelf_number = $_POST['shelf_number'];
    $location = $_POST['location'];

    $sql = "INSERT INTO shelves (shelf_number, location) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $shelf_number, $location);

    if ($stmt->execute()) {
        $message = "Shelf added successfully.";
    } else {
        $message = "Error adding shelf: " . $stmt->error;
    }

    $stmt->close();
}
?>
<?php include '../includes/header.php'; ?>
<div class="container">
    <h2>Add Shelf</h2>
    <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="shelf_number" class="form-label">Shelf Number</label>
            <input type="text" name="shelf_number" id="shelf_number" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" id="location" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Shelf</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
