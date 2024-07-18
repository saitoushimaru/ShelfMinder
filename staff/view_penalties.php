<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isStaff()) {
    header("Location: ../login.php");
    exit();
}

// Fetch all user penalties
$sql = "SELECT p.id, p.user_id, u.name, p.amount, p.reason, p.paid, p.created_at 
        FROM penalties p
        JOIN users u ON p.user_id = u.id";
$result = $conn->query($sql);
?>
<?php include '../includes/header.php'; ?>
<h2>User Penalties</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>User Name</th>
            <th>Amount</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['amount']; ?></td>
            <td><?php echo $row['reason']; ?></td>
            <td><?php echo $row['paid'] ? 'Paid' : 'Unpaid'; ?></td>
            <td><?php echo $row['created_at']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>
