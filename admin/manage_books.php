<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

// Handle delete request
if (isset($_POST['delete_book_id'])) {
    $delete_book_id = $_POST['delete_book_id'];
    
    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Delete related records in the dependent table(s)
        $sql = "DELETE FROM loans WHERE book_id = $delete_book_id";
        $conn->query($sql);

        $sql = "DELETE FROM reservations WHERE book_id = $delete_book_id";
        $conn->query($sql);

        // Delete the book record
        $sql = "DELETE FROM books WHERE id = $delete_book_id";
        $conn->query($sql);

        // Commit the transaction
        $conn->commit();
        $message = "Book deleted successfully.";
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $message = "Error deleting book: " . $exception->getMessage();
    }
}

// Handle generate barcode request
if (isset($_POST['generate_barcode'])) {
    if (!empty($_POST['selected_books'])) {
        $selected_books = $_POST['selected_books'];
        
        // Include a barcode generation library (such as TCPDF) to create barcodes for the selected books
        require_once('../includes/tcpdf/tcpdf.php');

        // Create a new PDF document
        $pdf = new TCPDF();
        $pdf->AddPage();

        foreach ($selected_books as $book_id) {
            // Fetch book details
            $book_sql = "SELECT * FROM books WHERE id = $book_id";
            $book_result = $conn->query($book_sql);
            if ($book_result && $book_result->num_rows > 0) {
                $book = $book_result->fetch_assoc();
                $isbn = $book['isbn'];

                // Generate barcode for the book
                $style = array(
                    'border' => false,
                    'padding' => 4,
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false
                );
                $pdf->write1DBarcode($isbn, 'C128', '', '', '', 18, 0.4, $style, 'N');
                $pdf->Ln();
                $pdf->Cell(0, 0, $book['title'], 0, 1);
                $pdf->Ln(10);
            }
        }

        // Output the PDF as a download
        $pdf->Output('barcodes.pdf', 'D');
        exit();
    } else {
        $message = "No books selected for barcode generation.";
    }
}

// Fetch all books
$sql = "SELECT books.*, shelves.shelf_number FROM books LEFT JOIN shelves ON books.shelf_id = shelves.id";
$result = $conn->query($sql);
$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}
?>
<?php include '../includes/header.php'; ?>
<div class="container">
    <h2>Manage Books</h2>
    <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
    <a href="add_book.php" class="btn btn-success mb-3">Add Book</a>
    <form method="POST" action="">
        <div class="mb-3">
            <button type="submit" name="generate_barcode" class="btn btn-primary">Generate Barcode for Selected</button>
            <button type="submit" name="delete_books" class="btn btn-danger">Delete Selected</button>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Year</th>
                    <th>ISBN</th>
                    <th>Shelf Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                <tr>
                    <td><input type="checkbox" name="selected_books[]" value="<?php echo $book['id']; ?>"></td>
                    <td><?php echo $book['id']; ?></td>
                    <td><?php echo $book['title']; ?></td>
                    <td><?php echo $book['author']; ?></td>
                    <td><?php echo $book['genre']; ?></td>
                    <td><?php echo isset($book['year']) ? $book['year'] : 'N/A'; ?></td>
                    <td><?php echo $book['isbn']; ?></td>
                    <td><?php echo $book['shelf_number']; ?></td>
                    <td>
                        <a href="update_book.php?id=<?php echo $book['id']; ?>" class="btn btn-primary btn-sm">Update</a>
                        <a href="generate_barcode.php?id=<?php echo $book['id']; ?>" class="btn btn-secondary btn-sm">Generate Barcode</a>
                        <form method="POST" action="" style="display:inline-block;">
                            <input type="hidden" name="delete_book_id" value="<?php echo $book['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
