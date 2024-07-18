<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
include '../includes/header.php';
session_start();

if (!isStaff()) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barcode = $_POST['barcode'];
    $sql = "SELECT * FROM users WHERE id='$barcode' AND approved=1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<div class='alert alert-success'>User Verified: " . $row['name'] . "</div>";
    } else {
        echo "<div class='alert alert-danger'>User not found or not approved.</div>";
    }
}
?>
<h2>Verify Member</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label for="barcode" class="form-label">Barcode</label>
        <input type="text" name="barcode" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Verify</button>
</form>
<?php include '../includes/footer.php'; ?>
