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

        echo "Password updated successfully! <a href='login.php'>Login</a>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
</head>

<body>

    <h2>Reset Password</h2>

    <?php if (!empty($message))
        echo $message; ?>

    <form method="post">
        <input type="password" name="password" placeholder="New Password" required><br><br>
        <input type="password" name="confirm" placeholder="Confirm Password" required><br><br>
        <button type="submit">Reset Password</button>
    </form>

</body>

</html>