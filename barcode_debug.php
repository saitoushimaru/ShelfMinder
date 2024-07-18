<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
startSession();

if (!isStaff()) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barcode = trim($_POST['barcode']);  // Trim any whitespace or newlines
    $book_id = intval($_POST['book_id']);  // Ensure book_id is an integer
    
    // Debugging: Print the barcode value
    echo "Scanned Barcode: " . htmlspecialchars($barcode) . "<br>";

    // Fetch user details using the barcode
    $sql = "SELECT id FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        // Issue book to the user
        $sql = "INSERT INTO loans (user_id, book_id, loan_date, due_date) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY))";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $book_id);
        if ($stmt->execute()) {
            $message = "Book issued successfully.";
        } else {
            $message = "Failed to issue book.";
        }
    } else {
        $message = "No user found with that barcode.";
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <h2>Issue Book</h2>
    <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="barcode" class="form-label">User Barcode</label>
            <input type="text" name="barcode" class="form-control" id="barcode" required>
        </div>
        <div class="mb-3">
            <label for="book_id" class="form-label">Book ID</label>
            <input type="text" name="book_id" class="form-control" id="book_id" required>
        </div>
        <button type="submit" class="btn btn-primary">Issue Book</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
