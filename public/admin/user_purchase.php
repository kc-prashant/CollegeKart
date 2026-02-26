<?php
session_start();

// Only admin allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once __DIR__ . '/../../app/db.php';

$transactions = [];

// Fetch ALL transactions with item & buyer info
$stmt = mysqli_prepare($conn, "
    SELECT t.*, 
           i.name AS item_name, 
           i.price,
           u.email AS buyer_email
    FROM transactions t
    JOIN items i ON t.item_id = i.id
    JOIN users u ON t.buyer_id = u.id
    ORDER BY t.created_at DESC
");

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $transactions[] = $row;
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Transactions | Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            width: 1100px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #4CAF50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #4CAF50;
            color: white;
        }

        .status-pill {
            padding: 4px 10px;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .status-booked {
            background: #d97706;
        }

        .status-cancelled {
            background: #dc2626;
        }

        .status-completed {
            background: #16a34a;
        }

        .top-links a {
            margin-right: 15px;
            text-decoration: none;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">

        <h1>All Purchase Transactions</h1>

        <div class="top-links">
            <a href="dashboard.php">Dashboard</a> |
            <a href="../index.php">Marketplace</a> |
            <a href="../auth/logout.php">Logout</a>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Buyer</th>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>

            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td>
                            <?= $t['id'] ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($t['buyer_email']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($t['item_name']) ?>
                        </td>
                        <td>Rs.
                            <?= number_format($t['price'], 2) ?>
                        </td>
                        <td>
                            <?= $t['quantity'] ?? 1 ?>
                        </td>
                        <td>Rs.
                            <?= number_format(($t['quantity'] ?? 1) * $t['price'], 2) ?>
                        </td>
                        <td>
                            <?php
                            $statusClass = '';
                            switch ($t['status']) {
                                case 'booked':
                                    $statusClass = 'status-booked';
                                    break;
                                case 'cancelled':
                                    $statusClass = 'status-cancelled';
                                    break;
                                case 'completed':
                                    $statusClass = 'status-completed';
                                    break;
                                default:
                                    $statusClass = 'status-booked';
                            }
                            ?>
                            <span class="status-pill <?= $statusClass ?>">
                                <?= ucfirst($t['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?= $t['created_at'] ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No transactions found</td>
                </tr>
            <?php endif; ?>

        </table>
    </div>
</body>

</html>