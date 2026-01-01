<?php
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/db.php';

$item_name = $_POST['item_name'];
$price = $_POST['price'];
$type = $_POST['type']; // 'sell' or 'donate'

// Insert into database
$stmt = $conn->prepare("INSERT INTO listings (item_name, price, type) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $item_name, $price, $type);
$stmt->execute();

header("Location: listings.php");
?>