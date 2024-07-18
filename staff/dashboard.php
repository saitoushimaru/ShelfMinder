<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isStaff()) {
    header("Location: ../login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];
?>
<?php include '../includes/header.php'; ?>
<h2>Staff Dashboard</h2>
<p>Welcome, Staff!</p>
<div class="container">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card hover-card">
                <a href="view_penalties.php" class="card-link">
                    <div class="content">
                        <div class="img"><img src="../assets/icons/penalty.png" alt="View Penalties"></div>
                        <div class="details">
                            <div class="name">View User Penalties</div>
                            <div class="job">Check the penalties assigned to users.</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card hover-card">
                <a href="view_reserved_books.php" class="card-link">
                    <div class="content">
                        <div class="img"><img src="../assets/icons/reserved.png" alt="View Reserved Books"></div>
                        <div class="details">
                            <div class="name">View Reserved Books</div>
                            <div class="job">See all the books that users have reserved.</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card hover-card">
                <a href="view_loaned_books.php" class="card-link">
                    <div class="content">
                        <div class="img"><img src="../assets/icons/loaned.png" alt="View Loaned Books"></div>
                        <div class="details">
                            <div class="name">View Loaned Books</div>
                            <div class="job">View the list of books currently loaned out.</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card hover-card">
                <a href="issue_book.php" class="card-link">
                    <div class="content">
                        <div class="img"><img src="../assets/icons/issue.png" alt="Issue Book"></div>
                        <div class="details">
                            <div class="name">Issue Book to User</div>
                            <div class="job">Issue a book to a user by scanning their barcode.</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card hover-card">
                <a href="return_book.php" class="card-link">
                    <div class="content">
                        <div class="img"><img src="../assets/icons/return.png" alt="Return Book"></div>
                        <div class="details">
                            <div class="name">Return Book</div>
                            <div class="job">Process the return of a book from a user.</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card hover-card fade-card">
                <a href="../logout.php" class="card-link">
                    <div class="content">
                        <div class="img"><i class="bi bi-box-arrow-right" style="font-size: 60px; color: black;"></i></div>
                        <div class="details">
                            <div class="name">Logout</div>
                            <div class="job">Logout from your account.</div>
                        </div>
                        
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>

<!-- Add necessary Bootstrap CSS and JS files -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.3/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<style>
/* CSS for responsive cards */
.cards {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    padding: 20px;
}

.card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    height: 250px; /* Set a fixed height for the cards */
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 16px rgba(0,0,0,0.2);
}

.card .content {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    width: 100%;
}

.card .content .img {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    margin-bottom: 20px;
}

.card .content .img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card .content .details {
    flex-grow: 1;
}

.card .content .details .name {
    font-size: 1.25em;
    font-weight: bold;
    margin-bottom: 10px;
    color: black;
}

.card .content .details .job {
    color: #777;
}

.card-link {
    text-decoration: none; /* Remove underline from links */
}

.card-link:hover {
    text-decoration: none; /* Remove underline from links on hover */
}

/* Fade card effect */
.fade-card {
    -webkit-transition: background-color 0.5s, color 0.5s;
    transition: background-color 0.5s, color 0.5s;
}

.fade-card:hover {
    background-color: #f00;
    color: white;
}

.fade-card:hover .name,
.fade-card:hover .job,
.fade-card:hover .img i {
    color: white;
}

.fade-card .btn {
    transition: background-color 0.5s, color 0.5s;
}

.fade-card .btn:hover {
    background-color: #f00;
    color: white;
}
</style>
