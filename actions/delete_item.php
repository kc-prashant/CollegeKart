<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include config, DB, and auth check
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth_check.php';

// Validate item ID
if (!isset($_GET['id'])) {
    die("No item specified.");
}

$id = intval($_GET['id']);

// Fetch the item from DB
$stmt = $conn->prepare("SELECT * FROM items WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Item not found.");
}

$item = $result->fetch_assoc();

// Check permission: admin or seller
if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $item['seller_id']) {
    die("Access denied. You cannot delete this item.");
}

// Delete the image file if exists
if (!empty($item['image'])) {
    $imagePath = __DIR__ . '/' . $item['image']; // Absolute path relative to delete_item.php
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Delete the database record
$stmtDel = $conn->prepare("DELETE FROM items WHERE id=?");
$stmtDel->bind_param("i", $id);

if ($stmtDel->execute()) {
    // Redirect to selling items page
    header("Location: " . BASE_URL . "/index.php");
    exit();
} else {
    die("Database error: " . $stmtDel->error);
}
