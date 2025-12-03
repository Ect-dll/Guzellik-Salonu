<?php
include "db.php";

$logged_in = isset($_SESSION['user_id']);
$user_name = $logged_in ? $_SESSION['user_fullname'] ?? "" : "";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Anasayfa</title>
<script src="firewall.js" defer></script>
<style>
  
<?php echo file_get_contents("style.css"); ?>
</style>

<script>
function toggleMenu() {
  const menu = document.getElementById('menuBox');
  menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}

function toggleHamburger() {
  const nav = document.querySelector('.nav-links');
  nav.classList.toggle('show');
}
</script>

</head>
<body>

<header>
  <h1>Hoş Geldiniz</h1>
</header>

<nav>
  <button class="hamburger" onclick="toggleHamburger()">☰</button>

  <div class="nav-links">
    <a href="index.php">Anasayfa</a>
    <a href="hakkimizda.php">Hakkımızda</a>
    <a href="urunler.php">Ürünler & Hizmetler</a>
    <a href="randevu_ekle.php">Randevu</a>
  </div>

  <div id="kullaniciMenusu">
    <?php if($logged_in): ?>
      <span id="kullaniciAdi"><?= htmlspecialchars($user_name) ?></span>
      <button onclick="toggleMenu()" style="background:none; border:none; color:white; font-size:22px;">☰</button>

      <div id="menuBox">
        <a href="profil.php">Profil</a>
        <a href="randevularim.php">Randevularım</a>
        <a href="siparislerim.php">Siparişlerim</a>
        <a href="logout.php">Çıkış Yap</a>
      </div>

    <?php else: ?>
      <a href="login.php" style="color:white; font-weight:bold; text-decoration:none;">Giriş Yap</a>
    <?php endif; ?>
  </div>

</nav>

<main>
  <?php echo file_get_contents("index_tema.html"); ?>
</main>

</body>
</html>
