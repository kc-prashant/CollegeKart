<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("Access denied");
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = (int) $_GET['id'];

if ($user_id === (int) $_SESSION['user_id']) {
    header("Location: dashboard.php?msg=You cannot delete yourself");
    exit();
}

mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");

header("Location: dashboard.php?msg=User deleted successfully");
exit();
