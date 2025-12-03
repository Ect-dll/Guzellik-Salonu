<?php
include "admin_db.php";

$hata = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $kullanici = $_POST["username"];
    $sifre = $_POST["password"];

    if ($kullanici === $ADMIN_USERNAME && $sifre === $ADMIN_PASSWORD) {

        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_username"]   = $kullanici;

        header("Location: dashboard.php");
        exit;

    } else {
        $hata = "Kullanıcı adı veya şifre hatalı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Admin Giriş</title>
<script src="firewall.js" defer></script>
<style>
body {
    background:#f3f3f3;
    font-family: Arial;
}
.login-box {
    width: 360px;
    margin: 120px auto;
    background:white;
    padding:30px;
    border-radius:12px;
    box-shadow:0 0 15px rgba(0,0,0,0.15);
}
h2 {
    text-align:center;
    color:#E91E63;
}
input {
    width:100%;
    padding:10px;
    margin:10px 0;
    border:1px solid #ccc;
    border-radius:6px;
}
button {
    width:100%;
    padding:10px;
    background:#E91E63;
    border:none;
    border-radius:6px;
    color:white;
    font-size:16px;
    cursor:pointer;
}
.hata {
    color:red;
    text-align:center;
}
</style>
</head>

<body>

<div class="login-box">
    <h2>Admin Panel</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Kullanıcı adı" required>
        <input type="password" name="password" placeholder="Şifre" required>

        <?php if ($hata): ?>
            <p class="hata"><?= $hata ?></p>
        <?php endif; ?>

        <button type="submit">Giriş Yap</button>
    </form>
</div>

</body>
</html>
