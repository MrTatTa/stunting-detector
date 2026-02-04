<?php
// ===============================
// CONFIG DATABASE
// ===============================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "stunting_prediction";

// ===============================
// KONEKSI DATABASE
// ===============================
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
  die("Koneksi database gagal: " . mysqli_connect_error());
}

// ===============================
// SET TIMEZONE
// ===============================
date_default_timezone_set("Asia/Jakarta");

// ===============================
// START SESSION
// ===============================
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
