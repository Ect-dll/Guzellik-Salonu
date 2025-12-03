<?php
include "db.php";
include "auth.php";

$id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT fullname, email, phone, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil</title>
  <script src="firewall.js" defer></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #f9f9f9;
      color: #333;
    }

    header {
      background: #EFA3B8;
      color: white;
      padding: 20px;
      text-align: center;
    }

    nav {
      background: #E68EA7;
      padding: 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      margin: 0 10px;
    }

    nav a:hover {
      text-decoration: underline;
    }

    .nav-links {
      display: flex;
      gap: 20px;
      align-items: center;
      flex-wrap: wrap;
    }

    #kullaniciMenusu {
      display: flex;
      align-items: center;
      position: relative;
      color: white;
    }

    #menuBox {
      display: none;
      position: absolute;
      right: 0;
      top: 40px;
      background: white;
      border: 1px solid #ccc;
      border-radius: 5px;
      min-width: 160px;
      z-index: 1000;
    }

    #menuBox a {
      display: block;
      padding: 10px;
      color: black;
      text-decoration: none;
    }

    #menuBox a:hover {
      background: #f2f2f2;
    }

    #kullaniciAdi {
      margin-right: 10px;
      font-weight: bold;
    }

    main {
      padding: 40px 20px;
      max-width: 600px;
      margin: auto;
      background: white;
      margin-top: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    h2 {
      color: #EFA3B8;
    }

    p {
      font-size: 1.1em;
    }
  </style>
  <script>
  function toggleMenu() {
    const menu = document.getElementById('menuBox');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
  }
  </script>
</head>
<body>

<header><h1>Profil</h1></header>

<nav>
  <div class="nav-links">
    <a href="index.php">Anasayfa</a>
    <a href="hakkimizda.php">Hakkımızda</a>
    <a href="urunler.php">Ürünler & Hizmetler</a>
    <a href="randevu_ekle.php">Randevu</a>
  </div>

  <div id="kullaniciMenusu">
    <span id="kullaniciAdi"><?php echo htmlspecialchars($user['fullname']); ?></span>
    <button onclick="toggleMenu()" style="background:none; border:none; font-size: 24px; color: white;">☰</button>
    <div id="menuBox">
      <a href="profil.php">Profil</a>
      <a href="randevularim.php">Randevularım</a>
      <a href="siparislerim.php">Siparişlerim</a>
      <a href="logout.php">Çıkış Yap</a>
    </div>
  </div>
</nav>

<main>
  <h2>Merhaba, <?php echo htmlspecialchars($user['fullname']); ?></h2>
  <p><strong>E-posta:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
  <p><strong>Telefon:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
  <p><strong>Üyelik Tarihi:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
</main>

<footer style="text-align:center; padding:20px; background:#EFA3B8; color:white; margin-top:20px;">
  &copy; 2025 Dünya'nın İstediği Güzellik Merkezi
</footer>

</body>
</html>