<?php
include "admin_db.php";
admin_giris_kontrol();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
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
.box {
    max-width:900px;
    margin:40px auto;
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 0 12px rgba(0,0,0,0.12);
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

<div class="box">
    <h1>Hoş geldin, <?= $_SESSION["admin_username"] ?> 👑</h1>
    <p>Soldaki menüden yönetim işlemlerini yapabilirsin.</p>
</div>

</body>
</html>
