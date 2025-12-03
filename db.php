<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "batuhan_panel";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("MySQL bağlantı hatası: " . $conn->connect_error);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
