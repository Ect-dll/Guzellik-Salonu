<?php
include "db.php";
include "auth.php";

$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT service, date, time, status, created_at FROM appointments WHERE user_id = ? ORDER BY date DESC, time DESC");
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Randevularım</title>
  <script src="firewall.js" defer></script>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: #f9f9f9;
  }

  header {
    background: #EFA3B8;
    color: white;
    padding: 20px;
    text-align: center;
  }

  nav {
    background: #E68EA7;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
  }

  nav a {
    color: white;
    font-weight: bold;
    text-decoration: none;
    margin: 0 10px;
  }

  nav a:hover {
    text-decoration: underline;
  }

  .nav-links {
    display: flex;
    gap: 20px;
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
    background: #eee;
  }

  table {
    width: 90%;
    margin: 30px auto;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
  }

  th, td {
    padding: 14px;
    border: 1px solid #ddd;
    text-align: left;
  }

  th {
    background: #EFA3B8;
    color: white;
    font-size: 18px;
  }

  tr:nth-child(even) {
    background: #f8f8f8;
  }

  .status {
    padding: 6px 10px;
    border-radius: 6px;
    color: white;
    font-weight: bold;
  }

  .beklemede { background: #FFA726; }
  .onaylandi { background: #66BB6A; }
  .iptal   { background: #EF5350; }
</style>

<script>
function toggleMenu() {
  const menu = document.getElementById('menuBox');
  menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}
</script>

</head>

<body>

<header><h1>Randevularım</h1></header>

<nav>
  <div class="nav-links">
    <a href="index.php">Anasayfa</a>
    <a href="hakkimizda.php">Hakkımızda</a>
    <a href="urunler.php">Ürünler & Hizmetler</a>
    <a href="randevu_ekle.php">Randevu</a>
  </div>

  <div id="kullaniciMenusu">
    <span id="kullaniciAdi"><?php echo htmlspecialchars($_SESSION['user_fullname'] ?? "Kullanıcı"); ?></span>
    <button onclick="toggleMenu()" style="background:none; border:none; font-size: 24px; color: white;">☰</button>

    <div id="menuBox">
      <a href="profil.php">Profil</a>
      <a href="randevularim.php">Randevularım</a>
      <a href="siparislerim.php">Siparişlerim</a>
      <a href="logout.php">Çıkış Yap</a>
    </div>
  </div>
</nav>


<table>
  <tr>
    <th>Hizmet</th>
    <th>Tarih</th>
    <th>Saat</th>
    <th>Durum</th>
    <th>Oluşturulma</th>
  </tr>

  <?php while ($row = $res->fetch_assoc()): ?>
  <tr>
    <td><?= htmlspecialchars($row['service']) ?></td>
    <td><?= htmlspecialchars($row['date']) ?></td>
    <td><?= htmlspecialchars($row['time']) ?></td>

    <td>
      <?php
        $durum = $row['status'];
        $renk  = "beklemede";

        if ($durum == "onaylandi") $renk = "onaylandi";
        if ($durum == "iptal")     $renk = "iptal";
      ?>
      <span class="status <?= $renk ?>"><?= htmlspecialchars($durum) ?></span>
    </td>

    <td><?= htmlspecialchars($row['created_at']) ?></td>
  </tr>
  <?php endwhile; ?>

</table>

</body>
</html>