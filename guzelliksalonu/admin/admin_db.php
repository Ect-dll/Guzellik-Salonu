<?php
session_start();

// Admin sabit bilgileri
$ADMIN_USERNAME = "Adminn";
$ADMIN_PASSWORD = "11235813256.Ts";

// Admin giriş kontrol fonksiyonu
function admin_giris_kontrol() {
    if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
        header("Location: index.php?auth=0");
        exit;
    }
}
?>
