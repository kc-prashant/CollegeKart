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

            $resetLink = "http://localhost/CLZ-PROJECT-REDONE/public/auth/reset_password.php?token=" . $token;

            // For college project (instead of sending email)
            $message = "Reset Link (Copy & Paste): <br><a href='$resetLink'>$resetLink</a>";

        } else {
            $message = "If this email exists, a reset link has been sent.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password</title>
</head>

<body>
    <h2>Forgot Password</h2>

    <?php if (!empty($message))
        echo $message; ?>

    <form method="post">
        <input type="email" name="email" placeholder="Enter Email" required>
        <button type="submit">Send Reset Link</button>
    </form>

</body>

</html>