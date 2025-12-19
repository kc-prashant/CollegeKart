<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'auth/auth_check.php';  // ensures session is active
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    die("Invalid request.");
}

$id = intval($_POST['id']);

// Fetch existing item
$result = $conn->query("SELECT * FROM items WHERE id=$id");
if (!$result || $result->num_rows === 0) {
    die("Item not found.");
}

$item = $result->fetch_assoc();

// Check permission: admin or seller
if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $item['seller_id']) {
    die("Access denied. You cannot edit this item.");
}

// Get updated values
$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$description = $_POST['description'];
$image_path = $item['image']; // keep existing image by default

// Handle image upload if a new file is provided
if (!empty($_FILES['image']['name'])) {
    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    $new_image_path = "uploads/" . uniqid() . "_" . $image;

    if (move_uploaded_file($tmp, $new_image_path)) {
        $image_path = $new_image_path;

        // Optionally delete old image
        if (!empty($item['image']) && file_exists($item['image'])) {
            unlink($item['image']);
        }
    } else {
        die("Failed to upload new image.");
    }
}

// Update database
$stmt = $conn->prepare("UPDATE items SET name=?, category=?, price=?, description=?, image=? WHERE id=?");
$stmt->bind_param("ssdssi", $name, $category, $price, $description, $image_path, $id);

if ($stmt->execute()) {
    header("Location: index.php"); // redirect to homepage or dashboard
    exit();
} else {
    die("Database error: " . $stmt->error);
}
?>