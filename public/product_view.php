<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';

if (!isset($_GET['id'])) {
    die("Item ID not specified.");
}

$id = (int) $_GET['id'];

$q = mysqli_query($conn, "
    SELECT items.*, users.name AS seller 
    FROM items 
    JOIN users ON items.seller_id = users.id
    WHERE items.id = $id
");

if (mysqli_num_rows($q) == 0) {
    die("Item not found.");
}

$p = mysqli_fetch_assoc($q);

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId = $_SESSION['user_id'] ?? 0;
$userName = $_SESSION['name'] ?? 'User';

/* Fetch logged in user email */
$userEmail = '';
if ($userId) {
    $stmt = mysqli_prepare($conn, "SELECT email FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        $userEmail = $row['email'];
    }
    mysqli_stmt_close($stmt);
}

/* Item states */
$isSold = isset($p['status']) && $p['status'] === 'sold';
$isDonated = (int) $p['price'] === 0;
$purchaseSuccess = isset($_GET['success']) && $_GET['success'] == 1;
$isOwner = $userId && $userId == $p['seller_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>
        <?= htmlspecialchars($p['name']) ?> - College Kart
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(90deg, #4CAF50, #388E3C);
            color: white;
            padding: 20px 0;
            text-align: center;
            position: relative;
        }

        nav a {
            color: white;
            margin: 0 10px;
            font-weight: 500;
            text-decoration: none;
        }

        .profile-container {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .profile-dropdown {
            display: none;
            position: absolute;
            right: 0;
            background: #fff;
            color: #000;
            border-radius: 8px;
            padding: 12px;
            width: 200px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
            z-index: 10;
        }

        .item-box {
            max-width: 720px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .item-box img {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            border-radius: 10px;
        }

        .btn-custom {
            background: linear-gradient(135deg, #ff9800, #ff5722);
            color: #fff;
            border-radius: 30px;
            padding: 12px 35px;
            border: none;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-custom:hover {
            color: #fff;
            opacity: 0.9;
        }

        footer {
            background: #111;
            color: #fff;
            text-align: center;
            padding: 18px;
            margin-top: auto;
        }
    </style>
</head>

<body>

    <header>
        <?php if ($userId): ?>
            <div class="profile-container">
                <div onclick="toggleProfile()" style="cursor:pointer;font-size:24px;">👤</div>
                <div class="profile-dropdown" id="profileBox">
                    <p><strong>
                            <?= htmlspecialchars($userName) ?>
                        </strong></p>
                    <p>
                        <?= htmlspecialchars($userEmail) ?>
                    </p>
                    <hr>
                    <a href="auth/logout.php" class="text-danger fw-bold">Logout</a>
                </div>
            </div>
        <?php endif; ?>
        <h1>College Kart 🛒</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="marketplace.php">Marketplace</a>
        </nav>
    </header>

    <div class="item-box">

        <?php if ($purchaseSuccess): ?>
            <div class="alert alert-success">✅ Purchase successful!</div>
        <?php endif; ?>

        <img src="<?= htmlspecialchars($p['image']) ?>" alt="item image">

        <h2 class="mt-3">
            <?= htmlspecialchars($p['name']) ?>
        </h2>

        <?php if ($isDonated): ?>
            <p class="badge bg-success fs-6">Donated Item</p>
        <?php else: ?>
            <p class="text-muted fs-5">Price: ₹
                <?= htmlspecialchars($p['price']) ?>
            </p>
        <?php endif; ?>

        <p>
            <?= nl2br(htmlspecialchars($p['description'])) ?>
        </p>
        <p class="text-muted">Seller:
            <?= htmlspecialchars($p['seller']) ?>
        </p>

        <div class="mt-3">

            <a href="index.php" class="btn btn-secondary mb-3">⬅ Back to Store</a>

            <!-- ACTION BUTTONS -->
            <?php if ($isSold): ?>
                <div class="alert alert-danger mt-3">This item has already been sold</div>

            <?php elseif ($p['status'] === 'booked'): ?>
                <span class="badge bg-warning fs-6">Booked</span>

            <?php elseif (!$userId): ?>
                <a href="auth/login.php" class="btn-custom">Login to Continue</a>

            <?php elseif ($isOwner): ?>
                <div class="alert alert-info mt-3">ℹ You cannot purchase your own item</div>

            <?php else: ?>
                <!-- BUY BUTTON -->
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#buyModal">
                    <?= $isDonated ? '🎁 Get Item' : '🛒 Buy Now' ?>
                </button>
            <?php endif; ?>

        </div>
    </div>

    <!-- BUY MODAL -->
    <div class="modal fade" id="buyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Confirm Purchase</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form method="post" action="user/action.php">
                    <div class="modal-body">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <input type="hidden" name="action_type" value="<?= $isDonated ? 'get' : 'buy' ?>">

                        <h6>Item Details</h6>
                        <p><strong>Item:</strong>
                            <?= htmlspecialchars($p['name']) ?>
                        </p>
                        <p><strong>Price:</strong>
                            <?= $isDonated ? 'Free' : '₹' . htmlspecialchars($p['price']) ?>
                        </p>

                        <hr>

                        <h6>Buyer Details</h6>
                        <p><strong>Name:</strong>
                            <?= htmlspecialchars($userName) ?>
                        </p>
                        <p><strong>Email:</strong>
                            <?= htmlspecialchars($userEmail) ?>
                        </p>

                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">Select Payment</option>
                                <option value="Cash">Cash</option>
                                <option value="eSewa">eSewa</option>
                                <option value="Khalti">Khalti</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pickup Location</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Confirm</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <footer>
        ©
        <?= date('Y') ?> College Kart | Built by Prashant & Ayush
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleProfile() {
            const box = document.getElementById("profileBox");
            box.style.display = box.style.display === "block" ? "none" : "block";
        }
        document.addEventListener("click", function (e) {
            const profile = document.querySelector(".profile-container");
            if (profile && !profile.contains(e.target)) {
                document.getElementById("profileBox").style.display = "none";
            }
        });
    </script>

</body>

</html>