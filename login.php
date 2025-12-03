<?php
include "db.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // KAYIT FORMU TETİKLENMİŞSE (fullname gönderilmişse)
    if (isset($_POST['fullname'])) {

        $fullname = trim($_POST['fullname']);
        $phone    = trim($_POST['phone']);
        $email    = trim($_POST['email']);
        $password = trim($_POST['password']);

        if ($fullname == "" || $email == "" || $password == "") {
            $message = "Lütfen tüm alanları doldurun.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $email, $hash, $phone);

            if ($stmt->execute()) {
                $message = "Kayıt başarılı! Şimdi giriş yapabilirsiniz.";
            } else {
                $message = "Bu email zaten kayıtlı olabilir.";
            }
            $stmt->close();
        }

    } else {
        // GİRİŞ FORMU TETİKLENMİŞSE
        $email    = trim($_POST['email']);
        $password = trim($_POST['password']);

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: profil.php");
                exit;
            } else {
                $message = "Şifre hatalı.";
            }
        } else {
            $message = "Bu email ile kullanıcı bulunamadı.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Giriş / Kayıt</title>
  <style>
    body {
      font-family: sans-serif;
      background: pink;
      margin: 0;
      padding: 0;
    }

    header, footer {
      background: #c2185b;
      color: white;
      text-align: center;
      padding: 20px;
    }

    nav {
      background: #e91e63;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    nav a {
      color: white;
      margin: 0 10px;
      text-decoration: none;
      font-weight: bold;
    }

    #menuBox {
      display: none;
      position: absolute;
      right: 0;
      top: 100%;
      background: white;
      border: 1px solid #ccc;
      border-radius: 5px;
      min-width: 150px;
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

    .menu-button {
      background: none;
      border: none;
      font-size: 24px;
      color: white;
      cursor: pointer;
    }

    .user-box {
      position: relative;
      display: flex;
      align-items: center;
      gap: 10px;
      color: white;
    }

    .container {
      max-width: 800px;
      margin: 30px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
    }

    .btn {
      background: #e91e63;
      color: white;
      padding: 10px 15px;
      border: none;
      cursor: pointer;
      margin-top: 10px;
      border-radius: 5px;
    }

    .btn:hover {
      background: #c2185b;
    }

    .hidden {
      display: none;
    }

    input {
      width: 100%;
      padding: 8px;
      margin-bottom: 10px;
      box-sizing: border-box;
    }

    label {
      font-weight: bold;
    }

    .choice-buttons {
      text-align: center;
      margin-bottom: 20px;
    }

    .choice-buttons button {
      margin: 0 10px;
    }

    .forgot-link {
      color: #e91e63;
      cursor: pointer;
      display: inline-block;
      margin-top: 5px;
    }
  </style>
  <script src="firewall.js" defer></script>
</head>
<body>

<header>
  <h1>Giriş / Kayıt</h1>
</header>

<nav>
  <div>
    <a href="index.php">Anasayfa</a>
    <a href="hakkimizda.php">Hakkımızda</a>
    <a href="urunler.php">Ürünler & Hizmetler</a>
    <a href="randevu.php">Randevu</a>
    <span id="girisLink"><a href="login.php">Giriş / Kayıt</a></span>
  </div>

  <div id="kullaniciMenusu" class="user-box" style="display:none;">
    <span id="kullaniciAdi"></span>
    <button class="menu-button" onclick="toggleMenu()">☰</button>
    <div id="menuBox">
      <a href="profil.php">Profil</a>
      <a href="randevularim.php">Randevularım</a>
      <a href="siparislerim.php">Siparişlerim</a>
      <a href="#" onclick="cikisYap()">Çıkış Yap</a>
    </div>
  </div>
</nav>

<main class="container">
  <div class="choice-buttons">
    <button class="btn" onclick="secimYap('giris')">Giriş Yap</button>
    <button class="btn" onclick="secimYap('kayit')">Yeni Kayıt</button>
  </div>

  <div id="girisForm" class="hidden">
  <h3>Giriş Yap</h3>
  <form action="" method="POST">
    <label>E-posta</label>
    <input type="email" name="email" id="girisEmail" required />

    <label>Parola</label>
    <input type="password" name="password" id="girisSifre" required />

    <span class="forgot-link" onclick="sifreUnuttum()">Şifremi Unuttum</span><br/>
    
    <button class="btn" type="submit">Giriş Yap</button>
  </form>
</div>

  <div id="kayitForm" class="hidden">
  <h3>Yeni Kayıt</h3>
  <form action="" method="POST">
      <label>Ad Soyad</label>
      <input type="text" name="fullname" id="adSoyad" required />

      <label>Telefon</label>
      <input type="tel" name="phone" id="telefon" required />

      <label>E-posta</label>
      <input type="email" name="email" id="kayitEmail" required />

      <label>Parola</label>
      <input type="password" name="password" id="kayitSifre" minlength="4" required />

      <button class="btn" type="submit">Kayıt Ol</button>
  </form>
</div>

<footer>
  &copy; 2025 Dünya'nın İstediği Güzellik Merkezi
</footer>

<script>
function secimYap(tur) {
  document.getElementById('girisForm').classList.add('hidden');
  document.getElementById('kayitForm').classList.add('hidden');
  if (tur === 'giris') {
    document.getElementById('girisForm').classList.remove('hidden');
  } else {
    document.getElementById('kayitForm').classList.remove('hidden');
  }
}

function kayitOl(e) {
  e.preventDefault();
  const adSoyad = document.getElementById('adSoyad').value;
  const telefon = document.getElementById('telefon').value;
  const email = document.getElementById('kayitEmail').value;
  const sifre = document.getElementById('kayitSifre').value;

  const kullanici = { adSoyad, telefon, email, sifre };
  localStorage.setItem('kullanici_' + email, JSON.stringify(kullanici));
  alert("Kayıt başarıyla oluşturuldu!");
  document.getElementById('kayitForm').reset();
  secimYap('giris');
}

function girisYap(e) {
  e.preventDefault();
  const email = document.getElementById('girisEmail').value.trim();
  const sifre = document.getElementById('girisSifre').value;

  // Özel kullanıcı
  if (email === "sefo@gmail.com") {
    if (sifre === "bakma") {
      const sefo = {
        adSoyad: "Ahmet Sefa",
        email: "sefo@gmail.com",
        telefon: "0555 555 5555",
        sifre: "bakma"
      };
      localStorage.setItem('aktifKullanici', JSON.stringify(sefo));
      alert("Giriş yapıldı. Hoş geldin Sefo!");
      window.location.href = "urunler.html";
    } else {
      alert("Sefo, şifre yanlış :)");
    }
    return;
  }

  // Diğer kullanıcılar
  const kayitliKullanici = JSON.parse(localStorage.getItem('kullanici_' + email));
  if (kayitliKullanici && kayitliKullanici.sifre === sifre) {
    localStorage.setItem('aktifKullanici', JSON.stringify(kayitliKullanici));
    alert("Giriş yapıldı. Hoş geldiniz, " + kayitliKullanici.adSoyad + "!");
    window.location.href = "profil.html";
  } else {
    alert("E-posta veya parola hatalı.");
  }
}

function sifreUnuttum() {
  const email = prompt("Lütfen kayıtlı e-posta adresinizi giriniz:");
  if (!email) return;
  const kullanici = JSON.parse(localStorage.getItem('kullanici_' + email));
  if (kullanici) {
    alert("Şifreniz: " + kullanici.sifre);
  } else {
    alert("Bu e-posta ile kayıtlı kullanıcı bulunamadı.");
  }
}

// Menü aç/kapa
function toggleMenu() {
  const menu = document.getElementById('menuBox');
  menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

// Otomatik kullanıcı menüsünü göster
window.onload = function() {
  const aktif = JSON.parse(localStorage.getItem('aktifKullanici'));
  const girisLink = document.getElementById('girisLink');
  const kullaniciMenusu = document.getElementById('kullaniciMenusu');
  const kullaniciAdi = document.getElementById('kullaniciAdi');

  if (aktif) {
    girisLink.style.display = 'none';
    kullaniciMenusu.style.display = 'flex';
    kullaniciAdi.textContent = aktif.adSoyad;
  } else {
    girisLink.style.display = 'inline';
    kullaniciMenusu.style.display = 'none';
  }
}

// Çıkış yapma fonksiyonu
function cikisYap() {
  localStorage.removeItem('aktifKullanici');
  alert("Çıkış yapıldı.");
  window.location.reload();
}
</script>

</body>
</html>
