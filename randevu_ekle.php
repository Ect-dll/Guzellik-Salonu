<?php
include "db.php";
include "auth.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $service = trim($_POST['service']);
    $date    = $_POST['date'];
    $time    = $_POST['time'];
    $uid     = $_SESSION['user_id'];

    if ($service == "" || $date == "" || $time == "") {
        $message = "Lütfen tüm alanları doldurun.";
    } else {
        $stmt = $conn->prepare("INSERT INTO appointments (user_id, service, date, time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $uid, $service, $date, $time);
        if ($stmt->execute()) {
            header("Location: randevularim.php");
            exit;
        } else {
            $message = "Hata: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Randevu</title>
  <script src="firewall.js" defer></script>
  <style>
    body {
      font-family: sans-serif;
      margin: 0;
      padding: 0;
      background: #f3f3f3;
      color: #333;
    }

    header {
      background: #e91e63;
      color: white;
      padding: 20px;
      text-align: center;
    }

    nav {
      background: #f06292;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      position: relative;
    }

    .hamburger {
      display: none;
      font-size: 28px;
      background: none;
      border: none;
      color: white;
      cursor: pointer;
    }

    .nav-links {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      flex-grow: 1;
      margin-left: 20px;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      padding: 8px 12px;
      border-radius: 4px;
      transition: background-color 0.3s ease;
    }

    nav a:hover {
      background-color: #d81b60;
    }

    #kullaniciMenusu {
      color: white;
      position: relative;
      user-select: none;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    #kullaniciAdi {
      font-weight: bold;
    }

    #kullaniciMenusu button {
      background: none;
      border: none;
      color: white;
      font-size: 24px;
      cursor: pointer;
      vertical-align: middle;
      padding: 0;
    }

    #menuBox {
      display: none;
      position: absolute;
      right: 0;
      top: 35px;
      background: #f06292;
      border-radius: 6px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      min-width: 140px;
      z-index: 1000;
      flex-direction: column;
    }

    #menuBox a {
      padding: 10px 15px;
      display: block;
      color: white;
      text-decoration: none;
      font-weight: normal;
      border-bottom: 1px solid #d81b60;
      transition: background-color 0.3s ease;
    }

    #menuBox a:last-child {
      border-bottom: none;
    }

    #menuBox a:hover {
      background-color: #d81b60;
    }

    @media (max-width: 700px) {
      .nav-links {
        display: none;
        flex-direction: column;
        gap: 10px;
        background: #f06292;
        position: absolute;
        top: 55px;
        left: 0;
        width: 100%;
        padding: 10px 0;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 999;
        margin-left: 0;
      }
      .nav-links.show {
        display: flex;
      }
      .hamburger {
        display: block;
      }
      #kullaniciMenusu {
        order: 1;
      }
    }

    .container {
      max-width: 600px;
      margin: 40px auto;
      background: white;
      padding: 20px;
      border-radius: 10px;
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }

    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      box-sizing: border-box;
    }

    .btn {
      background: #e91e63;
      color: white;
      border: none;
      padding: 10px 20px;
      margin-top: 20px;
      border-radius: 5px;
      cursor: pointer;
    }

    .hidden {
      display: none;
    }

    .slide {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s ease-out;
    }

    .slide.show {
      max-height: 500px;
    }

    .priceBox {
      font-size: 1.1em;
      color: #e91e63;
      font-weight: bold;
      margin-top: 10px;
    }
  </style>

<script>
function toggleMenu() {
  const menu = document.getElementById('menuBox');
  menu.style.display = (menu.style.display === 'flex') ? 'none' : 'flex';
}

function toggleHamburger() {
  const nav = document.querySelector('.nav-links');
  nav.classList.toggle('show');
}
</script>

</head>
<body>

<header><h1>Randevu Al</h1></header>

<nav>
  <button class="hamburger" onclick="toggleHamburger()">☰</button>

  <div class="nav-links">
    <a href="index.php">Anasayfa</a>
    <a href="hakkimizda.php">Hakkımızda</a>
    <a href="urunler.php">Ürünler & Hizmetler</a>
    <a href="randevu_ekle.php">Randevu</a>
  </div>

  <div id="kullaniciMenusu">
    <span id="kullaniciAdi"><?php echo htmlspecialchars($_SESSION['user_fullname'] ?? "Kullanıcı"); ?></span>
    <button onclick="toggleMenu()">☰</button>

    <div id="menuBox">
      <a href="profil.php">Profil</a>
      <a href="randevularim.php">Randevularım</a>
      <a href="siparislerim.php">Siparişlerim</a>
      <a href="logout.php">Çıkış Yap</a>
    </div>
  </div>
</nav>

<main class="container">

<?php if ($message): ?>
  <p style="color:red; font-weight:bold;"><?= $message ?></p>
<?php endif; ?>

  <h3>Randevu Formu</h3>
  <form action="" method="POST">

    <label>Hizmet</label>
    <select name="service" id="service" required>
      <option value="">-- Seçiniz --</option>
      <option value="Cilt Bakımı">Cilt Bakımı</option>
      <option value="Manikür & Pedikür">Manikür & Pedikür</option>
      <option value="Saç Bakımı">Saç Bakımı</option>
    </select>

    <label>Tarih</label>
    <input type="date" name="date" required>

    <label>Saat</label>
    <select name="time" id="timeSelect" required>
      <option value="">-- Saat Seçiniz --</option>
      <option value="09:00">09:00</option>
      <option value="10:00">10:00</option>
      <option value="11:00">11:00</option>
      <option value="12:00">12:00</option>
      <option value="13:00">13:00</option>
      <option value="14:00">14:00</option>
      <option value="15:00">15:00</option>
      <option value="16:00">16:00</option>
    </select>

    <button type="submit" class="btn">Randevu Al</button>
  </form>
</main>

</body>
</html>