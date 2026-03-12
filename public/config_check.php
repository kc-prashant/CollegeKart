<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('SITE_NAME', 'collegecart.page.gd');

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth_check.php';

function checkFile($path)
{
    return file_exists($path);
}

function checkReadable($path)
{
    return is_readable($path);
}

function checkFunction($name)
{
    return function_exists($name);
}

function result($bool)
{
    return $bool ? "✅" : "❌";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>College Cart Configuration Check</title>

    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
            padding: 40px;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-top: 30px;
        }

        p {
            margin: 6px 0;
        }
    </style>

</head>

<body>

    <div class="container">

        <h1>College Cart Configuration Check</h1>

        <h2>1. File Existence Check</h2>

        <p>
            <?= result(checkFile('../app/config.php')) ?> app/config.php
        </p>
        <p>
            <?= result(checkFile('../app/db.php')) ?> app/db.php
        </p>
        <p>
            <?= result(checkFile('../app/auth_check.php')) ?> app/auth_check.php
        </p>
        <p>
            <?= result(checkFile('auth/login.php')) ?> public/auth/login.php
        </p>
        <p>
            <?= result(checkFile('auth/signup.php')) ?> public/auth/signup.php
        </p>
        <p>
            <?= result(checkFile('marketplace.php')) ?> public/user/marketplace.php
        </p>
        <p>
            <?= result(checkFile('selling_items.php')) ?> public/user/selling_items.php
        </p>
        <p>
            <?= result(checkFile('donating_items.php')) ?> public/user/donating_items.php
        </p>
        <p>
            <?= result(checkFile('../actions/edit_item.php')) ?> actions/edit_item.php
        </p>
        <p>
            <?= result(checkFile('../actions/update_item.php')) ?> actions/update_item.php
        </p>


        <h2>2. Constants Check</h2>

        <p>
            <?= defined('BASE_URL') ? "✅ BASE_URL: " . BASE_URL : "❌ BASE_URL missing" ?>
        </p>
        <p>
            <?= defined('SITE_NAME') ? "✅ SITE_NAME: " . SITE_NAME : "❌ SITE_NAME missing" ?>
        </p>


        <h2>3. Session Functions Check</h2>

        <p>
            <?= result(checkFunction('isLoggedIn')) ?> isLoggedIn() function exists
        </p>

        <?php
        $logged = checkFunction('isLoggedIn') && isLoggedIn();
        ?>

        <p>User logged in:
            <?= $logged ? "YES" : "NO" ?>
        </p>

        <p>
            <?= result(checkFunction('isAdmin')) ?> isAdmin() function exists
        </p>


        <h2>4. Database Connection Check</h2>

        <?php

        try {
            // Test connection
            $conn->query("SELECT 1");
            echo "<p>✅ Database connected successfully</p>";

            $tables = ['users', 'items', 'transactions'];

            foreach ($tables as $table) {
                $stmt = $conn->query("SHOW TABLES LIKE '$table'");

                if ($stmt) {
                    if ($stmt->num_rows > 0) {
                        echo "<p>✅ Table '$table' exists</p>";
                    } else {
                        echo "<p>❌ Table '$table' does not exist</p>";
                    }
                } else {
                    echo "<p>❌ Failed to check table '$table'</p>";
                }
            }

        } catch (Exception $e) {
            echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
        }

        ?>


        <h2>5. File Permissions</h2>

        <p>
            <?= result(checkReadable('../app/')) ?> app/ is readable
        </p>
        <p>
            <?= result(checkReadable('./')) ?> public/ is readable
        </p>
        <p>
            <?= result(checkReadable('../actions/')) ?> actions/ is readable
        </p>



        <h2>6. PHP Info</h2>

        <p>✅ PHP Version:
            <?= phpversion() ?>
        </p>

    </div>

</body>

</html>