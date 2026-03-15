<?php
date_default_timezone_set('Asia/Kathmandu');

require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/db.php';

$message = "";

if (!isset($_GET['token'])) {
    die("Invalid request!");
}

$token = $_GET['token'];

$stmt = $conn->prepare("SELECT * FROM users WHERE reset_token=? AND token_expiry > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Invalid or expired token!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $message = "Passwords do not match!";
    } else {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, token_expiry=NULL WHERE id=?");
        $stmt->bind_param("si", $hashedPassword, $user['id']);
        $stmt->execute();

        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Clz Store</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            width: 350px;
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;
            color: #4CAF50;
            font-size: 2rem;
        }

        .input-group {
            position: relative;
            margin-bottom: 15px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 1px 12px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
        }

        .input-group .bi {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            font-size: 1.1rem;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: linear-gradient(135deg, #ff9800, #ff5722);
            color: #fff;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background: linear-gradient(135deg, #ff5722, #ff9800);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(255, 87, 34, 0.4);
        }

        .message {
            color: red;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .links {
            margin-top: 15px;
            font-size: 0.95rem;
        }

        .links a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <h2>Reset Password</h2>

        <?php if (!empty($message)): ?>
            <div class="message">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="input-group">
                <input type="password" name="password" placeholder="New Password" required id="password">
                <i class="bi bi-eye-slash" id="togglePassword"></i>
            </div>

            <div class="input-group">
                <input type="password" name="confirm" placeholder="Confirm Password" required id="confirmPassword">
                <i class="bi bi-eye-slash" id="toggleConfirm"></i>
            </div>

            <button type="submit">Reset Password</button>
        </form>

        <div class="links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function () {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            this.classList.toggle("bi-eye");
            this.classList.toggle("bi-eye-slash");
        });

        const toggleConfirm = document.querySelector("#toggleConfirm");
        const confirmPassword = document.querySelector("#confirmPassword");

        toggleConfirm.addEventListener("click", function () {
            const type = confirmPassword.getAttribute("type") === "password" ? "text" : "password";
            confirmPassword.setAttribute("type", type);
            this.classList.toggle("bi-eye");
            this.classList.toggle("bi-eye-slash");
        });
    </script>

</body>

</html>