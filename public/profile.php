<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';

$userId = $_SESSION['user_id'] ?? 0;
$userName = $_SESSION['name'] ?? 'User';

if (!$userId) {
    header("Location: auth/login.php");
    exit;
}

/* ================= HANDLE CANCEL BOOKING ================= */
if (isset($_GET['cancel_id'])) {

    $cancelId = (int) $_GET['cancel_id'];

    // Fetch transaction to validate
    $stmtCheck = mysqli_prepare(
        $conn,
        "SELECT * FROM transactions WHERE id = ? AND buyer_id = ?"
    );
    mysqli_stmt_bind_param($stmtCheck, "ii", $cancelId, $userId);
    mysqli_stmt_execute($stmtCheck);
    $resCheck = mysqli_stmt_get_result($stmtCheck);
    $transaction = mysqli_fetch_assoc($resCheck);
    mysqli_stmt_close($stmtCheck);

    if ($transaction) {

        // Only allow cancel if booked
        if ($transaction['status'] === 'booked') {

            $stmtUpdate = mysqli_prepare(
                $conn,
                "UPDATE transactions SET status = 'cancelled' WHERE id = ?"
            );
            mysqli_stmt_bind_param($stmtUpdate, "i", $cancelId);
            mysqli_stmt_execute($stmtUpdate);
            mysqli_stmt_close($stmtUpdate);

            // Optionally, make item available again
            $stmtItem = mysqli_prepare(
                $conn,
                "UPDATE items SET status = 'available' WHERE id = ?"
            );
            mysqli_stmt_bind_param($stmtItem, "i", $transaction['item_id']);
            mysqli_stmt_execute($stmtItem);
            mysqli_stmt_close($stmtItem);

            header("Location: profile.php?msg=Booking cancelled successfully");
            exit;
        } else {
            header("Location: profile.php?msg=Cannot cancel this booking");
            exit;
        }
    } else {
        header("Location: profile.php?msg=Transaction not found");
        exit;
    }
}

/* ================= FETCH USER INFO ================= */
$stmtUser = mysqli_prepare($conn, "SELECT email FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmtUser, "i", $userId);
mysqli_stmt_execute($stmtUser);
$resUser = mysqli_stmt_get_result($stmtUser);
$userData = mysqli_fetch_assoc($resUser);
mysqli_stmt_close($stmtUser);

$userEmail = $userData['email'] ?? '';

/* ================= FETCH MY LISTINGS ================= */
$listings = [];
$stmtListings = mysqli_prepare($conn, "
    SELECT i.*, u.name AS buyer_name, u.email AS buyer_email
    FROM items i
    LEFT JOIN transactions t ON t.item_id = i.id
    LEFT JOIN users u ON t.buyer_id = u.id
    WHERE i.seller_id = ?
    ORDER BY i.created_at DESC
");
mysqli_stmt_bind_param($stmtListings, "i", $userId);
mysqli_stmt_execute($stmtListings);
$resListings = mysqli_stmt_get_result($stmtListings);
while ($row = mysqli_fetch_assoc($resListings)) {
    $listings[] = $row;
}
mysqli_stmt_close($stmtListings);

/* ================= FETCH PURCHASES ================= */
$transactions = [];
$stmtTransactions = mysqli_prepare($conn, "
    SELECT t.*, i.name AS item_name, i.price
    FROM transactions t
    JOIN items i ON t.item_id = i.id
    WHERE t.buyer_id = ?
    ORDER BY t.created_at DESC
");
mysqli_stmt_bind_param($stmtTransactions, "i", $userId);
mysqli_stmt_execute($stmtTransactions);
$resTransactions = mysqli_stmt_get_result($stmtTransactions);
while ($row = mysqli_fetch_assoc($resTransactions)) {
    $transactions[] = $row;
}
mysqli_stmt_close($stmtTransactions);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - College Kart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

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
            margin-left: 20px;
            text-decoration: none;
        }

        .card-box {
            background: #fff;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .05);
            margin-bottom: 15px;
        }

        .status-pill {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 600;
            color: #fff;
        }

        .status-available {
            background: #16a34a;
        }

        .status-booked {
            background: #d97706;
        }

        .status-cancelled {
            background: #dc2626;
        }

        footer {
            background: #111;
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-top: 60px;
        }

        .section-title {
            font-weight: 700;
            margin: 40px 0 20px;
        }
    </style>
</head>

<body>

    <header>
        <h3>College Kart 🛒</h3>
        <nav>
            <a href="index.php">Home</a>
            <a href="marketplace.php">Marketplace</a>
            <a href="profile.php">👤
                <?= htmlspecialchars($userName) ?>
            </a>
            <a href="auth/logout.php">Logout</a>
        </nav>
    </header>

    <div class="container py-4">

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <h4>Welcome,
            <?= htmlspecialchars($userName) ?> 👋
        </h4>

        <div class="card-box">
            <p><strong>Name:</strong>
                <?= htmlspecialchars($userName) ?>
            </p>
            <p><strong>Email:</strong>
                <?= htmlspecialchars($userEmail) ?>
            </p>
        </div>

        <!-- ================= MY LISTINGS ================= -->
        <h4 class="section-title">My Listings</h4>

        <?php if ($listings): ?>
            <?php foreach ($listings as $l): ?>
                <div class="card-box">
                    <strong>
                        <?= htmlspecialchars($l['name']) ?>
                    </strong>
                    <span class="status-pill status-<?= htmlspecialchars($l['status']) ?>">
                        <?= ucfirst($l['status']) ?>
                    </span>

                    <?php if ($l['price'] > 0): ?>
                        <div class="mt-2 text-primary fw-bold">
                            Rs.
                            <?= number_format($l['price'], 2) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($l['buyer_name'])): ?>
                        <div class="mt-3 p-3 bg-light rounded">
                            <strong>Buyer Details:</strong><br>
                            Name:
                            <?= htmlspecialchars($l['buyer_name']) ?><br>
                            Email:
                            <?= htmlspecialchars($l['buyer_email']) ?>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No listings yet.</p>
        <?php endif; ?>

        <!-- ================= PURCHASES ================= -->
        <h4 class="section-title">Items I Purchased</h4>

        <?php if ($transactions): ?>
            <?php foreach ($transactions as $t): ?>
                <div class="card-box">
                    <strong>
                        <?= htmlspecialchars($t['item_name']) ?>
                    </strong>
                    <div class="mt-2 mb-2">
                        <span class="badge bg-secondary">
                            <?= ucfirst($t['status']) ?>
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap mt-2">

                        <?php if ($t['status'] === 'booked'): ?>
                            <a href="profile.php?cancel_id=<?= $t['id'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to cancel this booking?')">
                                Cancel Booking
                            </a>
                        <?php elseif ($t['status'] === 'cancelled'): ?>
                            <span class="text-warning fw-bold">Booking Cancelled</span>
                        <?php endif; ?>

                        <button class="btn btn-primary btn-sm viewReceiptBtn" data-id="<?= $t['id'] ?>"
                            data-item="<?= htmlspecialchars($t['item_name']) ?>" data-type="<?= htmlspecialchars($t['type']) ?>"
                            data-status="<?= htmlspecialchars($t['status']) ?>"
                            data-price="<?= number_format($t['price'], 2) ?>"
                            data-date="<?= date('d M Y, H:i', strtotime($t['created_at'])) ?>" data-bs-toggle="modal"
                            data-bs-target="#receiptModal">
                            View Receipt
                        </button>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No purchases yet.</p>
        <?php endif; ?>

        <!-- ================= RECEIPT MODAL ================= -->
        <div class="modal fade" id="receiptModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Transaction Receipt</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="printArea">
                            <h5 class="text-center">College Kart 🛒</h5>
                            <hr>
                            <p><strong>Receipt ID:</strong> CK-<span id="rId"></span></p>
                            <p><strong>Item:</strong> <span id="rItem"></span></p>
                            <p><strong>Type:</strong> <span id="rType"></span></p>
                            <p><strong>Status:</strong> <span id="rStatus"></span></p>
                            <p><strong>Price:</strong> Rs. <span id="rPrice"></span></p>
                            <p><strong>Date:</strong> <span id="rDate"></span></p>
                            <hr>
                            <p class="text-center" style="font-size:13px;">
                                Thank you for using College Kart ❤️
                            </p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success" onclick="printReceipt()">🖨 Print</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

        <footer>
            ©
            <?= date('Y') ?> College Kart | Built by Prashant & Aayush
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            document.querySelectorAll('.viewReceiptBtn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById("rId").textContent = this.dataset.id;
                    document.getElementById("rItem").textContent = this.dataset.item;
                    document.getElementById("rType").textContent = this.dataset.type;
                    document.getElementById("rStatus").textContent = this.dataset.status;
                    document.getElementById("rPrice").textContent = this.dataset.price;
                    document.getElementById("rDate").textContent = this.dataset.date;
                });
            });

            function printReceipt() {
                var content = document.getElementById("printArea").innerHTML;
                var w = window.open('');
                w.document.write('<html><head><title>Receipt</title></head><body>' + content + '</body></html>');
                w.document.close();
                w.print();
            }
        </script>

</body>

</html>