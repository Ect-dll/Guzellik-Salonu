<?php
include "admin_db.php";
admin_giris_kontrol();
include "../db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST["title"];
    $description = $_POST["description"];
    $price = $_POST["price"];

    $stmt = $conn->prepare("
        INSERT INTO products (title, description, price)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("ssd", $title, $description, $price);
    $stmt->execute();

    header("Location: products.php?ekleme=ok");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Yeni Ürün Ekle</title>
<script src="firewall.js" defer></script>
<style>
body {
    font-family: Arial;
    background:#f9f9f9;
    margin:0;
}
nav {
    background:#E91E63;
    padding:15px;
    color:white;
    display:flex;
    justify-content:space-between;
}
nav a {
    color:white;
    margin:0 10px;
    text-decoration:none;
    font-weight:bold;
}
.container {
    max-width:700px;
    margin:40px auto;
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 0 12px rgba(0,0,0,0.12);
}
label {
    font-weight:bold;
    display:block;
    margin-top:15px;
}
input, textarea {
    width:100%;
    padding:10px;
    border:1px solid #ccc;
    border-radius:6px;
    margin-top:5px;
}
button {
    background:#4CAF50;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:6px;
    cursor:pointer;
    margin-top:20px;
    font-size:16px;
}
</style>

</head>

<body>

<nav>
    <div>Admin Panel</div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Ürünler</a>
        <a href="orders.php">Siparişler</a>
        <a href="randevular.php">Randevular</a>
        <a href="users.php">Kullanıcılar</a>
        <a href="logout.php">Çıkış</a>
    </div>
</nav>

<div class="container">
    <h2>Yeni Ürün Ekle</h2>

    <form method="POST">

        <label>Ürün Adı</label>
        <input type="text" name="title" required>

        <label>Açıklama</label>
        <textarea name="description" rows="4" required></textarea>

        <label>Fiyat (₺)</label>
        <input type="number" step="0.01" name="price" required>

        <button type="submit">Ürünü Kaydet</button>

    </form>
</div>

</body>
</html>