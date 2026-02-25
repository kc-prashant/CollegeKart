<?php
session_start();

// Only allow admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once __DIR__ . '/../../app/db.php';

$currentAdminId = $_SESSION['user_id'];

/* ===== HANDLE ACTIONS ===== */
if (isset($_GET['action']) && isset($_GET['id'])) {

    $id = (int) $_GET['id'];
    $action = $_GET['action'];

    // Prevent admin from modifying themselves
    if ($id !== $currentAdminId) {

        if ($action === 'make_admin') {
            mysqli_query($conn, "UPDATE users SET role='admin' WHERE id=$id");
        }

        if ($action === 'remove_admin') {
            mysqli_query($conn, "UPDATE users SET role='user' WHERE id=$id");
        }

        if ($action === 'delete') {
            mysqli_query($conn, "DELETE FROM users WHERE id=$id");
        }
    }

    header("Location: dashboard.php");
    exit;
}
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
            max-width: 1100px;
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
            font-weight: 500;
        }

        .btn-delete {
            color: red;
        }

        .btn-admin {
            color: green;
        }

        .btn-remove {
            color: orange;
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
            text-align: center;
        }

        .table th {
            background: #4CAF50;
            color: white;
        }

        .top-links {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">

        <h1>Admin Dashboard</h1>

        <div class="top-links">
            Welcome, Admin |
            <a href="../index.php">Go to Marketplace</a> |
            <a href="../auth/logout.php">Logout</a>
        </div>

        <h2>Registered Users</h2>

        <table class="table">
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Admin Control</th>
                <th>Delete User</th>
            </tr>

            <?php
            $result = mysqli_query($conn, "SELECT id, email, role FROM users ORDER BY id DESC");

            if ($result) {
                while ($user = mysqli_fetch_assoc($result)) {

                    echo "<tr>";
                    echo "<td>{$user['id']}</td>";
                    echo "<td>{$user['email']}</td>";
                    echo "<td>{$user['role']}</td>";

                    // ===== ADMIN CONTROL COLUMN =====
                    echo "<td>";

                    if ($user['id'] != $currentAdminId) {

                        if ($user['role'] !== 'admin') {
                            echo "<a class='btn-admin' 
                              href='?action=make_admin&id={$user['id']}' 
                              onclick=\"return confirm('Make this user admin?')\">
                              Make Admin
                              </a>";
                        } else {
                            echo "<a class='btn-remove' 
                              href='?action=remove_admin&id={$user['id']}' 
                              onclick=\"return confirm('Remove admin role?')\">
                              Remove Admin
                              </a>";
                        }

                    } else {
                        echo "You";
                    }

                    echo "</td>";

                    // ===== DELETE COLUMN =====
                    echo "<td>";

                    if ($user['id'] != $currentAdminId) {
                        echo "<a class='btn-delete' 
                          href='?action=delete&id={$user['id']}' 
                          onclick=\"return confirm('Delete this user?')\">
                          Delete
                          </a>";
                    } else {
                        echo "-";
                    }

                    echo "</td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Failed to fetch users</td></tr>";
            }
            ?>
        </table>

    </div>
</body>

</html>