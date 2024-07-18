<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = getUserDetails($user_id);
?>

<?php include '../includes/header.php'?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>
            <p>Welcome, <?php echo $user['name']; ?>!</p>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card hover-card">
                        <a href="view_books.php" class="card-link">
                            <div class="content">
                                <div class="img"><img src="../assets/icons/viewbook.png" alt="View Books"></div>
                                <div class="details">
                                    <div class="name">View Books</div>
                                    <div class="job">Browse the library's book collection.</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card hover-card">
                        <a href="reserve_book.php" class="card-link">
                            <div class="content">
                                <div class="img"><img src="../assets/icons/reserved.png" alt="Reserve Books"></div>
                                <div class="details">
                                    <div class="name">Reserve Books</div>
                                    <div class="job">Reserve books for later pickup.</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card hover-card">
                        <a href="notifications.php" class="card-link">
                            <div class="content">
                                <div class="img"><img src="../assets/icons/notification.gif" alt="Notifications"></div>
                                <div class="details">
                                    <div class="name">Notifications</div>
                                    <div class="job">Check your notifications and alerts.</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card hover-card">
                        <a href="penalties.php" class="card-link">
                            <div class="content">
                                <div class="img"><img src="../assets/icons/penalty.png" alt="View Penalties"></div>
                                <div class="details">
                                    <div class="name">View Penalties</div>
                                    <div class="job">Check your penalties and fines.</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card hover-card">
                        <a href="edit_profile.php" class="card-link">
                            <div class="content">
                                <div class="img"><img src="../assets/icons/editprofile.png" alt="Edit Profile"></div>
                                <div class="details">
                                    <div class="name">Edit Profile</div>
                                    <div class="job">Update your personal information.</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card hover-card fade-card">
                        <a href="/dbshelfminder/logout.php" class="card-link">
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
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace()
</script>
</body>
</html>

<style>
/* CSS for responsive cards */
.card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.5s, color 0.5s;
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
    color: white !important;
}

.fade-card .btn {
    transition: background-color 0.5s, color 0.5s;
}

.fade-card .btn:hover {
    background-color: #f00;
    color: white;
}
</style>
<?php include '../includes/footer.php'; ?>
