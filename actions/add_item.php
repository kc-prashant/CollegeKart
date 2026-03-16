<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth_check.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price'] ?? 0);
    $description = trim($_POST['description']);
    $type = $_POST['type'];
    $seller_id = $_SESSION['user_id'];

    // ---- VALIDATIONS ----

    if (!preg_match("/[A-Za-z]/", $name)) {
        $error = "Item name must contain letters.";
    } elseif (!preg_match("/[A-Za-z]/", $category)) {
        $error = "Category must contain letters.";
    } elseif (!in_array($type, ['sell', 'donate'])) {
        $error = "Invalid item type.";
    } elseif ($type === 'sell' && $price <= 0) {
        $error = "Price must be greater than 0.";
    } else {

        if ($type === "donate") {
            $price = 0;
        }

        if (!empty($_FILES['image']['name'])) {

            $uploads_dir = __DIR__ . '/../public/uploads';

            if (!is_dir($uploads_dir)) {
                mkdir($uploads_dir, 0777, true);
            }

            // ---- FILE SECURITY ----
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error = "Only JPG, PNG, WEBP images allowed.";
            } else {

                $image_name = uniqid("item_", true) . "." . $ext;
                $full_path = $uploads_dir . "/" . $image_name;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $full_path)) {

                    $image_path = "uploads/" . $image_name;

                    $stmt = $conn->prepare("INSERT INTO items 
                    (name, category, price, description, image, seller_id, type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");

                    if (!$stmt) {
                        die("Prepare failed: " . $conn->error);
                    }

                    $stmt->bind_param(
                        "ssdssis",
                        $name,
                        $category,
                        $price,
                        $description,
                        $image_path,
                        $seller_id,
                        $type
                    );

                    if ($stmt->execute()) {

                        header("Location: " . BASE_URL . "/marketplace.php");
                        exit();

                    } else {

                        $error = "Database error: " . $stmt->error;

                    }

                    $stmt->close();

                } else {

                    $error = "Failed to upload image.";

                }

            }

        } else {

            $error = "Please upload an image.";

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
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>

</head>

<body>

    <div class="container-box">

        <h2>Add New Item</h2>

        <?php if (!empty($error))
            echo "<div class='error-message'>$error</div>"; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">

                <label>Item Name</label>

                <input type="text" name="name" class="form-control" pattern=".*[A-Za-z].*" placeholder="Enter item name"
                    required>

            </div>


            <div class="mb-3">

                <label>Category</label>

                <input type="text" name="category" class="form-control" pattern=".*[A-Za-z].*"
                    placeholder="Book, Electronics, Notes..." required>

            </div>


            <div class="mb-3" id="priceDiv">

                <label>Price (Rs.)</label>

                <input type="number" name="price" class="form-control" min="0.01" step="0.01" placeholder="Enter price">

            </div>


            <div class="mb-3">

                <label>Type</label>

                <br>

                <div class="form-check form-check-inline">

                    <input class="form-check-input" type="radio" name="type" value="sell" checked>

                    <label class="form-check-label">Sell</label>

                </div>

                <div class="form-check form-check-inline">

                    <input class="form-check-input" type="radio" name="type" value="donate">

                    <label class="form-check-label">Donate</label>

                </div>

            </div>


            <div class="mb-3">

                <label>Description</label>

                <textarea name="description" class="form-control" rows="3" placeholder="Write description"></textarea>

            </div>


            <div class="mb-3">

                <label>Upload Image</label>

                <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>

            </div>


            <button type="submit" class="btn btn-submit mt-3">

                Add Item

            </button>

        </form>

    </div>

    <script>

        const radios = document.querySelectorAll('input[name="type"]')
        const priceDiv = document.getElementById('priceDiv')
        const priceInput = document.querySelector('input[name="price"]')

        radios.forEach(r => {

            r.addEventListener('change', () => {

                if (r.value === 'donate' && r.checked) {

                    priceDiv.style.display = 'none'
                    priceInput.value = 0

                }

                else {

                    priceDiv.style.display = 'block'

                }

            })

        })

    </script>

</body>

</html>