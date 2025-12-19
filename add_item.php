<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include auth check and database connection
include "auth/auth_check.php"; // makes sure user is logged in
include "db.php";

$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['category'], $_POST['price'])) {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);

    if (!empty($_FILES['image']['name'])) {

        // Make uploads folder if it doesn't exist
        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        $image_path = "uploads/" . uniqid() . "_" . basename($image);

        if (move_uploaded_file($tmp, $image_path)) {

            // Insert into DB safely
            $stmt = $conn->prepare("INSERT INTO items (name, category, price, description, image, seller_id) VALUES (?, ?, ?, ?, ?, ?)");
            $user_id = intval($_SESSION['user_id']);
            $stmt->bind_param("ssdssi", $name, $category, $price, $description, $image_path, $user_id);

            if ($stmt->execute()) {
                header("Location: index.php");
                exit;
            } else {
                $error = "Database error: " . $stmt->error;
            }

            $stmt->close();

        } else {
            $error = "Failed to upload image. Check folder permissions.";
        }

    } else {
        $error = "Please select an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item - Clz Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f4f8;
            font-family: 'Poppins', sans-serif;
        }

        .container-box {
            max-width: 650px;
            background: #fff;
            padding: 30px;
            margin: 60px auto;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 20px;
            color: #007bff;
        }

        label {
            font-weight: 600;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(90deg, #007bff, #6610f2);
            border: none;
            padding: 12px;
            color: #fff;
            font-weight: 600;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="container-box">
        <h2>Add New Item</h2>
        <?php if (!empty($error))
            echo "<div class='error-message'>$error</div>"; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Item Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter item name" required>
            </div>
            <div class="mb-3">
                <label>Category</label>
                <input type="text" name="category" class="form-control" placeholder="Book, Electronics, Notes..."
                    required>
            </div>
            <div class="mb-3">
                <label>Price (Rs.)</label>
                <input type="number" step="0.01" name="price" class="form-control" placeholder="Enter price" required>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"
                    placeholder="Write short description"></textarea>
            </div>
            <div class="mb-3">
                <label>Upload Image</label>
                <input type="file" name="image" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-submit mt-3">Add Item</button>
        </form>
    </div>
</body>

</html>