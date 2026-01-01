<?php
// Start session first
session_start();

// Include config, DB, auth
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/db.php';
require_once __DIR__ . '/../../app/auth_check.php';

// Default variables
$backLink = BASE_URL . '/index.php';
$message = "";
$title = "";
$error = false;

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['action_type'])) {

    $productId = intval($_POST['product_id']);
    $actionType = $_POST['action_type'];

    // Fetch item from DB
    $stmt = $conn->prepare("SELECT * FROM items WHERE id=?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $res = $stmt->get_result();

    if (!$res || $res->num_rows === 0) {
        $title = "Error";
        $message = "Item not found.";
        $error = true;
    } else {
        $item = $res->fetch_assoc();

        if ($actionType === 'buy') {
            $title = "Purchase Successful";
            $message = "You bought <strong>" . htmlspecialchars($item['name']) . "</strong> successfully.";
        } elseif ($actionType === 'get') {
            $title = "Success!";
            $message = "You have successfully claimed <strong>" . htmlspecialchars($item['name']) . "</strong>.";
        } else {
            $title = "Error";
            $message = "Invalid action.";
            $error = true;
        }
    }

} else {
    $title = "Error";
    $message = "Invalid request.";
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($title) ?> - Clz Store
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f4f8;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .message-box {
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
        }

        h2 {
            font-weight: 700;
            margin-bottom: 20px;
            color:
                <?= $error ? '#e74c3c' : '#2dce89' ?>
            ;
        }

        p {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        a.btn {
            text-decoration: none;
            padding: 10px 25px;
            border-radius: 10px;
            background: #0984e3;
            color: #fff;
            font-weight: 600;
            transition: 0.3s;
        }

        a.btn:hover {
            background: #74b9ff;
        }
    </style>
</head>

<body>
    <div class="message-box">
        <h2>
            <?= htmlspecialchars($title) ?>
        </h2>
        <p>
            <?= $message ?>
        </p>
        <a href="<?= $backLink ?>" class="btn">Back to Store</a>
    </div>
</body>

</html>