<?php
include "db.php";

// --- Giriş kontrolü ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_fullname'] ?? "";

// --- Siparişleri veritabanından çek ---
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// --- Sipariş tablosu oluştur ---
if ($result->num_rows === 0) {
    $siparisHTML = '<p class="no-data">Hiç siparişiniz bulunmamaktadır.</p>';
} else {
    $siparisHTML = '
    <table>
        <thead>
            <tr>
                <th>Ürün</th>
                <th>Adet</th>
                <th>Birim Fiyat</th>
                <th>Toplam</th>
                <th>Tarih</th>
            </tr>
        </thead>
        <tbody>
    ';

    while ($row = $result->fetch_assoc()) {

        $total = $row["price"] * $row["quantity"];

        $siparisHTML .= "
        <tr>
            <td>{$row['product']}</td>
            <td>{$row['quantity']}</td>
            <td>{$row['price']} ₺</td>
            <td>{$total} ₺</td>
            <td>{$row['created_at']}</td>
        </tr>
        ";
    }

    $siparisHTML .= "</tbody></table>";
}

// --- Tema dosyasını yükle ---
$tema = file_get_contents("siparislerim_tema.html");
$tema = str_replace("{{SIPARISLER}}", $siparisHTML, $tema);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Siparişlerim</title>

    <link rel="stylesheet" href="style.css">

</head>
<body>

<!-- ÜST PHP MENÜ -->
<nav>
  <button class="hamburger" onclick="toggleHamburger()">☰</button>

  <div class="nav-links">
    <a href="index.php">Anasayfa</a>
    <a href="hakkimizda.php">Hakkımızda</a>
    <a href="urunler.php">Ürünler & Hizmetler</a>
    <a href="randevu_ekle.php">Randevu</a>
  </div>

  <div id="kullaniciMenusu">
    <span id="kullaniciAdi"><?= htmlspecialchars($user_name) ?></span>
    <button onclick="toggleMenu()">☰</button>

    <div id="menuBox">
      <a href="profil.php">Profil</a>
      <a href="randevularim.php">Randevularım</a>
      <a href="siparislerim.php" class="aktif">Siparişlerim</a>
      <a href="logout.php">Çıkış Yap</a>
    </div>
  </div>
</nav>

<!-- Tema içeriği -->
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
