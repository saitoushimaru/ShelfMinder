<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

// Determine the sorting order
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'title';
$sort_order = isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc' ? 'desc' : 'asc';
$search_term = isset($_GET['search_term']) ? $_GET['search_term'] : '';

// Fetch all books with sorting and search
$sql = "SELECT books.*, shelves.shelf_number AS shelf_location 
        FROM books 
        LEFT JOIN shelves ON books.shelf_id = shelves.id 
        WHERE books.title LIKE ? OR books.author LIKE ? OR books.genre LIKE ? OR books.year LIKE ?
        ORDER BY $sort_by $sort_order";
$stmt = $conn->prepare($sql);
$search_param = "%{$search_term}%";
$stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}
$stmt->close();

// Fetch all shelves for the dropdown
$shelves_sql = "SELECT id, shelf_number FROM shelves";
$shelves_result = $conn->query($shelves_sql);

// Handle form submission for assigning shelf
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_shelf'])) {
    $selected_books = $_POST['books'];
    $shelf_id = $_POST['shelf_id'];

    $conn->begin_transaction();
    try {
        foreach ($selected_books as $book_id) {
            $book_id = (int)$book_id;
            $shelf_id = (int)$shelf_id;

            $sql = "UPDATE books SET shelf_id = $shelf_id WHERE id = $book_id";
            if (!$conn->query($sql)) {
                throw new Exception($conn->error);
            }
        }
        $conn->commit();
        $message = "Books assigned to shelf successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error assigning books to shelf: " . $e->getMessage();
    }
}

// Determine the next sort order
function getSortOrder($current_order) {
    return $current_order == 'asc' ? 'desc' : 'asc';
}

$next_sort_order = getSortOrder($sort_order);

?>
<?php include '../includes/header.php'; ?>
<div class="container">
    <h2>View Books</h2>
    <?php if (!empty($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
    <form method="GET" action="">
        <div class="form-group">
            <label for="search_term">Search:</label>
            <input type="text" name="search_term" id="search_term" class="form-control" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Enter title, author, genre or year">
        </div>
        <div class="form-group">
            <label for="sort_by">Sort by:</label>
            <select name="sort_by" id="sort_by" class="form-control" onchange="this.form.submit()">
                <option value="title" <?php echo $sort_by == 'title' ? 'selected' : ''; ?>>Title</option>
                <option value="author" <?php echo $sort_by == 'author' ? 'selected' : ''; ?>>Author</option>
                <option value="genre" <?php echo $sort_by == 'genre' ? 'selected' : ''; ?>>Genre</option>
                <option value="year" <?php echo $sort_by == 'year' ? 'selected' : ''; ?>>Year</option>
            </select>
        </div>
        <div class="form-group">
            <label for="sort_order">Order:</label>
            <select name="sort_order" id="sort_order" class="form-control" onchange="this.form.submit()">
                <option value="asc" <?php echo $sort_order == 'asc' ? 'selected' : ''; ?>>Ascending</option>
                <option value="desc" <?php echo $sort_order == 'desc' ? 'selected' : ''; ?>>Descending</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
 

    <div class="row mt-4">
        <?php foreach ($books as $book): ?>
        <div class="col-md-4">
            <div class="card mb-4" style="width: 18rem;">
                <img src="../uploads/books/images (1).jpg" class="card-img-top" alt="Book Image">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $book['title']; ?></h5>
                    <p class="card-text">
                        <strong>Author:</strong> <?php echo $book['author']; ?><br>
                        <strong>Genre:</strong> <?php echo $book['genre']; ?><br>
                        <strong>Year:</strong> <?php echo $book['year']; ?><br>
                        <strong>ISBN:</strong> <?php echo $book['isbn']; ?><br>
                        <strong>Shelf Location:</strong> <?php echo isset($book['shelf_location']) ? $book['shelf_location'] : 'N/A'; ?>
                    </p>
                    <a href="#" class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
