<?php
session_start();

// Only allow admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Include DB connection
require_once __DIR__ . '/../../app/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Clz Store</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #4CAF50;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .table th {
            background: #4CAF50;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, Admin! <a href="../auth/logout.php">Logout</a></p>

        <h2>Registered Users</h2>
        <table class="table">
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
            <?php
            $result = mysqli_query($conn, "SELECT id, email, role FROM users ORDER BY id DESC");
            if ($result) {
                while ($user = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$user['id']}</td>
                        <td>{$user['email']}</td>
                        <td>{$user['role']}</td>
                        <td>
                            <a href='delete_user.php?id={$user['id']}' onclick=\"return confirm('Delete this user?')\">Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>Failed to fetch users: " . mysqli_error($conn) . "</td></tr>";
            }
            ?>
        </table>

        <h2>Products</h2>
        <p><a href="../index.php">Go to Products Page</a></p>
    </div>
</body>

</html>