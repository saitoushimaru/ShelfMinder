<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/tcpdf/tcpdf.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}

// Fetch book details from the database
$bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bookId > 0) {
    $sql = "SELECT * FROM books WHERE id = $bookId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();

        // Create a new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Kota Samarahan Public Library');
        $pdf->SetTitle('Book Barcode');
        $pdf->SetSubject('Book Barcode');
        $pdf->SetKeywords('TCPDF, PDF, barcode, book, library');

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
        $pdf->Write(0, 'Book Barcode', '', 0, 'L', true, 0, false, false, 0);

        // Add book details
        $html = '<h2>' . $book['title'] . '</h2>';
        $html .= '<p>Author: ' . $book['author'] . '</p>';
        $html .= '<p>ISBN: ' . $book['isbn'] . '</p>';
        $pdf->writeHTML($html, true, false, true, false, '');

        // Generate and add barcode
        $barcode = $book['isbn']; // Assuming ISBN is used as the barcode content
        $pdf->write1DBarcode($barcode, 'C128', '', '', '', 18, 0.4, array('position' => 'S', 'align' => 'C', 'stretch' => false, 'fitwidth' => true, 'cellfitalign' => '', 'border' => true, 'hpadding' => 'auto', 'vpadding' => 'auto', 'fgcolor' => array(0,0,0), 'bgcolor' => false, 'text' => true, 'font' => 'helvetica', 'fontsize' => 8, 'stretchtext' => 4), 'N');

        // Close and output PDF document
        $pdf->Output('book_barcode_' . $bookId . '.pdf', 'I');
    } else {
        echo 'Book not found.';
    }
} else {
    echo 'Invalid book ID.';
}
?>
