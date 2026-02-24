<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include DB connection
require_once __DIR__ . '/../app/db.php';

// Logged-in user info
$userId = $_SESSION['user_id'] ?? 0;
$userName = $_SESSION['name'] ?? 'User';
if (!$userId) {
    header("Location: auth/login.php");
    exit;
}

// --- Fetch user listings (items the user is selling/donating) ---
$listings = [];
$sqlListings = "SELECT id, name, price, type, status, created_at FROM items WHERE seller_id = ? ORDER BY created_at DESC";
$stmtListings = mysqli_prepare($conn, $sqlListings);
if ($stmtListings) {
    mysqli_stmt_bind_param($stmtListings, "i", $userId);
    mysqli_stmt_execute($stmtListings);
    $resListings = mysqli_stmt_get_result($stmtListings);
    if ($resListings) {
        while ($row = mysqli_fetch_assoc($resListings)) {
            $listings[] = $row;
        }
    }
    mysqli_stmt_close($stmtListings);
}

// --- Fetch transactions where user is buyer ---
$transactions = [];
$errorMessage = '';
$sqlTransactions = "SELECT t.id, t.type, t.status, t.created_at, i.name AS item_name, i.price AS item_price
                    FROM transactions t
                    JOIN items i ON t.item_id = i.id
                    WHERE t.buyer_id = ?
                    ORDER BY t.created_at DESC";
$stmtTransactions = mysqli_prepare($conn, $sqlTransactions);
if ($stmtTransactions) {
    mysqli_stmt_bind_param($stmtTransactions, "i", $userId);
    mysqli_stmt_execute($stmtTransactions);
    $resTransactions = mysqli_stmt_get_result($stmtTransactions);
    if ($resTransactions) {
        while ($row = mysqli_fetch_assoc($resTransactions)) {
            $transactions[] = $row;
        }
    }
    mysqli_stmt_close($stmtTransactions);
} else {
    $errorMessage = "Database query failed: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - College Kart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6fb;
        }

        header {
            background: linear-gradient(90deg, #111, #6366f1);
            color: #fff;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav a {
            color: #fff;
            margin-left: 25px;
            text-decoration: none;
        }

        .profile-container {
            padding: 50px 20px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .section-card {
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .section-title {
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <header>
        <h1>College Kart 🛒</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="marketplace.php">Marketplace</a>
            <a href="profile.php">👤
                <?= htmlspecialchars($userName) ?>
            </a>
            <a href="auth/logout.php">Logout</a>
        </nav>
    </header>

    <div class="container profile-container">

        <!-- User Details -->
        <div class="section-card">
            <h2 class="section-title">User Details</h2>
            <p><strong>Name:</strong>
                <?= htmlspecialchars($userName) ?>
            </p>
            <p><strong>User ID:</strong>
                <?= $userId ?>
            </p>
            <!-- Add email or phone if available -->
        </div>

        <!-- User Listings -->
        <div class="section-card">
            <h2 class="section-title">My Listings</h2>
            <?php if (!empty($listings)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Item</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th>Listed On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listings as $l): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($l['name']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars(ucfirst($l['type'])) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars(ucfirst($l['status'])) ?>
                                    </td>
                                    <td>₹
                                        <?= number_format($l['price'], 2) ?>
                                    </td>
                                    <td>
                                        <?= date('d M Y, H:i', strtotime($l['created_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>You have no listings yet.</p>
            <?php endif; ?>
        </div>

        <!-- User Purchases -->
        <div class="section-card">
            <h2 class="section-title">My Purchases</h2>

            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger">
                    <?= $errorMessage ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($transactions)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Item</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th>Purchased On</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $t): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($t['item_name']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars(ucfirst($t['type'])) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars(ucfirst($t['status'])) ?>
                                    </td>
                                    <td>₹
                                        <?= number_format($t['item_price'], 2) ?>
                                    </td>
                                    <td>
                                        <?= date('d M Y, H:i', strtotime($t['created_at'])) ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#receiptModal<?= $t['id'] ?>">View Receipt</button>

                                        <!-- Receipt Modal -->
                                        <div class="modal fade" id="receiptModal<?= $t['id'] ?>" tabindex="-1"
                                            aria-labelledby="receiptLabel<?= $t['id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="receiptLabel<?= $t['id'] ?>">Receipt -
                                                            <?= htmlspecialchars($t['item_name']) ?>
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Item:</strong>
                                                            <?= htmlspecialchars($t['item_name']) ?>
                                                        </p>
                                                        <p><strong>Type:</strong>
                                                            <?= htmlspecialchars(ucfirst($t['type'])) ?>
                                                        </p>
                                                        <p><strong>Status:</strong>
                                                            <?= htmlspecialchars(ucfirst($t['status'])) ?>
                                                        </p>
                                                        <p><strong>Price:</strong> ₹
                                                            <?= number_format($t['item_price'], 2) ?>
                                                        </p>
                                                        <p><strong>Purchased On:</strong>
                                                            <?= date('d M Y, H:i', strtotime($t['created_at'])) ?>
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>You have no purchases yet.</p>
            <?php endif; ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>