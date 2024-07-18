<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

$book_id = $_GET['id'];
$sql = "SELECT * FROM books WHERE id = $book_id";
$result = $conn->query($sql);
$book = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $year = $_POST['year'];
    $shelf_id = $_POST['shelf_id'];

    // Handle image upload
    $image_path = $book['image_path']; // Use existing image path if no new image is uploaded
    if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] == UPLOAD_ERR_OK) {
        $image_name = basename($_FILES['book_image']['name']);
        $target_dir = "../uploads/";
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES['book_image']['tmp_name'], $target_file)) {
            $image_path = "uploads/" . $image_name;
        }
    }

    $sql = "UPDATE books SET title='$title', author='$author', genre='$genre', year='$year', shelf_id='$shelf_id', image_path='$image_path' WHERE id=$book_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: manage_books.php");
        exit();
    } else {
        $message = "Error updating book: " . $conn->error;
    }
}

// Fetch all shelves
$sql = "SELECT * FROM shelves";
$result = $conn->query($sql);
$shelves = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shelves[] = $row;
    }
}
?>
<?php include '../includes/header.php'; ?>
<div class="container">
    <h2>Update Book</h2>
    <?php if (isset($message)) { echo "<div class='alert alert-danger'>$message</div>"; } ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="<?php echo $book['title']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="author" class="form-label">Author</label>
            <input type="text" name="author" id="author" class="form-control" value="<?php echo $book['author']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="genre" class="form-label">Genre</label>
            <input type="text" name="genre" id="genre" class="form-control" value="<?php echo $book['genre']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="year" class="form-label">Year</label>
            <input type="number" name="year" id="year" class="form-control" value="<?php echo $book['year']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="shelf_id" class="form-label">Shelf Number</label>
            <select name="shelf_id" id="shelf_id" class="form-control" required>
                <option value="">Select a shelf</option>
                <?php foreach ($shelves as $shelf): ?>
                    <option value="<?php echo $shelf['id']; ?>" <?php if ($shelf['id'] == $book['shelf_id']) echo 'selected'; ?>><?php echo $shelf['shelf_number']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="book_image" class="form-label">Book Image</label>
            <input type="file" name="book_image" id="book_image" class="form-control" accept="image/*">
            <?php if (!empty($book['image_path'])): ?>
                <img src="../<?php echo $book['image_path']; ?>" alt="Book Image" class="img-thumbnail mt-2" style="width: 150px;">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Update Book</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
