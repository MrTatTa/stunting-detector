<?php
require_once "../config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

  // Ambil JSON terakhir dari output
  $matches = [];
  preg_match_all('/\{.*\}$/s', $output, $matches);

  if (!empty($matches[0])) {
    $json_str = end($matches[0]);
    $data = json_decode($json_str, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      echo json_encode([
        "success" => false,
        "message" => "Training gagal: Output Python bukan JSON valid."
      ]);
    } else {
      echo json_encode($data);
    }
  } else {
    echo json_encode([
      "success" => false,
      "message" => "Training gagal: Tidak ada output JSON dari Python."
    ]);
  }
  exit;
}
