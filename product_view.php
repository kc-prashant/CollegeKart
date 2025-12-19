<?php
session_start();
include "db.php";

if (!isset($_GET['id'])) {
    die("Item ID not specified.");
}

$id = (int) $_GET['id']; // sanitize input
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

// Session info
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId = $_SESSION['user_id'] ?? 0;
$userName = $_SESSION['name'] ?? 'User';
$userEmail = $_SESSION['email'] ?? 'user@email.com';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($p['name']) ?> - Clz Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
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

        header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #ffeb3b;
        }

        /* PROFILE */
        .profile-container {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .profile-icon {
            font-size: 26px;
            cursor: pointer;
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
            margin-top: 8px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
            text-align: left;
            z-index: 10;
        }

        .profile-dropdown p {
            margin: 4px 0;
            font-size: 14px;
        }

        .profile-dropdown a {
            color: red;
            text-decoration: none;
            font-weight: 600;
        }

        .item-box {
            max-width: 700px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .item-box img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .btn-custom {
            background: linear-gradient(135deg, #ff9800, #ff5722);
            color: #fff;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 30px;
            box-shadow: 0 6px 15px rgba(255, 87, 34, 0.4);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-custom:hover {
            background: linear-gradient(135deg, #ff5722, #ff9800);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 87, 34, 0.6);
            color: #fff;
        }

        footer {
            background: #111;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }
    </style>
</head>

<body>

    <header>

        <?php if ($userId): ?>
            <div class="profile-container">
                <div class="profile-icon" onclick="toggleProfile()">👤</div>
                <div class="profile-dropdown" id="profileBox">
                    <p><strong><?= htmlspecialchars($userName) ?></strong></p>
                    <p><?= htmlspecialchars($userEmail) ?></p>
                    <hr>
                    <a href="auth/logout.php">Logout</a>
                </div>
            </div>
        <?php endif; ?>

        <h1>Welcome to College Kart 🛒</h1>

        <nav>
            <?php if (!$userId): ?>
                <a href="auth/login.php">Login</a>
                <a href="auth/signup.php">Sign Up</a>
            <?php endif; ?>
        </nav>

    </header>

    <div class="item-box">
        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        <h1><?= htmlspecialchars($p['name']) ?></h1>
        <p class="text-muted">Price: ₹<?= htmlspecialchars($p['price']) ?></p>
        <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
        <p class="text-muted">Seller: <?= htmlspecialchars($p['seller']) ?></p>

        <?php if (!$userId): ?>
            <a href="auth/login.php" class="btn-custom">Login to Buy</a>
        <?php else: ?>
            <form method="post" action="user/buy.php">
                <input type="hidden" name="items_id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn-custom">Buy</button>
            </form>
        <?php endif; ?>
    </div>

    <footer>
        <p>© <?= date('Y') ?> College Kart | Built by Prashant and Ayush</p>
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