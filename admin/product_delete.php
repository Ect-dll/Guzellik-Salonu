<?php
include "admin_db.php";
admin_giris_kontrol();
include "../db.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: products.php");
    exit;
}

$id = (int)$_GET["id"];

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: products.php?sil=ok");
exit;
