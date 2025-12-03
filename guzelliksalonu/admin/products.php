<?php
include "admin_db.php";
admin_giris_kontrol();
include "../db.php";

// Ürünleri çek
$query = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Ürün Yönetimi</title>
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
    max-width:900px;
    margin:40px auto;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.12);
}
table {
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}
th, td {
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:center;
}
th {
    background:#f8bbd0;
}
.action-btn {
    padding:6px 10px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    color:white;
    font-weight:bold;
}
.edit-btn { background:#2196F3; }
.delete-btn { background:#d9534f; }
.add-btn {
    background:#4CAF50;
    padding:10px 16px;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
    margin-bottom:15px;
    font-weight:bold;
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

    <h2>Ürün Yönetimi</h2>

    <a href="product_add.php">
        <button class="add-btn">+ Yeni Ürün Ekle</button>
    </a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ürün Adı</th>
                <th>Açıklama</th>
                <th>Fiyat</th>
                <th>İşlemler</th>
            </tr>
        </thead>

        <tbody>
            <?php while($urun = $query->fetch_assoc()): ?>
            <tr>
                <td><?= $urun["id"] ?></td>
                <td><?= htmlspecialchars($urun["title"]) ?></td>
                <td><?= htmlspecialchars($urun["description"]) ?></td>
                <td><?= $urun["price"] ?> TL</td>

                <td>
                    <a href="product_edit.php?id=<?= $urun["id"] ?>">
                        <button class="action-btn edit-btn">Düzenle</button>
                    </a>

                    <a href="product_delete.php?id=<?= $urun["id"] ?>"
                       onclick="return confirm('Bu ürünü silmek istediğine emin misin?');">
                        <button class="action-btn delete-btn">Sil</button>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>
