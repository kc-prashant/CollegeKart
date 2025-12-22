<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'auth/auth_check.php'; // ensure user is logged in
include 'db.php'; // include DB connection

if (!isset($_GET['id'])) {
    die("No item specified.");
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM items WHERE id=$id");

if (!$result || $result->num_rows === 0) {
    die("Item not found.");
}

$row = $result->fetch_assoc();

// Check if current user is admin OR the seller
if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $row['seller_id']) {
    die("Access denied. You cannot edit this item.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Item</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2>Edit Item</h2>
        <form action="update_item.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">

            <div class="mb-3">
                <label>Item Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>"
                    required>
            </div>

            <div class="mb-3">
                <label>Category</label>
                <input type="text" name="category" class="form-control"
                    value="<?= htmlspecialchars($row['category']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Price</label>
                <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($row['price']) ?>"
                    required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control"
                    rows="3"><?= htmlspecialchars($row['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label>Image (leave blank to keep current)</label>
                <input type="file" name="image" class="form-control">
                <?php if (!empty($row['image'])): ?>
                    <img src="<?= $row['image'] ?>" alt="Current Image" style="height:100px;margin-top:10px;">
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>

</html>