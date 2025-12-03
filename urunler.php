<?php
include "db.php";

// Kullanıcı giriş kontrolü
$logged_in = isset($_SESSION['user_id']);
$user_name = $logged_in ? ($_SESSION['user_fullname'] ?? "") : "";

// Ürünleri veritabanından çek
$query = $conn->query("SELECT * FROM products ORDER BY id DESC");
$urunler_html = "";

while ($urun = $query->fetch_assoc()) {
    $urunler_html .= '
    <div class="urun-card">
        <h3>' . htmlspecialchars($urun["name"]) . '</h3>
        <p>' . htmlspecialchars($urun["description"]) . '</p>

        <span class="price">' . number_format($urun["price"], 2) . ' TL</span>

        <form method="POST" action="siparis_olustur.php" style="margin-top:15px;">
            <input type="hidden" name="product_id" value="' . $urun["id"] . '">
            <input type="hidden" name="product_name" value="' . htmlspecialchars($urun["name"]) . '">
            <input type="hidden" name="product_price" value="' . $urun["price"] . '">

            <label>Adet:</label>
            <input type="number" name="quantity" value="1" min="1"
                   style="width:60px; padding:4px; margin-left:6px;">

            <button type="submit" class="siparis-btn"
                style="display:block; margin-top:10px; padding:8px 14px;
                       background:#d63384; color:white; border:none;
                       border-radius:8px; cursor:pointer;">
                Sepete Ekle
            </button>
        </form>
    </div>
    ';
}

// Tema dosyasını içeri al
$tema = file_get_contents("urunler_tema.html");

// Tema içindeki {{URUNLER}} bölümünü doldur
$tema = str_replace("{{URUNLER}}", $urunler_html, $tema);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürünler</title>
    <script src="firewall.js" defer></script>
    <link rel="stylesheet" href="style.css">

    <style>
        .urun-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .urun-card {
            background: white;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
            transition: 0.2s;
        }

        .urun-card:hover {
            transform: translateY(-3px);
            box-shadow: 0px 6px 18px rgba(0,0,0,0.15);
        }

        .price {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 12px;
            background: #f3b3c3;
            border-radius: 8px;
            font-weight: bold;
        }
    </style>

</head>
<body>

<!-- ÜST MENÜ (Tasarım bozulmasın diye buraya koyduk) -->
<nav>
  <button class="hamburger" onclick="toggleHamburger()">☰</button>

  <div class="nav-links">
    <a href="index.php">Anasayfa</a>
    <a href="hakkimizda.php">Hakkımızda</a>
    <a href="urunler.php" class="aktif">Ürünler & Hizmetler</a>
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
      <a href="login.php" style="color:white; font-weight:bold;">Giriş Yap</a>
    <?php endif; ?>
  </div>
</nav>

<!-- Tema içeriğini yazdır -->
<?= $tema ?>

<script>
function toggleHamburger() {
  document.querySelector(".nav-links").classList.toggle("show");
}

function toggleMenu() {
  const menu = document.getElementById("menuBox");
  menu.style.display = (menu.style.display === "block") ? "none" : "block";
}
</script>

</body>
</html>
