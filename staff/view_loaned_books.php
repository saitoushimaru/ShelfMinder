<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isStaff()) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return_book'])) {
    $loan_id = $_POST['loan_id'];
    $book_id = $_POST['book_id'];

    // Mark the book as returned
    $sql = "UPDATE loans SET returned_at = NOW() WHERE id = $loan_id";
    if ($conn->query($sql) === TRUE) {
        // Update book availability
        $update_sql = "UPDATE books SET available = 1 WHERE id = $book_id";
        $conn->query($update_sql);
        echo "<div class='alert alert-success'>Book returned successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error returning book: " . $conn->error . "</div>";
    }
}

// Fetch loaned books
$sql = "SELECT loans.id AS loan_id, books.id AS book_id, users.name, books.title, loans.borrowed_at 
        FROM loans 
        JOIN users ON loans.user_id = users.id 
        JOIN books ON loans.book_id = books.id 
        WHERE loans.returned_at IS NULL";
$result = $conn->query($sql);
?>
<?php include '../includes/header.php'; ?>
<h2>Loaned Books</h2>
<table class="table">
    <thead>
        <tr>
            <th>User Name</th>
            <th>Book Title</th>
            <th>Borrowed Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['borrowed_at']; ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="loan_id" value="<?php echo $row['loan_id']; ?>">
                        <input type="hidden" name="book_id" value="<?php echo $row['book_id']; ?>">
                        <button type="submit" name="return_book" class="btn btn-primary">Return Book</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>
