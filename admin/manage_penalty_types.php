<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $amount = $_POST['amount'];
        $sql = "INSERT INTO penalty_types (name, amount) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sd", $name, $amount);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $amount = $_POST['amount'];
        $sql = "UPDATE penalty_types SET name = ?, amount = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdi", $name, $amount, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM penalty_types WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

$penalty_types = $conn->query("SELECT * FROM penalty_types");
?>

<?php include '../includes/header.php'; ?>
<h2>Manage Penalty Types</h2>

<form method="POST" action="">
    <h3>Add Penalty Type</h3>
    <div class="mb-3">
        <label for="name" class="form-label">Penalty Name</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="amount" class="form-label">Amount</label>
        <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
    </div>
    <button type="submit" name="add" class="btn btn-primary">Add Penalty Type</button>
</form>

<h3>Existing Penalty Types</h3>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Amount</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $penalty_types->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['amount']; ?></td>
            <td>
                <form method="POST" action="" class="d-inline">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                    <input type="hidden" name="amount" value="<?php echo $row['amount']; ?>">
                    <button type="submit" name="edit" class="btn btn-warning">Edit</button>
                </form>
                <form method="POST" action="" class="d-inline">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
