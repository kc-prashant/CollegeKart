<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session and include dependencies
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth_check.php';

$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['category'], $_POST['price'], $_POST['type'])) {

    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $type = $_POST['type']; // 'sell' or 'donate'
    $seller_id = $_SESSION['user_id'];

    if (!in_array($type, ['sell', 'donate'])) {
        $error = "Invalid item type selected.";
    } else {
        // Handle image upload
        if (!empty($_FILES['image']['name'])) {

            // Save images in public/uploads (accessible by browser)
            $uploads_dir = __DIR__ . '/../public/uploads'; // absolute path to public/uploads
            if (!is_dir($uploads_dir)) {
                mkdir($uploads_dir, 0777, true);
            }

            $image_name = uniqid() . "_" . basename($_FILES['image']['name']);
            $full_path = $uploads_dir . "/" . $image_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $full_path)) {

                // Relative path to store in DB
                $image_path = "uploads/" . $image_name;

                // Insert into database
                $stmt = $conn->prepare("INSERT INTO items (name, category, price, description, image, seller_id, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt)
                    die("Prepare failed: " . $conn->error);

                $stmt->bind_param("ssdssis", $name, $category, $price, $description, $image_path, $seller_id, $type);

                if ($stmt->execute()) {
                    // Redirect to homepage
                    header("Location: " . BASE_URL . "/index.php");
                    exit();
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
                <label>Type</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="sell" value="sell" checked>
                    <label class="form-check-label" for="sell">Sell</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="donate" value="donate">
                    <label class="form-check-label" for="donate">Donate</label>
                </div>
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

    <script>
        const typeRadios = document.querySelectorAll('input[name="type"]');
        const priceField = document.querySelector('input[name="price"]');

        typeRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.value === 'donate' && radio.checked) {
                    priceField.style.display = 'none';
                    priceField.value = 0;
                } else {
                    priceField.style.display = 'inline-block';
                }
            });
        });
    </script>
</body>

</html>