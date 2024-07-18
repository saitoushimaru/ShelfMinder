<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/tcpdf/tcpdf.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

// Check if user ID is set in the URL
if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);
} else {
    echo "User ID is not set in the URL.";
    exit();
}

if ($userId > 0) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the query returned any results
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Create a new PDF document with custom size
            $pdf = new TCPDF('L', 'mm', array(85.6, 54), true, 'UTF-8', false);

            // Set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Kota Samarahan Public Library');
            $pdf->SetTitle('Library Card');
            $pdf->SetSubject('Library Card');
            $pdf->SetKeywords('TCPDF, PDF, library card, member, barcode');

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margins
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false, 0);

            // Add the first page
            $pdf->AddPage();

            // Use the provided image as a background template
            $background1 = '../assets/media/card-front.png'; // Adjust the path
            $background2 = '../assets/media/card-back.png'; // Adjust the path

            // Add the first background image
            $pdf->Image($background1, 0, 0, 85.6, 54, '', '', '', false, 300, '', false, false, 0);

            // Set font size for name, email, and user ID
            $pdf->SetFont('helvetica', '', 10);

            // Set position for the name
            $pdf->SetXY(35, 20); // Adjusted position
            $pdf->Cell(0, 10, 'Name: ' . htmlspecialchars($user['name']), 0, 1, 'L', false, '', 0, false, 'T', 'M');

            // Set position for the email
            $pdf->SetXY(35, 25); // Adjusted position
            $pdf->Cell(0, 10, 'Email: ' . htmlspecialchars($user['email']), 0, 1, 'L', false, '', 0, false, 'T', 'M');

            // Set position for the user ID
            $pdf->SetXY(35, 30); // Adjusted position
            $pdf->Cell(0, 10, 'User ID: ' . htmlspecialchars($user['user_id']), 0, 1, 'L', false, '', 0, false, 'T', 'M');

            // Add QR code on the first side (left side)
            $pdf->write2DBarcode(htmlspecialchars($user['user_id']), 'QRCODE,H', 5, 20.828, 25, 25, array('border' => false), 'N');

            // Add the second page
            $pdf->AddPage();

            // Add the second background image
            $pdf->Image($background2, 0, 0, 85.6, 54, '', '', '', false, 300, '', false, false, 0);

            // Center the barcode on the second side
            $barcode = htmlspecialchars($user['user_id']);
            $pdf->write1DBarcode($barcode, 'C128', 27, 32.274, 95, 15, 0.4, array('position' => 'S', 'align' => 'C', 'stretch' => false, 'fitwidth' => true, 'border' => false, 'hpadding' => 'auto', 'vpadding' => 'auto', 'fgcolor' => array(0, 0, 0), 'bgcolor' => false, 'text' => true, 'font' => 'helvetica', 'fontsize' => 8, 'stretchtext' => 4), 'N');

            // Close and output PDF document
            $pdf->Output('library_card_' . $userId . '.pdf', 'I');
        } else {
            echo 'User not found.';
        }
        $stmt->close();
    } else {
        echo 'Failed to prepare SQL statement.';
    }
} else {
    echo 'Invalid user ID.';
}

$conn->close();
?>
