<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reserve_book'])) {
        $book_id = $_POST['book_id'];

        // Check if the book is already reserved by the user
        $sql = "SELECT * FROM reservations WHERE book_id = $book_id AND user_id = $user_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<div class='alert alert-warning'>You have already reserved this book.</div>";
        } else {
            // Reserve the book
            $sql = "INSERT INTO reservations (book_id, user_id, reserve_date, status) VALUES ($book_id, $user_id, NOW(), 'Reserved')";
            if ($conn->query($sql) === TRUE) {
                echo "<div class='alert alert-success'>Book reserved successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error reserving book: " . $conn->error . "</div>";
            }
        }
    } elseif (isset($_POST['cancel_reservation'])) {
        $reservation_id = $_POST['reservation_id'];

        // Fetch reservation date
        $sql = "SELECT reserve_date FROM reservations WHERE id = $reservation_id AND user_id = $user_id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $reserve_date = new DateTime($row['reserve_date']);
            $current_date = new DateTime();

            // Calculate the difference in days
            $interval = $current_date->diff($reserve_date)->days;

            // Check if the cancellation is within 1 day
            if ($interval <= 1) {
                // Apply a cancellation fee
                $fee = 5.00; // Example fee amount
                $sql = "INSERT INTO fees (user_id, amount, description) VALUES ($user_id, $fee, 'Cancellation fee for reservation within 1 day')";
                if ($conn->query($sql) === TRUE) {
                    echo "<div class='alert alert-info'>A cancellation fee of $$fee has been applied to your account.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error applying cancellation fee: " . $conn->error . "</div>";
                }
            }

            // Cancel the reservation
            $sql = "DELETE FROM reservations WHERE id = $reservation_id AND user_id = $user_id";
            if ($conn->query($sql) === TRUE) {
                echo "<div class='alert alert-success'>Reservation canceled successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error canceling reservation: " . $conn->error . "</div>";
            }
        }
    }
}

// Fetch available books along with their statuses
$sql = "SELECT b.id, b.title, b.author, b.genre, b.year, s.shelf_number AS shelf_location, 
        CASE 
            WHEN r.book_id IS NOT NULL AND r.status = 'Reserved' THEN 'Reserved' 
            WHEN l.book_id IS NOT NULL THEN 'Borrowed' 
            ELSE 'Available' 
        END AS status,
        r.id AS reservation_id
        FROM books b
        LEFT JOIN shelves s ON b.shelf_id = s.id
        LEFT JOIN reservations r ON b.id = r.book_id AND r.user_id = $user_id
        LEFT JOIN loans l ON b.id = l.book_id AND l.returned_at IS NULL";

$result = $conn->query($sql);
?>
<?php include '../includes/header.php'; ?>
<h2>Reserve Book</h2>
<?php if ($result->num_rows > 0) { ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Year</th>
                <th>Shelf Location</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['author']; ?></td>
                <td><?php echo $row['genre']; ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['shelf_location']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <?php if ($row['status'] == 'Available') { ?>
                    <form method="POST" action="">
                        <input type="hidden" name="book_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="reserve_book" class="btn btn-primary">Reserve</button>
                    </form>
                    <?php } elseif ($row['status'] == 'Reserved' && $row['reservation_id']) { ?>
                    <form method="POST" action="">
                        <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
                        <button type="submit" name="cancel_reservation" class="btn btn-danger">Cancel Reservation</button>
                    </form>
                    <?php } else { ?>
                    <button class="btn btn-secondary" disabled><?php echo $row['status']; ?></button>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <p>No books available for reservation.</p>
<?php } ?>
<?php include '../includes/footer.php'; ?>
