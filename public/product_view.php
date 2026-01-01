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

/* Session info */
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId = $_SESSION['user_id'] ?? 0;
$userName = $_SESSION['name'] ?? 'User';
$userEmail = $_SESSION['email'] ?? 'user@email.com';

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
    <title><?= htmlspecialchars($p['name']) ?> - College Kart</title>
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
                    <p><strong><?= htmlspecialchars($userName) ?></strong></p>
                    <p><?= htmlspecialchars($userEmail) ?></p>
                    <hr>
                    <a href="auth/logout.php" class="text-danger fw-bold">Logout</a>
                </div>
            </div>
        <?php endif; ?>

        <h1>College Kart 🛒</h1>

        <nav>
            <?php if (!$userId): ?>
                <a href="auth/login.php">Login</a>
                <a href="auth/signup.php">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="item-box">

        <?php if ($purchaseSuccess): ?>
            <div class="alert alert-success">
                ✅ Purchase successful!
            </div>
            <a href="index.php" class="btn btn-secondary mb-3">⬅ Back to Store</a>
        <?php endif; ?>

        <img src="<?= htmlspecialchars($p['image']) ?>" alt="item image">

        <h2 class="mt-3"><?= htmlspecialchars($p['name']) ?></h2>

        <?php if ($isDonated): ?>
            <p class="badge bg-success fs-6">🎁 Donated Item</p>
        <?php else: ?>
            <p class="text-muted fs-5">Price: ₹<?= htmlspecialchars($p['price']) ?></p>
        <?php endif; ?>

        <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
        <p class="text-muted">Seller: <?= htmlspecialchars($p['seller']) ?></p>

        <!-- ACTION BUTTONS -->
        <?php if ($isSold): ?>

            <div class="alert alert-danger mt-3">
                ❌ This item has already been sold
            </div>

        <?php elseif (!$userId): ?>

            <a href="auth/login.php" class="btn-custom">Login to Continue</a>

        <?php elseif ($isOwner): ?>

            <div class="alert alert-info">
                ℹ You cannot purchase your own item
            </div>

        <?php else: ?>

            <form method="post" action="user/action.php">
                <input type="hidden" name="item_id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn-custom">
                    <?= $isDonated ? '🎁 Get Item' : '🛒 Buy Now' ?>
                </button>
            </form>

        <?php endif; ?>

    </div>

    <footer>
        © <?= date('Y') ?> College Kart | Built by Prashant & Ayush
    </footer>

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