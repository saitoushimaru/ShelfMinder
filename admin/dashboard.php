<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
startSession();

if (!isAdmin()) {
    header("Location: login.php");
    exit();
}
?>
<?php include '../includes/header.php'; ?>
<h2>Admin Dashboard</h2>
<p>Welcome, Admin!</p>
<div class="container">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card hover-card">
                <a href="approve_user.php" class="card-link">
                    <div class="content">
                        <div class="img"><img src="../assets/icons/approveuser.png" alt="Approve Users"></div>
                        <div class="details">
                            <div class="name">Approve Users</div>
                            <div class="job">Approve or reject user registrations.</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card hover-card card-toggle">
                <div class="content">
                    <div class="img"><img src="../assets/icons/manageinventory.png" alt="Manage Inventory"></div>
                    <div class="details">
                        <div class="name">Manage Inventory</div>
                        <div class="job">Manage books and shelves in the library.</div>
                    </div>
                </div>
                <ul class="card-options">
                    <li><a class="dropdown-item" href="manage_books.php">Manage Books</a></li>
                    <li><a class="dropdown-item" href="manage_shelves.php">Manage Shelves</a></li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card hover-card card-toggle">
                <div class="content">
                    <div class="img"><img src="../assets/icons/manageuser.png" alt="Manage Users"></div>
                    <div class="details">
                        <div class="name">Manage Users</div>
                        <div class="job">Ban members, reset passwords, and manage user details.</div>
                    </div>
                </div>
                <ul class="card-options">
                    <li><a class="dropdown-item" href="manage_users.php">Manage Users</a></li>
                    <li><a class="dropdown-item" href="manage_staff.php">Manage Staff</a></li>
                    <li><a class="dropdown-item" href="ban_member.php">Ban Member</a></li>
                    <li><a class="dropdown-item" href="reset_password.php">Reset User Password</a></li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card hover-card card-toggle">
                <div class="content">
                    <div class="img"><img src="../assets/icons/transactionhistory.png" alt="Transaction History"></div>
                    <div class="details">
                        <div class="name">Transaction History</div>
                        <div class="job">View the transaction history of all users.</div>
                    </div>
                </div>
                <ul class="card-options">
                    <li><a class="dropdown-item" href="transaction_history.php">Transaction History</a></li>
                    <li><a class="dropdown-item" href="lending_reports.php">Lending Reports</a></li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card hover-card card-toggle">
                <div class="content">
                    <div class="img"><img src="../assets/icons/managepenalty.png" alt="Manage Penalties"></div>
                    <div class="details">
                        <div class="name">Manage Penalties</div>
                        <div class="job">Manage penalties for users.</div>
                    </div>
                </div>
                <ul class="card-options">
                    <li><a class="dropdown-item" href="manage_penalties.php">Manage Penalties</a></li>
                    <li><a class="dropdown-item" href="manage_penalty_types.php">Manage Penalty Types</a></li>
                    <li><a class="dropdown-item" href="manage_penalty_settings.php">Manage Penalty Settings</a></li>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card hover-card fade-card">
                <a href="../logout.php" class="card-link">
                    <div class="content">
                        <div class="img"><i class="bi bi-box-arrow-right" style="font-size: 60px;"></i></div>
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
}

.card .content .details .job {
    color: #777;
}

.card-options {
    display: none;
    list-style: none;
    padding: 0;
    margin: 0;
    position: absolute;
    bottom: 0;
    width: 100%;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.card-options li {
    border-top: 1px solid #ddd;
}

.card-options li a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #333;
}

.card-options li a:hover {
    background-color: #f0f0f0;
}

.card-link {
    text-decoration: none;
    color: inherit;
}

.card-link:hover {
    text-decoration: none;
    color: inherit;
}

.card-toggle {
    cursor: pointer;
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

.fade-card:hover .job {
    color: white;
    
}

.fade-card .content .btn {
    transition: background-color 0.5s, color 0.5s;
}

.fade-card .content .btn:hover {
    background-color: #f00;
    color: white;
}
</style>

<script>
document.querySelectorAll('.card-toggle').forEach(card => {
    card.addEventListener('click', () => {
        const cardOptions = card.querySelector('.card-options');
        cardOptions.style.display = cardOptions.style.display === 'block' ? 'none' : 'block';
    });
});
</script>
