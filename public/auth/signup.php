<?php
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "All fields are required!";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Email already registered!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Extract name from email
            $name = explode('@', $email)[0];

            // Insert user
            $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt_insert->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt_insert->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $message = "Error: " . $stmt_insert->error;
            }

            $stmt_insert->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup | Clz Store</title>
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

        .login-link {
            margin-top: 15px;
            font-size: 0.95rem;
        }

        .login-link a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <h2>Create Account</h2>

        <?php if (!empty($message)): ?>
            <div class="message">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required id="password">
                <i class="bi bi-eye-slash" id="togglePassword"></i>
            </div>

            <button type="submit">Sign Up</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login</a>
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
    </script>

</body>

</html>