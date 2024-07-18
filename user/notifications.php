<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

// Fetch notifications for overdue books and penalties
$user_id = $_SESSION['user_id']; // Assuming user ID is stored in session after login

// Get overdue books
$overdue_sql = "
    SELECT b.title, l.due_date, DATEDIFF(NOW(), l.due_date) AS days_overdue
    FROM loans l
    JOIN books b ON l.book_id = b.id
    WHERE l.user_id = $user_id AND l.return_date IS NULL AND l.due_date < NOW()";

$overdue_result = $conn->query($overdue_sql);

// Get penalties (if you have a penalties table)
$penalty_sql = "
    SELECT p.amount, p.reason, p.date_issued
    FROM penalties p
    WHERE p.user_id = $user_id AND p.paid = 0";

$penalty_result = $conn->query($penalty_sql);

include '../includes/header.php'; 
?>

<h2>Notifications</h2>

<?php if ($overdue_result->num_rows > 0): ?>
    <h3>Overdue Books</h3>
    <ul>
        <?php while($row = $overdue_result->fetch_assoc()): ?>
            <li>
                <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                - Due on: <?php echo htmlspecialchars($row['due_date']); ?>
                - <?php echo htmlspecialchars($row['days_overdue']); ?> days overdue
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No overdue books.</p>
<?php endif; ?>

<?php if ($penalty_result->num_rows > 0): ?>
    <h3>Penalties</h3>
    <ul>
        <?php while($row = $penalty_result->fetch_assoc()): ?>
            <li>
                <strong>Amount:</strong> <?php echo htmlspecialchars($row['amount']); ?>
                - Reason: <?php echo htmlspecialchars($row['reason']); ?>
                - Issued on: <?php echo htmlspecialchars($row['date_issued']); ?>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No penalties.</p>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
