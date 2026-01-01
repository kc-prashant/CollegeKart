<?php
// Start session and enable errors
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include config and DB
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';

// Get session info
$userId = $_SESSION['user_id'] ?? 0;
$userName = $_SESSION['name'] ?? 'User';
$userEmail = $_SESSION['email'] ?? 'user@email.com';

// Fetch items 
$stmt = $conn->prepare("
    SELECT items.*, users.name AS seller 
    FROM items 
    JOIN users ON items.seller_id = users.id 
    WHERE items.type = 'sell'
    ORDER BY items.id DESC
");
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items for Sale - Clz Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        h2 {
            margin-bottom: 30px;
            font-weight: 600;
            color: #333;
        }

        .items .card {
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            background-color: #1e1e1e;
            color: #fff;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .items .card:hover {
            transform: scale(1.03);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4);
        }

        .items .card img {
            max-height: 200px;
            width: 100%;
            object-fit: contain;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            background: #2d2d2d;
        }

        .items .card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 15px;
        }

        .items .card .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #fff;
        }

        .items .card p {
            color: #dfe6e9;
            margin: 5px 0;
        }

        .btn-primary {
            background-color: #0984e3;
            border: none;
            width: 100%;
            padding: 10px 0;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #74b9ff;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <h2 class="text-center">Items for Sale</h2>
        <div class="row g-4 items">
            <?php while ($item = $res->fetch_assoc()): ?>
                <div class="col-md-4 d-flex">
                    <div class="card w-100">
                        <?php
                        $imgPath = !empty($item['image']) ? htmlspecialchars($item['image']) : 'https://via.placeholder.com/200';
                        ?>
                        <img src="<?= BASE_URL ?>/<?= $imgPath ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title">
                                    <?= htmlspecialchars($item['name']) ?>
                                </h5>
                                <p>Price: ₹
                                    <?= htmlspecialchars($item['price']) ?>
                                </p>
                                <p>Seller:
                                    <?= htmlspecialchars($item['seller']) ?>
                                </p>
                            </div>
                            <?php if ($userId): ?>
                                <form method="post" action="<?= BASE_URL ?>/user/action.php">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="action_type" value="buy">
                                    <button type="submit" class="btn btn-primary mt-2">Buy</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>

</html>