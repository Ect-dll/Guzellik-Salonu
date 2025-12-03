<?php
include "admin_db.php";
admin_giris_kontrol();
include "../db.php";

// ID kontrol
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: products.php");
    exit;
}

$id = (int)$_GET["id"];

// Güncelleme POST geldiyse
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST["title"];
    $description = $_POST["description"];
    $price = $_POST["price"];

    $stmt = $conn->prepare("
        UPDATE products
        SET title = ?, description = ?, price = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssdi", $title, $description, $price, $id);
    $stmt->execute();

    header("Location: products.php?guncelle=ok");
    exit;
}

// Mevcut ürünü çek
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$urun = $result->fetch_assoc();

if (!$urun) {
    header("Location: products.php?bulunamadi=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Ürün Düzenle</title>
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
    background:#2196F3;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:6px;
    cursor:pointer;
    margin-top:20px;
    font-size:16px;
}
button:hover { opacity:0.9; }
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
    <h2>Ürün Düzenle (#<?= $urun["id"] ?>)</h2>

    <form method="POST">

        <label>Ürün Adı</label>
        <input type="text" name="title" required
               value="<?= htmlspecialchars($urun['title']) ?>">

        <label>Açıklama</label>
        <textarea name="description" rows="4" required><?= htmlspecialchars($urun['description']) ?></textarea>

        <label>Fiyat (₺)</label>
        <input type="number" step="0.01" name="price" required
               value="<?= $urun['price'] ?>">

        <button type="submit">Değişiklikleri Kaydet</button>

    </form>
</div>

</body>
</html>
