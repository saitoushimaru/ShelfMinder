<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch penalties and fees
$sql = "SELECT amount, description AS reason, paid, created_at FROM penalties WHERE user_id = $user_id";
$result = $conn->query($sql);
?>
<?php include '../includes/header.php'; ?>
<h2>Your Penalties</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Amount</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['amount']; ?></td>
            <td><?php echo $row['reason']; ?></td>
            <td><?php echo $row['paid'] ? 'Paid' : 'Unpaid'; ?></td>
            <td><?php echo $row['created_at']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>
