<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isStaff()) {
    header("Location: ../login.php");
    exit();
}

// Fetch all reserved books
$sql = "SELECT r.id, r.book_id, r.user_id, r.reserve_date, r.status, r.reserved_at, 
        b.title AS book_title, u.name AS user_name 
        FROM reservations r 
        JOIN books b ON r.book_id = b.id 
        JOIN users u ON r.user_id = u.id";
$result = $conn->query($sql);
$reserved_books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reserved_books[] = $row;
    }
}

// Update book status
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_status'])) {
        $reservation_id = $_POST['reservation_id'];
        $new_status = $_POST['new_status'];

        // Debugging output
        echo "Reservation ID: $reservation_id<br>";
        echo "New Status: $new_status<br>";

        $sql = "UPDATE reservations SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("si", $new_status, $reservation_id);
        if (!$stmt->execute()) {
            die('Execute failed: ' . $stmt->error);
        }
        $stmt->close();

        // Debugging output
        echo "Status updated successfully<br>";

        header("Location: view_reserved_books.php");
        exit();
    }
}
?>
<?php include '../includes/header.php'; ?>
<div class="container">
    <h2>Reserved Books</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Book Title</th>
                <th>User Name</th>
                <th>Reserve Date</th>
                <th>Status</th>
                <th>Reserved At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reserved_books as $book): ?>
            <tr>
                <td><?php echo $book['id']; ?></td>
                <td><?php echo $book['book_title']; ?></td>
                <td><?php echo $book['user_name']; ?></td>
                <td><?php echo $book['reserve_date']; ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="reservation_id" value="<?php echo $book['id']; ?>">
                        <select name="new_status" class="form-select" onchange="this.form.submit()">
                            <option value="reserved" <?php echo $book['status'] == 'reserved' ? 'selected' : ''; ?>>Reserved</option>
                            <option value="cancelled" <?php echo $book['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="fulfilled" <?php echo $book['status'] == 'fulfilled' ? 'selected' : ''; ?>>Fulfilled</option>
                            <option value="ready to pick up" <?php echo $book['status'] == 'ready to pick up' ? 'selected' : ''; ?>>Ready to Pick Up</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                    </form>
                </td>
                <td><?php echo $book['reserved_at']; ?></td>
                <td>
                    <span class="badge bg-<?php echo $book['status'] == 'fulfilled' ? 'success' : 'info'; ?>">
                        <?php echo $book['status']; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
