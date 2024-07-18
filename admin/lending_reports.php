<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

// Fetch all lending records
$sql = "SELECT l.id, l.book_id, b.title, l.user_id, u.name, l.borrowed_at, l.returned_at 
        FROM loans l
        JOIN books b ON l.book_id = b.id
        JOIN users u ON l.user_id = u.id";
$result = $conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['export_pdf'])) {
    require_once('../includes/tcpdf/tcpdf.php');

    // Create a new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Library Management System');
    $pdf->SetTitle('Lending Report');
    $pdf->SetSubject('Lending Report');
    $pdf->SetKeywords('TCPDF, PDF, lending, report, library');

    // Set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // Set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // Add a title
    $pdf->Write(0, 'Lending Report', '', 0, 'L', true, 0, false, false, 0);

    // Add lending report data
    $html = '<table border="1" cellpadding="4">';
    $html .= '<thead><tr><th>Transaction ID</th><th>Book Title</th><th>User Name</th><th>Borrowed Date</th><th>Returned Date</th></tr></thead><tbody>';
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td>' . $row['id'] . '</td>';
        $html .= '<td>' . $row['title'] . '</td>';
        $html .= '<td>' . $row['name'] . '</td>';
        $html .= '<td>' . $row['borrowed_at'] . '</td>';
        $html .= '<td>' . ($row['returned_at'] ? $row['returned_at'] : 'Not Returned') . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';

    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('lending_report.pdf', 'D');
    exit();
}
?>
<?php include '../includes/header.php'; ?>
<h2>Lending Reports</h2>
<form method="POST" action="">
    <button type="submit" name="export_pdf" class="btn btn-primary mb-3">Export to PDF</button>
</form>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Transaction ID</th>
            <th>Book Title</th>
            <th>User Name</th>
            <th>Borrowed Date</th>
            <th>Returned Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['borrowed_at']; ?></td>
            <td><?php echo ($row['returned_at'] ? $row['returned_at'] : 'Not Returned'); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>
