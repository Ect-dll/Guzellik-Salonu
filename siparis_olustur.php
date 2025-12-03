<?php
include "db.php";

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// POST ile gelmeyen bir istek varsa ana sayfaya yolla
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: urunler.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_name = $_POST['product_name'];
$product_price = $_POST['product_price'];
$quantity = $_POST['quantity'];

// SQL kayıt
$stmt = $conn->prepare("
    INSERT INTO orders (user_id, product, price, quantity, created_at)
    VALUES (?, ?, ?, ?, NOW())
");
$stmt->bind_param("isii", $user_id, $product_name, $product_price, $quantity);
$stmt->execute();

// Siparişlerim sayfasına yönlendir
header("Location: siparislerim.php?ok=1");
exit;