<?php
include "db.php";

// --- Giriş kontrolü ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_fullname'] ?? "";

// --- Veritabanından randevuları çek ---
$stmt = $conn->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY date DESC, time DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// --- HTML tablo oluştur ---
if ($result->num_rows === 0) {
    $randevuHTML = '<p class="no-data">Hiç randevunuz bulunmamaktadır.</p>';
} else {
    $randevuHTML = '
    <table>
        <thead>
            <tr>
                <th>Hizmet</th>
                <th>Tarih</th>
                <th>Saat</th>
                <th>Durum</th>
            </tr>
        </thead>
        <tbody>
    ';

    while ($row = $result->fetch_assoc()) {
        $randevuHTML .= "
        <tr>
            <td>{$row['service']}</td>
            <td>{$row['date']}</td>
            <td>{$row['time']}</td>
            <td>{$row['status']}</td>
        </tr>";
    }

    $randevuHTML .= "</tbody></table>";
}

// --- Tema dosyasını yükle ---
$tema = file_get_contents("randevularim_tema.html");
$tema = str_replace("{{RANDEVULAR}}", $randevuHTML, $tema);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Randevularım</title>
    <script src="firewall.js" defer></script>
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
      <a href="randevularim.php" class="aktif">Randevularım</a>
      <a href="siparislerim.php">Siparişlerim</a>
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
