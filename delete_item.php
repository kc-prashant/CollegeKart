<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'auth/auth_check.php';
include 'db.php';

if (!isset($_GET['id'])) {
    die("No item specified.");
}

$id = intval($_GET['id']);

// Fetch the item
$result = $conn->query("SELECT * FROM items WHERE id=$id");
if (!$result || $result->num_rows === 0) {
    die("Item not found.");
}

$item = $result->fetch_assoc();

// Check permission: admin or seller
if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $item['seller_id']) {
    die("Access denied. You cannot delete this item.");
}

// Delete the image file if exists
if (!empty($item['image']) && file_exists($item['image'])) {
    unlink($item['image']);
}

// Delete the database record
$stmt = $conn->prepare("DELETE FROM items WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: index.php"); // redirect after deletion
    exit();
} else {
    die("Database error: " . $stmt->error);
}
?>