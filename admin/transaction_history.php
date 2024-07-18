<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

// Fetch all transactions
$sql = "SELECT t.id, t.book_id, b.title, t.user_id, u.name, t.description AS transaction_type, t.transaction_date 
        FROM transactions t
        JOIN books b ON t.book_id = b.id
        JOIN users u ON t.user_id = u.id
        ORDER BY t.transaction_date DESC";
$result = $conn->query($sql);

if ($result === FALSE) {
    die("Error: " . $conn->error);
}
?>
<?php include '../includes/header.php'; ?>
<h2>Transaction History</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Transaction ID</th>
            <th>Book Title</th>
            <th>User Name</th>
            <th>Transaction Type</th>
            <th>Transaction Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['transaction_type']; ?></td>
            <td><?php echo $row['transaction_date']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>
