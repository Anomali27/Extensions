<?php

$servername = "localhost";   // default server Laragon
$username   = "root";        // default user Laragon
$password   = "";            // default password kosong
$dbname     = "playstation-biling"; // nama database kamu

// Membuat koneksi
$connection = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($connection->connect_error) {
    die("Koneksi gagal: " . $connection->connect_error);
}

?>
