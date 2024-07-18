<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['title'])) {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $genre = $_POST['genre'];
        $published_date = $_POST['published_date'];
        $isbn = $_POST['isbn'];
        $shelf_id = $_POST['shelf_id'];
        $year = $_POST['year'];
        $available = 1;  // Assuming new books are available

        // Handle image upload
        $image_path = '';
        if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] == UPLOAD_ERR_OK) {
            $image_name = basename($_FILES['book_image']['name']);
            $target_dir = "../uploads/books/";
            $target_file = $target_dir . $image_name;

            // Ensure the target directory exists
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES['book_image']['tmp_name'], $target_file)) {
                $image_path = "uploads/books/" . $image_name;
            } else {
                $message = "Error uploading image.";
            }
        }

        $sql = "INSERT INTO books (title, author, genre, published_date, isbn, available, shelf_id, year, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('sssssiiss', $title, $author, $genre, $published_date, $isbn, $available, $shelf_id, $year, $image_path);
            if ($stmt->execute()) {
                $message = "New book added successfully.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
    } elseif (isset($_FILES['excel_file'])) {
        $file = $_FILES['excel_file']['tmp_name'];
        $spreadsheet = IOFactory::load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO books (title, author, genre, published_date, isbn, available, shelf_id, year, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($sheetData as $row) {
                if (!empty($row[0]) && !empty($row[1]) && !empty($row[2]) && !empty($row[3]) && !empty($row[4]) && !empty($row[5]) && !empty($row[6]) && !empty($row[7])) {
                    $title = $conn->real_escape_string($row[0]);
                    $author = $conn->real_escape_string($row[1]);
                    $genre = $conn->real_escape_string($row[2]);
                    $published_date = $conn->real_escape_string($row[3]);
                    $isbn = $conn->real_escape_string($row[4]);
                    $available = 1;  // Assuming imported books are available
                    $shelf_id = $conn->real_escape_string($row[5]);
                    $year = $conn->real_escape_string($row[6]);
                    $image_path = ''; // Add logic to handle image paths if available in Excel

                    $stmt->bind_param('sssssiiss', $title, $author, $genre, $published_date, $isbn, $available, $shelf_id, $year, $image_path);
                    if (!$stmt->execute()) {
                        throw new Exception($stmt->error);
                    }
                }
            }
            $conn->commit();
            $message = "Books imported successfully from Excel.";
        } catch (Exception $e) {
            $conn->rollback();
            $message = "Error importing books: " . $e->getMessage();
        } finally {
            $stmt->close();
        }
    }
}

// Fetch all shelves for the dropdown
$shelves_sql = "SELECT id, shelf_number FROM shelves";
$shelves_result = $conn->query($shelves_sql);
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Add New Book</h2>
    <?php if (!empty($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="author" class="form-label">Author</label>
            <input type="text" class="form-control" id="author" name="author" required>
        </div>
        <div class="mb-3">
            <label for="genre" class="form-label">Genre</label>
            <input type="text" class="form-control" id="genre" name="genre" required>
        </div>
        <div class="mb-3">
            <label for="published_date" class="form-label">Published Date</label>
            <input type="date" class="form-control" id="published_date" name="published_date" required>
        </div>
        <div class="mb-3">
            <label for="isbn" class="form-label">ISBN</label>
            <input type="text" class="form-control" id="isbn" name="isbn" required>
        </div>
        <div class="mb-3">
            <label for="shelf_id" class="form-label">Shelf Number</label>
            <select class="form-control" id="shelf_id" name="shelf_id" required>
                <?php while($shelf = $shelves_result->fetch_assoc()): ?>
                    <option value="<?php echo $shelf['id']; ?>"><?php echo $shelf['shelf_number']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="year" class="form-label">Year</label>
            <input type="number" class="form-control" id="year" name="year" required>
        </div>
        <div class="mb-3">
            <label for="book_image" class="form-label">Book Image</label>
            <input type="file" class="form-control" id="book_image" name="book_image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-success">Add Book</button>
    </form>

    <h2 class="mt-5">Import Books from Excel</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="excel_file" class="form-label">Excel File</label>
            <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx, .xls" required>
        </div>
        <button type="submit" class="btn btn-primary">Import from Excel</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
