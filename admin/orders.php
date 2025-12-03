<?php
include "admin_db.php";
admin_giris_kontrol();
include "../db.php";

// Sipariş silme
if (isset($_GET["sil"])) {
    $id = (int)$_GET["sil"];
    $conn->query("DELETE FROM orders WHERE id = $id");
    header("Location: orders.php?silindi=1");
    exit;
}

// Siparişleri çek
$sql = "
SELECT orders.*, users.fullname 
FROM orders
LEFT JOIN users ON orders.user_id = users.id
ORDER BY orders.id DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Sipariş Yönetimi</title>
<script src="firewall.js" defer></script>
<style>
body {font-family: Arial; background:#f9f9f9; margin:0;}
nav {background:#E91E63; padding:15px; color:white; display:flex; justify-content:space-between;}
nav a {color:white; margin:0 10px; text-decoration:none; font-weight:bold;}

.container {
    max-width:1000px;
    margin:40px auto;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 0 12px rgba(0,0,0,0.12);
}

table {width:100%; border-collapse:collapse; margin-top:20px;}
th, td {padding:12px; text-align:left; border-bottom:1px solid #ddd;}
th {background:#F8BBD0;}

.btn {
    padding:6px 12px;
    border-radius:6px;
    color:white;
    text-decoration:none;
}
.sil {background:#f44336;}
.sil:hover {background:#c62828;}
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
    <h2>Sipariş Yönetimi</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Kullanıcı</th>
            <th>Ürün</th>
            <th>Adet</th>
            <th>Fiyat</th>
            <th>Tarih</th>
            <th>İşlemler</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row["id"] ?></td>
            <td><?= htmlspecialchars($row["fullname"] ?? "Bilinmiyor") ?></td>
            <td><?= htmlspecialchars($row["product"]) ?></td>
            <td><?= $row["quantity"] ?></td>
            <td><?= number_format($row["price"], 2) ?> TL</td>
            <td><?= $row["created_at"] ?></td>

            <td>
                <a class="btn sil" href="orders.php?sil=<?= $row['id'] ?>" 
                   onclick="return confirm('Bu siparişi silmek istediğine emin misin?')">
                   Sil
                </a>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>

</div>

</body>
</html>
