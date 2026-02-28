<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Kart - Marketplace</title>

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

        .section-title {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            background: linear-gradient(90deg, #6366f1, #ec4899);
            color: #fff;
            padding: 15px 30px;
            border-radius: 18px;
            width: fit-content;
            margin: 60px auto 30px;
        }

        .card {
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .1);
            height: 100%;
        }

        .card img {
            height: 220px;
            object-fit: contain;
        }

        .badge-status {
            font-size: 0.8rem;
            padding: 6px 10px;
        }

        .modal-header {
            background: #6366f1;
            color: #fff;
        }
    </style>
</head>

<body>

    <header>
        <h4>College Kart 🛒</h4>
        <nav>
            <a href="index.php">Home</a>
            <a href="marketplace.php">Marketplace</a>

            <?php if ($userId): ?>
                <a href="profile.php">👤
                    <?= htmlspecialchars($userName) ?>
                </a>
                <a href="auth/logout.php" class="text-warning">Logout</a>
                <?php if ($isAdmin) { ?>
                    <div style="margin:15px 0;">
                        <a href="admin/dashboard.php" style="
                            background:#4CAF50;
                            color:white;
                            padding:8px 15px;
                            border-radius:6px;
                            text-decoration:none;
                            font-weight:500;">
                            ← Back to Admin Dashboard
                        </a>
                    </div>
                <?php } ?>
            <?php else: ?>
                <a href="auth/login.php">Login</a>
                <a href="auth/signup.php">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container my-5">

        <div class="section-title">Marketplace</div>

        <!-- ✅ ADD ITEM BUTTON -->
        <?php if ($userId): ?>
            <div class="text-end mb-4">
                <a href="<?= ACTION_URL ?>add_item.php" class="btn btn-primary">
                    + Add Item
                </a>
            </div>
        <?php endif; ?>

        <div class="row g-4">

            <?php
            $type = $_GET['type'] ?? 'all';

            $res = mysqli_query($conn, "
            SELECT items.*, users.name seller_name 
            FROM items 
            JOIN users ON items.seller_id = users.id
            ORDER BY items.id DESC
        ");

            if ($res && mysqli_num_rows($res) > 0):
                while ($p = mysqli_fetch_assoc($res)):

                    if ($type != 'all' && $p['type'] != $type)
                        continue;

                    $isOwner = $userId && ($isAdmin || $userId == $p['seller_id']);
                    ?>

                    <div class="col-md-4">
                        <div class="card p-3">

                            <img src="<?= htmlspecialchars($p['image']) ?>" class="w-100 mb-3">

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

                                    <?php if ($userId && $userId != $p['seller_id'] && !$isAdmin): ?>
                                        <button class="btn btn-primary btn-sm openBuyModal" data-id="<?= $p['id'] ?>"
                                            data-name="<?= htmlspecialchars($p['name']) ?>" data-price="<?= $p['price'] ?>"
                                            data-type="<?= $p['type'] ?>" data-bs-toggle="modal" data-bs-target="#buyModal">
                                            <?= $p['type'] == 'sell' ? 'Buy' : 'Get' ?>
                                        </button>

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

                <?php endwhile; else: ?>
                <p class="text-center text-muted">No items found</p>
            <?php endif; ?>

        </div>
    </div>


    <!-- BUY MODAL -->
    <div class="modal fade" id="buyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Confirm Purchase</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form method="post" action="user/action.php">
                    <div class="modal-body">

                        <input type="hidden" name="product_id" id="modalProductId">
                        <input type="hidden" name="action_type" id="modalActionType">

                        <h6>Item Details</h6>
                        <p><strong>Item:</strong> <span id="modalItemName"></span></p>
                        <p><strong>Price:</strong> ₹<span id="modalItemPrice"></span></p>

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
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pickup Location</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required pattern="\d{10}"
                                title="Phone number must be 10 digits">
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll('.openBuyModal').forEach(btn => {
            btn.addEventListener('click', function () {
                document.getElementById('modalProductId').value = this.dataset.id;
                document.getElementById('modalItemName').textContent = this.dataset.name;
                document.getElementById('modalItemPrice').textContent = this.dataset.price;
                document.getElementById('modalActionType').value =
                    this.dataset.type === 'sell' ? 'buy' : 'get';
            });
        });
    </script>

</body>

</html>