<?php
include "../auth/auth_check.php"; // ensure user is logged in

// Make sure required POST parameters exist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['action_type'])) {

    $productId = intval($_POST['product_id']);
    $actionType = $_POST['action_type']; // "buy" or "get"

    if ($actionType === 'buy') {
        // TODO: Add payment/order logic here
        echo "<h2>Purchase successful</h2>";
        echo "<p>You bought the item successfully.</p>";
        echo "<a href='../index.php'>Back to Store</a>";
    } elseif ($actionType === 'get') {
        // TODO: Mark item as claimed in DB if needed
        echo "<h2>Success!</h2>";
        echo "<p>You have successfully claimed the donated item.</p>";
        echo "<a href='../index.php'>Back to Store</a>";
    } else {
        echo "<h2>Error</h2>";
        echo "<p>Invalid action.</p>";
        echo "<a href='../index.php'>Back to Store</a>";
    }

} else {
    echo "<h2>Error</h2>";
    echo "<p>Invalid request.</p>";
    echo "<a href='../index.php'>Back to Store</a>";
}
