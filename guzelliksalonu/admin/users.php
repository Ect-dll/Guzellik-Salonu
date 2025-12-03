<?php
include "admin_db.php";
admin_giris_kontrol();
include "../db.php";

// Kullanıcı silme işlemi
if (isset($_GET["sil"])) {
    $id = (int)$_GET["sil"];
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: users.php?silindi=1");
    exit;
}

// Kullanıcıları çek
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Kullanıcı Yönetimi</title>
<script src="firewall.js" defer></script>
<style>
body {font-family: Arial; background:#f9f9f9; margin:0;}
nav {background:#E91E63; padding:15px; color:white; display:flex; justify-content:space-between;}
nav a {color:white; margin:0 10px; text-decoration:none; font-weight:bold;}

.container {
    max-width:1100px;
    margin:40px auto;
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 0 12px rgba(0,0,0,0.12);
}

table {width:100%; border-collapse:collapse; margin-top:20px;}
th, td {padding:12px; border-bottom:1px solid #ddd; text-align:left;}
th {background:#F8BBD0;}

.btn {
    padding:6px 12px;
    border-radius:6px;
    color:white;
    text-decoration:none;
}
.red {background:#f44336;}
.red:hover {background:#c62828;}
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
    <h2>Kullanıcı Yönetimi</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Ad Soyad</th>
            <th>E-mail</th>
            <th>Telefon</th>
            <th>Kayıt Tarihi</th>
            <th>İşlemler</th>
        </tr>

        <?php while ($user = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $user["id"] ?></td>
            <td><?= htmlspecialchars($user["fullname"]) ?></td>
            <td><?= htmlspecialchars($user["email"]) ?></td>
            <td><?= htmlspecialchars($user["phone"]) ?></td>
            <td><?= $user["created_at"] ?></td>

            <td>
                <a class="btn red" href="users.php?sil=<?= $user['id'] ?>"
                   onclick="return confirm('Bu kullanıcıyı silmek istiyor musun?')">
                   Sil
                </a>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>

</div>

</body>
</html>
