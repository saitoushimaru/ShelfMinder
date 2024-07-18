<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isStaff()) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_isbn = $_POST['book_id'];
    $user_id = $_POST['user_id'];

    // Verify user exists
    $user_sql = "SELECT id FROM users WHERE user_id = $user_id";
    $user_result = $conn->query($user_sql);

    if ($user_result->num_rows == 0) {
        echo "<div class='alert alert-danger'>Error: User ID does not exist.</div>";
    } else {
        $user_row = $user_result->fetch_assoc();
        $user_db_id = $user_row['id'];

        // Verify book exists and is available
        $book_sql = "SELECT id, available FROM books WHERE isbn = $book_isbn";
        $book_result = $conn->query($book_sql);

        if ($book_result->num_rows == 0) {
            echo "<div class='alert alert-danger'>Error: Book ID does not exist or is not available.</div>";
        } else {
            $book_row = $book_result->fetch_assoc();
            if ($book_row['available'] == 0) {
                echo "<div class='alert alert-danger'>Error: Book is not available.</div>";
            } else {
                $book_id = $book_row['id'];

                // Calculate due date (14 days from today)
                $due_date = date('Y-m-d', strtotime('+14 days'));

                // Issue the book to the user
                $sql = "INSERT INTO loans (book_id, user_id, loan_date, due_date, borrowed_at) VALUES ($book_id, $user_db_id, CURDATE(), '$due_date', NOW())";
                if ($conn->query($sql) === TRUE) {
                    // Update book availability
                    $update_sql = "UPDATE books SET available = 0 WHERE id = $book_id";
                    $conn->query($update_sql);
                    echo "<div class='alert alert-success'>Book issued successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error issuing book: " . $conn->error . "</div>";
                }
            }
        }
    }
}
?>
<?php include '../includes/header.php'; ?>
<h2>Issue Book to User</h2>
<form method="POST" action="">
    <div class="mb-3">
        <label for="user_id" class="form-label">Scan Member Card</label>
        <input type="text" name="user_id" id="user_id" class="form-control" required>
        <button type="button" class="btn btn-secondary" onclick="startScanner('user_id')">Scan Member Card</button>
    </div>
    <div class="mb-3">
        <label for="book_id" class="form-label">Scan Book QR Code</label>
        <input type="text" name="book_id" id="book_id" class="form-control" required>
        <button type="button" class="btn btn-secondary" onclick="startScanner('book_id')">Scan Book</button>
    </div>
    <button type="submit" class="btn btn-primary">Issue Book</button>
</form>

<!-- Scanner Modal -->
<div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scannerModalLabel">Scan Barcode/QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="scanner"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
function startScanner(inputId) {
    const modal = new bootstrap.Modal(document.getElementById('scannerModal'), {
        backdrop: 'static',
        keyboard: false
    });
    modal.show();

    Quagga.init({
        inputStream: {
            name: "Live",
            type: "LiveStream",
            target: document.querySelector('#scanner')
        },
        decoder: {
            readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "code_39_vin_reader", "codabar_reader", "upc_reader", "upc_e_reader", "i2of5_reader", "2of5_reader", "code_93_reader", "qr_reader"]
        }
    }, function (err) {
        if (err) {
            console.log(err);
            return;
        }
        Quagga.start();
    });

    Quagga.onDetected(function (data) {
        document.getElementById(inputId).value = data.codeResult.code;
        Quagga.stop();
        modal.hide();
    });
}
</script>
<?php include '../includes/footer.php'; ?>
