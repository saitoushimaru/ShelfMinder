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
        $user_id = $_POST['user_id'];
        $penalty_type_id = $_POST['penalty_type_id'];
        $amount = $_POST['amount'];  // New field
        $reason = $_POST['reason'];
        $sql = "INSERT INTO penalties (user_id, penalty_type_id, amount, reason) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $user_id, $penalty_type_id, $amount, $reason);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $penalty_type_id = $_POST['penalty_type_id'];
        $amount = $_POST['amount'];  // New field
        $reason = $_POST['reason'];
        $sql = "UPDATE penalties SET penalty_type_id = ?, amount = ?, reason = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisi", $penalty_type_id, $amount, $reason, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM penalties WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all penalties
$sql = "SELECT penalties.*, users.email, penalty_types.name AS penalty_type, penalty_types.amount 
        FROM penalties 
        JOIN users ON penalties.user_id = users.id 
        JOIN penalty_types ON penalties.penalty_type_id = penalty_types.id";
$penalties = $conn->query($sql);

// Fetch all users for the dropdown
$users = $conn->query("SELECT id, email FROM users");

// Fetch all penalty types for the dropdown
$penalty_types = $conn->query("SELECT id, name FROM penalty_types");
?>

<?php include '../includes/header.php'; ?>
<div class="container">
    <h2>Manage Penalties</h2>

    <form method="POST" action="">
        <h3>Add Penalty</h3>
        <div class="mb-3">
            <label for="user_id" class="form-label">User</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo $user['email']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="penalty_type_id" class="form-label">Penalty Type</label>
            <select name="penalty_type_id" id="penalty_type_id" class="form-control" required>
                <?php while ($penalty_type = $penalty_types->fetch_assoc()): ?>
                    <option value="<?php echo $penalty_type['id']; ?>"><?php echo $penalty_type['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="reason" class="form-label">Reason</label>
            <input type="text" name="reason" id="reason" class="form-control" required>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Add Penalty</button>
    </form>

    <h3>Existing Penalties</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>User Email</th>
                <th>Penalty Type</th>
                <th>Amount</th>
                <th>Reason</th>
                <th>Paid</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $penalties->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['penalty_type']; ?></td>
                <td><?php echo $row['amount']; ?></td>
                <td><?php echo $row['reason']; ?></td>
                <td><?php echo $row['paid'] ? 'Yes' : 'No'; ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <form method="POST" action="" class="d-inline">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="penalty_type_id" value="<?php echo $row['penalty_type_id']; ?>">
                        <input type="hidden" name="amount" value="<?php echo $row['amount']; ?>">
                        <input type="hidden" name="reason" value="<?php echo $row['reason']; ?>">
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
</div>
<?php include '../includes/footer.php'; ?>
