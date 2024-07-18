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

        // Verify book exists
        $book_sql = "SELECT id FROM books WHERE isbn = $book_isbn";
        $book_result = $conn->query($book_sql);

        if ($book_result->num_rows == 0) {
            echo "<div class='alert alert-danger'>Error: Book ID does not exist.</div>";
        } else {
            $book_row = $book_result->fetch_assoc();
            $book_id = $book_row['id'];

            // Verify loan exists
            $loan_sql = "SELECT id FROM loans WHERE book_id = $book_id AND user_id = $user_db_id AND returned_at IS NULL";
            $loan_result = $conn->query($loan_sql);

            if ($loan_result->num_rows == 0) {
                echo "<div class='alert alert-danger'>Error: This book is not currently loaned out to this user.</div>";
            } else {
                $loan_row = $loan_result->fetch_assoc();
                $loan_id = $loan_row['id'];

                // Mark the book as returned
                $update_loan_sql = "UPDATE loans SET returned_at = NOW() WHERE id = $loan_id";
                if ($conn->query($update_loan_sql) === TRUE) {
                    // Update book availability
                    $update_book_sql = "UPDATE books SET available = 1 WHERE id = $book_id";
                    $conn->query($update_book_sql);
                    echo "<div class='alert alert-success'>Book returned successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error returning book: " . $conn->error . "</div>";
                }
            }
        }
    }
}
?>
<?php include '../includes/header.php'; ?>
<h2>Return Book</h2>
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
    <button type="submit" class="btn btn-primary">Return Book</button>
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
