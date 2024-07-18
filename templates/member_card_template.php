<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
include '../includes/header.php';
session_start();

if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

require 'vendor/autoload.php'; // Composer autoloader for barcode library

use Picqer\Barcode\BarcodeGeneratorHTML;

$generator = new BarcodeGeneratorHTML();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $sql = "SELECT * FROM users WHERE id='$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $barcode = $generator->getBarcode($row['id'], $generator::TYPE_CODE_128);
    } else {
        echo "<div class='alert alert-danger'>User not found.</div>";
    }
}
?>
<h2>Generate Member Card</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label for="user_id" class="form-label">User ID</label>
        <input type="number" name="user_id" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Generate</button>
</form>
<?php if (isset($barcode)) { ?>
<div class="mt-4">
    <h3>Member Card</h3>
    <p>Name: <?php echo $row['name']; ?></p>
    <p>Email: <?php echo $row['email']; ?></p>
    <div><?php echo $barcode; ?></div>
    <button onclick="window.print()" class="btn btn-secondary mt-3">Print</button>
</div>
<?php } ?>
<?php include '../includes/footer.php'; ?>
