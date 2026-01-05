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
    <title>College Kart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ===== GLOBAL ===== */
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6fb;
        }

        /* ===== HEADER ===== */
        header {
            background: linear-gradient(90deg, #111, #6366f1);
            color: #fff;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-weight: 700;
        }

        nav a {
            color: #fff;
            margin-left: 20px;
            font-weight: 500;
        }

        /* ===== HERO ===== */
        .hero {
            background: linear-gradient(135deg, #667eea, #764ba2, #ff758c);
            padding: 110px 20px;
            color: #fff;
        }

        .hero h2 {
            font-size: 2.8rem;
            font-weight: 700;
        }

        .hero-subtext {
            font-size: 1.3rem;
            margin: 20px 0 30px;
            opacity: 0.95;
        }

        .btn-custom {
            background: linear-gradient(135deg, #22d3ee, #3b82f6);
            color: #fff;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .35);
        }

        /* ===== HERO CARD ===== */
        .hero-card {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(14px);
            border-radius: 22px;
            padding: 32px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .4);
        }

        .hero-card h3 {
            font-weight: 700;
        }

        .hero-card ul {
            list-style: none;
            padding: 0;
        }

        .hero-card li {
            margin-bottom: 8px;
        }

        .hero-card .tag {
            display: inline-block;
            background: rgba(255, 255, 255, .3);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: .85rem;
            margin: 4px;
        }

        /* ===== LATEST ITEMS ===== */
        #latestHeading {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            background: linear-gradient(90deg, #6366f1, #ec4899);
            color: #fff;
            padding: 16px 28px;
            border-radius: 18px;
            font-size: 1.7rem;
            width: fit-content;
            margin: 70px auto 25px;
        }

        /* ===== ITEM CARDS (FIXED SIZE) ===== */
        .items .card {
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .15);
            transition: .35s;
            height: 100%;
        }

        .items .card:hover {
            transform: translateY(-10px);
        }

        .card-img-wrapper {
            height: 220px;
            overflow: hidden;
        }

        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-body {
            display: flex;
            flex-direction: column;
        }

        .card-body .btn {
            margin-top: auto;
        }

        /* ===== FAB ===== */
        .fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #ec4899, #6366f1);
            color: #fff;
            border: none;
            padding: 18px 22px;
            border-radius: 50%;
            font-size: 1.3rem;
        }

        /* ===== FOOTER ===== */
        footer {
            background: #111;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>

<body>

    <header>
        <h1>College Kart 🛒</h1>
        <?php if ($userId): ?>
            <div>👤
                <?= htmlspecialchars($userName) ?> |
                <a href="auth/logout.php" class="text-warning">Logout</a>
            </div>
        <?php else: ?>
            <nav>
                <a href="auth/login.php">Login</a>
                <a href="auth/signup.php">Sign Up</a>
            </nav>
        <?php endif; ?>
    </header>

    <!-- ===== HERO ===== -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-lg-7 mb-4">
                    <h2>Buy | Sell | Donate</h2>
                    <p class="hero-subtext">Your one-stop shop for college essentials</p>

                    <?php if ($userId): ?>
                        <a href="<?= ACTION_URL ?>add_item.php" class="btn-custom">Start Donating | Selling</a>
                    <?php else: ?>
                        <a href="auth/login.php" class="btn-custom">Login to Start</a>
                    <?php endif; ?>
                </div>

                <div class="col-lg-5">
                    <div class="hero-card">
                        <h3>🎓 College Kart</h3>
                        <p>A student-first marketplace to exchange essentials securely.</p>
                        <ul>
                            <li>✔ Verified students</li>
                            <li>✔ Fixed fair pricing</li>
                            <li>✔ Donations supported</li>
                        </ul>
                        <div>
                            <span class="tag">Buy</span>
                            <span class="tag">Sell</span>
                            <span class="tag">Donate</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ===== LATEST ITEMS ===== -->
    <div class="container my-5">

        <div id="latestHeading">
            <span>Latest Items</span>
            <div class="dropdown">
                <button class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">Filter</button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?type=sell">Selling</a></li>
                    <li><a class="dropdown-item" href="?type=donate">Donated</a></li>
                    <li><a class="dropdown-item" href="?type=all">All</a></li>
                </ul>
            </div>
        </div>

        <div class="row g-4 items">
            <?php
            $type = $_GET['type'] ?? 'all';
            $res = mysqli_query(
                $conn,
                "SELECT items.*, users.name seller_name, users.id seller_id
 FROM items JOIN users ON items.seller_id = users.id
 ORDER BY items.id DESC"
            );

            if ($res && mysqli_num_rows($res) > 0):
                while ($p = mysqli_fetch_assoc($res)):
                    if ($type != 'all' && $p['type'] != $type)
                        continue;
                    $isOwner = $userId && ($isAdmin || $userId == $p['seller_id']);
                    ?>
                    <div class="col-md-4">
                        <div class="card h-100">

                            <div class="card-img-wrapper">
                                <img src="<?= $p['image'] ?>" alt="<?= $p['name'] ?>">
                            </div>

                            <div class="card-body">
                                <h5>
                                    <?= $p['name'] ?>
                                </h5>

                                <?php if ($p['type'] == 'sell'): ?>
                                    <p class="text-muted">Price: ₹
                                        <?= $p['price'] ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-muted">Available for Donation</p>
                                <?php endif; ?>

                                <p class="small">
                                    <?= $p['description'] ?>
                                </p>
                                <p class="small text-muted">Seller:
                                    <?= $p['seller_name'] ?>
                                </p>

                                <div>
                                    <a href="product_view.php?id=<?= $p['id'] ?>" class="btn btn-success btn-sm">View</a>

                                    <?php if ($isOwner): ?>
                                        <a href="<?= ACTION_URL ?>edit_item.php?id=<?= $p['id'] ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <a href="<?= ACTION_URL ?>delete_item.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete item?')">Delete</a>
                                    <?php endif; ?>

                                    <?php if ($userId && $userId != $p['seller_id']): ?>
                                        <form method="post" action="user/action.php" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="action_type"
                                                value="<?= $p['type'] == 'sell' ? 'buy' : 'get' ?>">
                                            <button class="btn btn-primary btn-sm">
                                                <?= $p['type'] == 'sell' ? 'Buy' : 'Get' ?>
                                            </button>
                                        </form>
                                    <?php elseif (!$userId): ?>
                                        <a href="auth/login.php" class="btn btn-primary btn-sm">Login</a>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endwhile; else: ?>
                <p class="text-center text-muted">No items found</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($userId): ?>
        <button class="fab" onclick="location.href='<?= ACTION_URL ?>add_item.php'">➕</button>
    <?php endif; ?>

    <footer>
        <p>©
            <?= date('Y') ?> College Kart | Built by Prashant & Ayush
        </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>