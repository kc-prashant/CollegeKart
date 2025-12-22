<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "db.php";

// Check if user is admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId = $_SESSION['user_id'] ?? 0;
$userName = $_SESSION['name'] ?? 'User';
$userEmail = $_SESSION['email'] ?? 'user@email.com';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clz Store - Buy, Sell & Donate College Essentials</title>

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

        .hero {
            text-align: center;
            padding: 50px 20px;
            background: url('https://images.unsplash.com/photo-1581091012184-4449b3f0cbe0?auto=format&fit=crop&w=1400&q=60') no-repeat center center/cover;
            color: #fff;
        }

        @keyframes bounceInWord {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }

            50% {
                transform: scale(1.2);
                opacity: 1;
            }

            70% {
                transform: scale(0.9);
            }

            100% {
                transform: scale(1);
            }
        }

        .bounce-word {
            display: inline-block;
            color: #9b59b6;
            animation: bounceInWord 0.8s forwards;
        }

        @keyframes slideInUp {
            0% {
                transform: translateY(50px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .hero-subtext {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #9b59b6;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
            opacity: 0;
            animation: slideInUp 1s ease forwards;
            animation-delay: 1.8s;
        }

        .btn-custom {
            opacity: 0;
            animation: slideInUp 1s ease forwards;
            animation-delay: 2.2s;
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

        #latestHeading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background-color: #00b894;
            color: #fff;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 1.8rem;
            font-weight: 600;
            width: fit-content;
            margin: 0 auto 10px auto;
            animation: bounceInWord 1s forwards;
            animation-delay: 3s;
        }

        .items .card {
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            background-color: #1e1e1e;
            color: #fff;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .items .card:hover {
            transform: scale(1.03);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.5);
        }

        .items .card img {
            max-height: 200px;
            width: 100%;
            object-fit: contain;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            background: #2d2d2d;
        }

        .items .card .card-title {
            color: #fff;
        }

        .items .card p {
            color: #dfe6e9;
        }

        .items .card .btn-success {
            background-color: #6c5ce7;
            border: none;
        }

        .items .card .btn-success:hover {
            background-color: #a29bfe;
        }

        .items .card .btn-warning {
            background-color: #fd79a8;
            border: none;
        }

        .items .card .btn-warning:hover {
            background-color: #ffb6c1;
        }

        .items .card .btn-danger {
            background-color: #d63031;
            border: none;
        }

        .items .card .btn-danger:hover {
            background-color: #ff7675;
        }

        .items .card .btn-primary {
            background-color: #0984e3;
            border: none;
        }

        .items .card .btn-primary:hover {
            background-color: #74b9ff;
        }

        .items .card .btn-get {
            background-color: #00b894;
            border: none;
            color: #fff;
        }

        .items .card .btn-get:hover {
            background-color: #55efc4;
            color: #000;
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
                    <p><strong>
                            <?= htmlspecialchars($userName) ?>
                        </strong></p>
                    <p>
                        <?= htmlspecialchars($userEmail) ?>
                    </p>
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

    <section class="hero">
        <h2>
            <span class="bounce-word" style="animation-delay: 0s;">Buy</span> |
            <span class="bounce-word" style="animation-delay: 0.5s;">Sell</span> |
            <span class="bounce-word" style="animation-delay: 1s;">Donate</span>
        </h2>
        <p class="hero-subtext">Your one-stop shop for college essentials</p>

        <?php if ($userId): ?>
            <a href="add_item.php" class="btn-custom">Start Donating | Selling </a>
        <?php else: ?>
            <a href="auth/login.php" class="btn-custom">Login to Start Donating | Selling</a>
        <?php endif; ?>
    </section>

    <div class="container my-5">
        <div id="latestHeading">
            <span>Latest Items</span>
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="itemTypeDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Filter
                </button>
                <ul class="dropdown-menu" aria-labelledby="itemTypeDropdown">
                    <li><a class="dropdown-item" href="?type=sell">Selling Items</a></li>
                    <li><a class="dropdown-item" href="?type=donate">Donated Items</a></li>
                    <li><a class="dropdown-item" href="?type=all">All Items</a></li>
                </ul>
            </div>
        </div>

        <div class="row g-4 items">
            <?php
            $type = $_GET['type'] ?? 'all';
            $res = mysqli_query($conn, "
                SELECT items.*, users.name AS seller_name, users.id AS seller_id
                FROM items
                JOIN users ON items.seller_id = users.id
                ORDER BY items.id DESC
            ");
            if ($res && mysqli_num_rows($res) > 0):
                while ($p = mysqli_fetch_assoc($res)):
                    $isOwner = $userId && ($isAdmin || $userId == $p['seller_id']);
                    if ($type != 'all' && $p['type'] != $type)
                        continue;
                    ?>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="<?= $p['image'] ?>" class="card-img-top" alt="<?= $p['name'] ?>">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?= $p['name'] ?>
                                </h5>
                                <?php if ($p['type'] === 'sell'): ?>
                                    <p class="text-muted mb-1">Price: ₹
                                        <?= $p['price'] ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-muted mb-1">Available for Donation</p>
                                <?php endif; ?>
                                <p class="small">
                                    <?= $p['description'] ?>
                                </p>
                                <p class="small text-muted">Seller:
                                    <?= $p['seller_name'] ?>
                                </p>

                                <a href="product_view.php?id=<?= $p['id'] ?>" class="btn btn-success mt-2 me-2">View</a>

                                <?php if ($isOwner): ?>
                                    <a href="edit_item.php?id=<?= $p['id'] ?>" class="btn btn-warning mt-2 me-2">Edit</a>
                                    <a href="delete_item.php?id=<?= $p['id'] ?>" class="btn btn-danger mt-2 me-2"
                                        onclick="return confirm('Are you sure?')">Delete</a>
                                <?php endif; ?>

                                <?php if ($userId && $userId != $p['seller_id']): ?>
                                    <form method="post" action="user/action.php" style="display:inline-block; margin-top:5px;">
                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="action_type"
                                            value="<?= $p['type'] === 'sell' ? 'buy' : 'get' ?>">
                                        <button type="submit"
                                            class="btn mt-2 <?= $p['type'] === 'sell' ? 'btn-primary' : 'btn-get' ?>">
                                            <?= $p['type'] === 'sell' ? 'Buy' : 'Get' ?>
                                        </button>
                                    </form>
                                <?php elseif (!$userId): ?>
                                    <a href="auth/login.php" class="btn btn-primary mt-2">
                                        <?= $p['type'] === 'sell' ? 'Login to Buy' : 'Login to Get' ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; else: ?>
                <p class='text-center text-muted'>No items found. Be the first to add one!</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>©
            <?= date('Y') ?> College Kart | Built by Prashant and Ayush
        </p>
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