<?php

session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';


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
    <title>College Kart - Vibrant Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* GENERAL STYLES*/
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            transition: 0.3s;
        }

        /* === HEADER === */
        header {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: #fff;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        nav a {
            color: #fff;
            margin-left: 20px;
            font-weight: 500;
        }

        nav a:hover {
            color: #ffeb3b;
        }

        .profile-container {
            position: relative;
            cursor: pointer;
        }

        .profile-icon {
            font-size: 28px;
        }

        .profile-dropdown {
            display: none;
            position: absolute;
            top: 40px;
            right: 0;
            background: #fff;
            color: #000;
            border-radius: 12px;
            padding: 15px;
            width: 220px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            text-align: left;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .profile-dropdown p {
            margin: 5px 0;
            font-size: 14px;
        }

        .profile-dropdown a {
            color: red;
            font-weight: 600;
        }

        /* HERO */
        .hero {
            background: linear-gradient(45deg, rgba(255, 87, 34, 0.7), rgba(255, 193, 7, 0.7)),
                url('https://images.unsplash.com/photo-1581091012184-4449b3f0cbe0?auto=format&fit=crop&w=1400&q=60') no-repeat center center/cover;
            color: #fff;
            text-align: center;
            padding: 100px 20px 80px;
        }

        .hero h2 {
            font-size: 2.6rem;
            margin-bottom: 20px;
        }

        .bounce-word {
            display: inline-block;
            font-weight: 700;
            animation: bounceIn 0.8s forwards;
            background: linear-gradient(45deg, #ff512f, #dd2476);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtext {
            font-size: 1.3rem;
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeInUp 1s forwards;
            animation-delay: 1s;
        }

        .btn-custom {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            color: #fff;
            border: none;
            padding: 14px 30px;
            font-weight: 600;
            border-radius: 50px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.6);
            background: linear-gradient(135deg, #0072ff, #00c6ff);
            color: #fff;
        }

        /* Animations */
        @keyframes bounceIn {
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

        @keyframes fadeInUp {
            0% {
                transform: translateY(20px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* LATEST ITEMS*/
        #latestHeading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            background: linear-gradient(90deg, #ff512f, #dd2476);
            color: #fff;
            padding: 14px 25px;
            border-radius: 15px;
            font-size: 1.8rem;
            font-weight: 600;
            width: fit-content;
            margin: 50px auto 20px auto;
            position: relative;
            z-index: 10;
        }

        .items .card {
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            background-color: #fff;
            color: #000;
            overflow: hidden;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.5s ease-out;
        }

        .items .card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .items .card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 18px 35px rgba(0, 0, 0, 0.35);
        }

        .items .card img {
            max-height: 220px;
            width: 100%;
            object-fit: cover;
        }

        .items .card-body h5 {
            font-weight: 600;
        }

        .items .btn-get {
            background: #6c5ce7;
            color: #fff;
            border-radius: 50px;
            padding: 6px 20px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
        }

        .items .btn-get:hover {
            background: #341f97;
            transform: translateY(-2px);
        }

        /* FILTER DROPDOWN */
        .dropdown-menu {
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* FLOATING ADD BUTTON */
        .fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(45deg, #ff512f, #dd2476);
            color: #fff;
            border: none;
            padding: 18px 22px;
            border-radius: 50px;
            font-size: 1.2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .fab:hover {
            transform: translateY(-4px) scale(1.1);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.6);
        }

        /* FOOTER */
        footer {
            background: #111;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }

        /* RESPONSIVE */
        @media(max-width:768px) {
            .hero h2 {
                font-size: 2rem;
            }

            .btn-custom {
                padding: 12px 25px;
            }

            header {
                flex-direction: column;
                align-items: flex-start;
            }

            nav {
                margin-top: 10px;
            }
        }
    </style>
</head>



<body>

    <header>
        <h1>College Kart 🛒</h1>

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
        <?php else: ?>
            <nav>
                <a href="auth/login.php">Login</a>
                <a href="auth/signup.php">Sign Up</a>
            </nav>
        <?php endif; ?>
    </header>

    <section class="hero">
        <h2>
            <span class="bounce-word" style="animation-delay:0s;">Buy</span> |
            <span class="bounce-word" style="animation-delay:0.5s;">Sell</span> |
            <span class="bounce-word" style="animation-delay:1s;">Donate</span>
        </h2>
        <p class="hero-subtext">Your one-stop shop for college essentials</p>
        <?php if ($userId): ?>
            <a href="<?= ACTION_URL ?>add_item.php" class="btn-custom">Start Donating | Selling</a>
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
            $res = mysqli_query($conn, "SELECT items.*, users.name AS seller_name, users.id AS seller_id FROM items JOIN users ON items.seller_id = users.id ORDER BY items.id DESC");
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
                                <h5 class="card-title"><?= $p['name'] ?></h5>
                                <?php if ($p['type'] === 'sell'): ?>
                                    <p class="text-muted mb-1">Price: ₹<?= $p['price'] ?></p>
                                <?php else: ?>
                                    <p class="text-muted mb-1">Available for Donation</p>
                                <?php endif; ?>
                                <p class="small"><?= $p['description'] ?></p>
                                <p class="small text-muted">Seller: <?= $p['seller_name'] ?></p>

                                <a href="product_view.php?id=<?= $p['id'] ?>" class="btn btn-success mt-2 me-2">View</a>
                                <?php if ($isOwner): ?>
                                    <a href="<?= ACTION_URL ?>edit_item.php?id=<?= $p['id'] ?>"
                                        class="btn btn-warning mt-2 me-2">Edit</a>
                                    <a href="<?= ACTION_URL ?>delete_item.php?id=<?= $p['id'] ?>" class="btn btn-danger mt-2 me-2"
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
                                    <a href="auth/login.php"
                                        class="btn btn-primary mt-2"><?= $p['type'] === 'sell' ? 'Login to Buy' : 'Login to Get' ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; else: ?>
                <p class='text-center text-muted'>No items found. Be the first to add one!</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($userId): ?>
        <button class="fab" onclick="location.href='<?= ACTION_URL ?>/add_item.php'">➕ Add Item</button>
    <?php endif; ?>

    <footer>
        <p>© <?= date('Y') ?> College Kart | Built by Prashant & Ayush</p>
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

        // Animate cards on scroll
        const cards = document.querySelectorAll('.items .card');
        function revealCards() {
            const triggerBottom = window.innerHeight * 0.85;
            cards.forEach((card, i) => {
                const cardTop = card.getBoundingClientRect().top;
                if (cardTop < triggerBottom) {
                    setTimeout(() => { card.classList.add('visible'); }, i * 150);
                }
            });
        }
        window.addEventListener('scroll', revealCards);
        window.addEventListener('load', revealCards);
    </script>

</body>

</html>