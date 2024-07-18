<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/tcpdf/tcpdf.php';
startSession();

if (!isAdmin()) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $user_id = $_POST['user_id'];
        $penalty_type_id = $_POST['penalty_type_id'];
        $reason = $_POST['reason'];
        $sql = "INSERT INTO penalties (user_id, penalty_type_id, reason) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $user_id, $penalty_type_id, $reason);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $penalty_type_id = $_POST['penalty_type_id'];
        $reason = $_POST['reason'];
        $sql = "UPDATE penalties SET penalty_type_id = ?, reason = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $penalty_type_id, $reason, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM penalties WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['mark_paid'])) {
        $id = $_POST['id'];
        $sql = "UPDATE penalties SET paid = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['print_receipt'])) {
        $id = $_POST['id'];
        $sql = "SELECT penalties.*, users.email, penalty_types.name AS penalty_type, penalty_types.amount FROM penalties JOIN users ON penalties.user_id = users.id JOIN penalty_types ON penalties.penalty_type_id = penalty_types.id WHERE penalties.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $penalty = $result->fetch_assoc();
        $stmt->close();

        // Generate PDF receipt
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Kota Samarahan Public Library');
        $pdf->SetTitle('Payment Receipt');
        $pdf->SetSubject('Payment Receipt');
        $pdf->SetKeywords('TCPDF, PDF, receipt, payment');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        // Add title
        $pdf->Cell(0, 10, 'Payment Receipt', 0, 1, 'C');

        // Add penalty details
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'User Email: ' . $penalty['email'], 0, 1);
        $pdf->Cell(0, 10, 'Penalty Type: ' . $penalty['penalty_type'], 0, 1);
        $pdf->Cell(0, 10, 'Amount: ' . $penalty['amount'], 0, 1);
        $pdf->Cell(0, 10, 'Reason: ' . $penalty['reason'], 0, 1);
        $pdf->Cell(0, 10, 'Paid: Yes', 0, 1);
        $pdf->Cell(0, 10, 'Date: ' . date('Y-m-d'), 0, 1);

        // Output PDF
        $pdf->Output('payment_receipt_' . $id . '.pdf', 'I');
    }
}

$penalties = $conn->query("SELECT penalties.*, users.email, penalty_types.name AS penalty_type, penalty_types.amount FROM penalties JOIN users ON penalties.user_id = users.id JOIN penalty_types ON penalties.penalty_type_id = penalty_types.id");
$users = $conn->query("SELECT id, email FROM users");
$penalty_types = $conn->query("SELECT id, name FROM penalty_types");
?>

<?php include '../includes/header.php'; ?>
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
        <label for="reason" class="form-label">Reason</label>
        <input type="text" name="reason" id="reason" class="form-control" required>
    </div>
    <button type="submit" name="add" class="btn btn-primary">Add Penalty</button>
</form>

<h3>Existing Penalties</h3>
<table class="table">
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
                    <input type="hidden" name="reason" value="<?php echo $row['reason']; ?>">
                    <button type="submit" name="edit" class="btn btn-warning">Edit</button>
                </form>
                <form method="POST" action="" class="d-inline">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                </form>
                <form method="POST" action="" class="d-inline">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="mark_paid" class="btn btn-success">Mark as Paid</button>
                </form>
                <form method="POST" action="" class="d-inline">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="print_receipt" class="btn btn-info">Print Receipt</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
