<?php
require_once "../config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode([
    "success" => false,
    "message" => "Invalid request method."
  ]);
  exit;
}

/* ==========================================
   1️⃣ AMBIL PARAMETER AKTIF
========================================== */

$params = [];
$result = mysqli_query($conn, "
  SELECT nama_parameter
  FROM parameter
  WHERE status_aktif = 1
  ORDER BY id ASC
");

while ($row = mysqli_fetch_assoc($result)) {
  $params[] = $row['nama_parameter'];
}

if (count($params) == 0) {
  echo json_encode([
    "success" => false,
    "message" => "Minimal 1 parameter harus aktif sebelum training."
  ]);
  exit;
}

/* ==========================================
   2️⃣ SIMPAN active_params.json
========================================== */

$paramPath = "../python/model/active_params.json";

if (!is_dir(dirname($paramPath))) {
  mkdir(dirname($paramPath), 0777, true);
}

file_put_contents(
  $paramPath,
  json_encode($params, JSON_PRETTY_PRINT)
);

/* ==========================================
   3️⃣ JALANKAN PYTHON
========================================== */

$python = "../python/python.exe";
$script = "../python/train_model.py";

$command = "\"$python\" \"$script\" 2>&1";
$output = shell_exec($command);

if (!$output) {
  echo json_encode([
    "success" => false,
    "message" => "Training gagal: Python tidak mengembalikan output."
  ]);
  exit;
}

/* ==========================================
   4️⃣ DECODE LANGSUNG
========================================== */

$output = trim($output);
$data = json_decode($output, true);

if (json_last_error() !== JSON_ERROR_NONE) {
  echo json_encode([
    "success" => false,
    "message" => "Training gagal: Output Python bukan JSON valid.",
    "debug_output" => $output
  ]);
  exit;
}

/* ==========================================
   5️⃣ RETURN RESULT
========================================== */

echo json_encode($data);
exit;
