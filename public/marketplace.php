<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId = $_SESSION['user_id'] ?? 0;
$userName = $_SESSION['name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Kart - Marketplace</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6fb;
        }

        /* HEADER */
        header {
            background: linear-gradient(90deg, #111, #6366f1);
            color: #fff;
            padding: 20px 40px;
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
            margin-left: 25px;
            text-decoration: none;
            font-weight: 500;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* MARKETPLACE TITLE */
        .section-title {
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

        /* ITEM CARDS */
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
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }

        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card-body {
            display: flex;
            flex-direction: column;
        }

        .badge-status {
            font-size: 0.8rem;
            padding: 6px 10px;
            border-radius: 8px;
        }

        /* FAB */
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

        /* FOOTER */
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
        <nav>
            <a href="index.php">Home</a>
            <a href="marketplace.php">Marketplace</a>

            <?php if ($userId): ?>
                <span style="margin-left:20px;">👤
                    <?= htmlspecialchars($userName) ?>
                </span>
                <a href="auth/logout.php" class="text-warning">Logout</a>
            <?php else: ?>
                <a href="auth/login.php">Login</a>
                <a href="auth/signup.php">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container my-5">
        <div class="section-title">
            <span>Marketplace</span>
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
                "SELECT items.*, users.name seller_name 
 FROM items 
 JOIN users ON items.seller_id = users.id
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
                                <img src="<?= $p['image'] ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                            </div>

                            <div class="card-body">
                                <h5>
                                    <?= htmlspecialchars($p['name']) ?>
                                </h5>

                                <?php if ($p['type'] == 'sell'): ?>
                                    <p class="text-muted">Price: ₹
                                        <?= $p['price'] ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-muted">Available for Donation</p>
                                <?php endif; ?>

                                <p class="small">
                                    <?= htmlspecialchars($p['description']) ?>
                                </p>
                                <p class="small text-muted">Seller:
                                    <?= htmlspecialchars($p['seller_name']) ?>
                                </p>

                                <div class="mt-auto">

                                    <a href="product_view.php?id=<?= $p['id'] ?>" class="btn btn-success btn-sm">View</a>

                                    <?php if ($isOwner): ?>
                                        <a href="<?= ACTION_URL ?>edit_item.php?id=<?= $p['id'] ?>"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <a href="<?= ACTION_URL ?>delete_item.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete item?')">Delete</a>
                                    <?php endif; ?>


                                    <?php if ($p['status'] == 'available'): ?>

                                        <?php if ($userId && $userId != $p['seller_id']): ?>
                                            <form method="post" action="user/action.php" class="d-inline">
                                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                                <input type="hidden" name="action_type" value="<?= $p['type'] == 'sell' ? 'buy' : 'get' ?>">
                                                <button class="btn btn-primary btn-sm">
                                                    <?= $p['type'] == 'sell' ? 'Buy' : 'Get' ?>
                                                </button>
                                            </form>
                                        <?php elseif (!$userId): ?>
                                            <a href="auth/login.php" class="btn btn-primary btn-sm">Login</a>
                                        <?php endif; ?>

                                    <?php elseif ($p['status'] == 'booked'): ?>

                                        <span class="badge bg-warning badge-status">Booked</span>

                                    <?php elseif ($p['status'] == 'completed'): ?>

                                        <span class="badge bg-success badge-status">Sold</span>

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
            <?= date('Y') ?> College Kart | Built by Prashant & Aayush
        </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>