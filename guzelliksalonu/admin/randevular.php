<?php
include "admin_db.php";
admin_giris_kontrol();
include "../db.php";

// Randevu silme
if (isset($_GET["sil"])) {
    $id = (int)$_GET["sil"];
    $conn->query("DELETE FROM appointments WHERE id = $id");
    header("Location: randevular.php?silindi=1");
    exit;
}

// Durum güncelleme
if (isset($_GET["durum"]) && isset($_GET["id"])) {
    $id = (int)$_GET["id"];
    $durum = $_GET["durum"] === "1" ? "tamamlandı" : "beklemede";

    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $durum, $id);
    $stmt->execute();

    header("Location: randevular.php?durum=guncellendi");
    exit;
}

// Randevuları çek
$sql = "
SELECT appointments.*, users.fullname
FROM appointments
LEFT JOIN users ON appointments.user_id = users.id
ORDER BY appointments.id DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Randevu Yönetimi</title>
<script src="firewall.js" defer></script>
<style>
body {font-family: Arial; background:#f9f9f9; margin:0;}
nav {background:#E91E63; padding:15px; color:white; display:flex; justify-content:space-between;}
nav a {color:white; margin:0 10px; text-decoration:none; font-weight:bold;}

.container {
    max-width:1100px;
    margin:40px auto;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 0 12px rgba(0,0,0,0.12);
}

table {width:100%; border-collapse:collapse; margin-top:20px;}
th, td {padding:12px; text-align:left; border-bottom:1px solid #ddd;}
th {background:#F8BBD0;}

.btn {
    padding:6px 12px;
    border-radius:6px;
    color:white;
    text-decoration:none;
}
.red {background:#f44336;}
.green {background:#4CAF50;}
.orange {background:#FF9800;}
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

<div class="container">
    <h2>Randevu Yönetimi</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Kullanıcı</th>
            <th>Hizmet</th>
            <th>Tarih</th>
            <th>Saat</th>
            <th>Durum</th>
            <th>İşlemler</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row["id"] ?></td>
            <td><?= htmlspecialchars($row["fullname"] ?? "Bilinmiyor") ?></td>
            <td><?= htmlspecialchars($row["service"]) ?></td>
            <td><?= $row["date"] ?></td>
            <td><?= $row["time"] ?></td>
            <td><?= ucfirst($row["status"]) ?></td>

            <td>
                <!-- Beklemede yap -->
                <a class="btn orange" href="randevular.php?id=<?= $row['id'] ?>&durum=0">Beklemede</a>

                <!-- Tamamlandı yap -->
                <a class="btn green" href="randevular.php?id=<?= $row['id'] ?>&durum=1">Tamamlandı</a>

                <!-- Sil -->
                <a class="btn red"
                   href="randevular.php?sil=<?= $row['id'] ?>"
                   onclick="return confirm('Bu randevuyu silmek istiyor musun?')">
                   Sil
                </a>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>

</div>

</body>
</html>
