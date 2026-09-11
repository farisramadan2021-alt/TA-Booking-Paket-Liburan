<?php

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "booking_liburan";

$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>