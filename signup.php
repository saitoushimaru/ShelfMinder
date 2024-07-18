<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <style>
        .alert {
            position: relative;
            padding: 1rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: .375rem;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }
        .qr-image {
            width: 250px; /* Adjust the width as needed */
            height: 250px; /* Adjust the height as needed */
            display: block;
            margin: 0 auto; /* Center the image */
        }
    </style>
</head>
<body>
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;

startSession();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $age_group = $_POST['age_group'];
    $membership_fee = ($age_group == 'Children') ? 2 : 3;

    // Ensure files are uploaded
    if (isset($_FILES['ic_document']) && isset($_FILES['receipt_document'])) {
        $ic_document = $_FILES['ic_document']['name'];
        $receipt_document = $_FILES['receipt_document']['name'];
        $ic_target_dir = "uploads/ic/";
        $receipt_target_dir = "uploads/receipts/";
        $ic_target_file = $ic_target_dir . basename($ic_document);
        $receipt_target_file = $receipt_target_dir . basename($receipt_document);
        $upload_ok = 1;
        $image_file_type = strtolower(pathinfo($ic_target_file, PATHINFO_EXTENSION));
        $receipt_file_type = strtolower(pathinfo($receipt_target_file, PATHINFO_EXTENSION));

        // Check if file is an actual image or fake image
        $check = getimagesize($_FILES['ic_document']['tmp_name']);
        if($check !== false) {
            $upload_ok = 1;
        } else {
            $message = "<div class='alert alert-danger'>IC document is not an image.</div>";
            $upload_ok = 0;
        }

        // Check if receipt is a valid image or pdf
        if($receipt_file_type != "jpg" && $receipt_file_type != "png" && $receipt_file_type != "jpeg" && $receipt_file_type != "pdf" ) {
            $message = "<div class='alert alert-danger'>Sorry, only JPG, JPEG, PNG & PDF files are allowed for receipt.</div>";
            $upload_ok = 0;
        }

        // Check file size
        if ($_FILES['ic_document']['size'] > 500000 || $_FILES['receipt_document']['size'] > 2000000) {
            $message = "<div class='alert alert-danger'>Sorry, your file is too large.</div>";
            $upload_ok = 0;
        }

        // Check if $upload_ok is set to 0 by an error
        if ($upload_ok == 0) {
            $message = "<div class='alert alert-danger'>Sorry, your file was not uploaded.</div>";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES['ic_document']['tmp_name'], $ic_target_file) && move_uploaded_file($_FILES['receipt_document']['tmp_name'], $receipt_target_file)) {
                // Check if email already exists
                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $message = "<div class='alert alert-danger'>Error: Email already exists. Please use a different email.</div>";
                } else {
                    // Generate a unique 7-digit numerical user ID
                    $user_id = generateUniqueUserId($conn);
                    
                    $sql = "INSERT INTO users (user_id, name, email, password, age_group, membership_fee, ic_document, receipt_document, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssisss", $user_id, $name, $email, $password, $age_group, $membership_fee, $ic_document, $receipt_document);
                    
                    if ($stmt->execute()) {
                        // Generate QR code for payment
                        $paymentData = "PAYMENT_INFO_FOR_$user_id"; // Replace with actual payment info
                        $qrCode = new QrCode($paymentData);
                        $qrCode->setSize(300);
                        $qrCodePath = __DIR__ . "/qr_codes/payment_$user_id.png";
                        $qrCode->writeFile($qrCodePath);

                        $message = "<div class='alert alert-success'>User registered successfully. Your User ID is $user_id. Please complete your payment by scanning the QR code below.</div>";
                        $message .= "<img src='qr_codes/payment_$user_id.png' alt='Payment QR Code' class='img-thumbnail'>";
                    } else {
                        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                    }

                    $stmt->close();
                }
            } else {
                $message = "<div class='alert alert-danger'>Sorry, there was an error uploading your files.</div>";
            }
        }
    } else {
        $message = "<div class='alert alert-danger'>Both IC and receipt documents are required.</div>";
    }
}

function generateUniqueUserId($conn) {
    do {
        $user_id = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } while ($result->num_rows > 0);
    return $user_id;
}
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <header class="card-header">
                    <a href="login.php" class="float-right btn btn-outline-primary mt-1">Login</a>
                    <h4 class="card-title mt-2">Sign Up</h4>
                </header>
                <article class="card-body">
                    <?php if (!empty($message)) { echo $message; } ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="age_group" class="form-label">Age Group</label>
                            <select class="form-control" id="age_group" name="age_group" required>
                                <option value="Children">6-12 (Children) - RM2</option>
                                <option value="Adults">13-18 (Adults) - RM3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ic_document" class="form-label">Identification Document (IC)</label>
                            <input type="file" class="form-control" id="ic_document" name="ic_document" required>
                        </div>
                        <div class="form-group text-center">
                            <h5>Scan to Pay</h5>
                            <img src="assets/media/duitnow_qr.png" alt="DuitNow QR Code" class="img-thumbnail qr-image">
                        </div>
                        <div class="form-group">
                            <label for="receipt_document" class="form-label">Payment Receipt (Image/PDF)</label>
                            <input type="file" class="form-control" id="receipt_document" name="receipt_document" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
                    </form>
                </article>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
