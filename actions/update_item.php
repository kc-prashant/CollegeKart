<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1️⃣ Include config, DB, and auth check
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth_check.php';

// 2️⃣ Validate POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    die("Invalid request.");
}

$id = intval($_POST['id']);

// 3️⃣ Fetch the existing item from database
$result = $conn->query("SELECT * FROM items WHERE id = $id");
if (!$result || $result->num_rows === 0) {
    die("Item not found.");
}
$item = $result->fetch_assoc();

// 4️⃣ Permission check: only admin or seller can update
if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $item['seller_id']) {
    die("Access denied. You cannot edit this item.");
}

// 5️⃣ Get submitted values
$name = $_POST['name'];
$category = $_POST['category'];
$price = floatval($_POST['price']); // ensure numeric
$description = $_POST['description'];
$image_path = $item['image']; // default: keep old image

// 6️⃣ Handle image upload if provided
if (!empty($_FILES['image']['name'])) {

    // Uploads directory inside public
    $uploads_dir = __DIR__ . '/../public/uploads';
    if (!is_dir($uploads_dir) && !mkdir($uploads_dir, 0777, true)) {
        die("Failed to create uploads directory.");
    }

    $image_name = uniqid() . "_" . basename($_FILES['image']['name']);
    $full_path = $uploads_dir . "/" . $image_name;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $full_path)) {
        die("Failed to upload image.");
    }

    // Update image path for DB (relative to public)
    $image_path = "uploads/" . $image_name;

    // Delete old image safely
    $old_image_path = __DIR__ . "/../public/" . $item['image'];
    if (!empty($item['image']) && file_exists($old_image_path)) {
        unlink($old_image_path);
    }
}

// 7️⃣ Update item in database safely
$stmt = $conn->prepare("UPDATE items SET name=?, category=?, price=?, description=?, image=? WHERE id=?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ssdssi", $name, $category, $price, $description, $image_path, $id);
if (!$stmt->execute()) {
    die("Database error: " . $stmt->error);
}

// 8️⃣ Redirect to selling_items page using BASE_URL
header("Location: " . BASE_URL . "/index.php");
exit();
