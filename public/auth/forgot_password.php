<?php
date_default_timezone_set('Asia/Kathmandu');

require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);

    if (empty($email)) {
        $message = "Email is required!";
    } else {

        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $token = bin2hex(random_bytes(32));
            $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

            $stmt = $conn->prepare("UPDATE users SET reset_token=?, token_expiry=? WHERE email=?");
            $stmt->bind_param("sss", $token, $expiry, $email);
            $stmt->execute();

            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;

            $message = "Reset link generated successfully! <br><br>
                        <a href='$resetLink'>$resetLink</a>";

        } else {
            $message = "If this email exists, a reset link has been generated.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Clz Store</title>

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

        input[type=email] {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
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
            word-wrap: break-word;
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
        <h2>Forgot Password</h2>

        <?php if (!empty($message)): ?>
            <div class="message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Generate Reset Link</button>
        </form>

        <div class="links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>

</body>

</html>