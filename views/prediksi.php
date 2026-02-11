<?php
require_once "../config.php";

$pesan = "";

// ===============================
// PROSES FORM
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // ===============================
  // AMBIL DATA FORM
  // ===============================
  $nama_ibu = htmlspecialchars(trim($_POST['nama_ibu']));
  $usia     = (int) $_POST['usia'];
  $tinggi   = (float) $_POST['tinggi_badan'];
  $lila     = (float) $_POST['lingkar_lengan_atas'];
  $hb       = (float) $_POST['kadar_hb'];

  // ===============================
  // VALIDASI
  // ===============================
  if ($usia <= 0 || $tinggi <= 0 || $lila <= 0 || $hb <= 0) {
    $pesan = "<div class='alert alert-danger'>Input tidak valid.</div>";
    return;
  }

  // ===============================
  // SIMPAN DATA IBU HAMIL
  // ===============================
  $stmt = mysqli_prepare(
    $conn,
    "INSERT INTO ibu_hamil
     (nama_ibu, usia, tinggi_badan, lingkar_lengan_atas, kadar_hb, created_by)
     VALUES (?, ?, ?, ?, ?, ?)"
  );

  $created_by = $_SESSION['user_id'] ?? null;

  mysqli_stmt_bind_param(
    $stmt,
    "sidddi",
    $nama_ibu,
    $usia,
    $tinggi,
    $lila,
    $hb,
    $created_by
  );

  if (!mysqli_stmt_execute($stmt)) {
    $pesan = "<div class='alert alert-danger'>Gagal menyimpan data ibu.</div>";
    return;
  }

  // ===============================
  // AMBIL ID IBU
  // ===============================
  $ibu_id = mysqli_insert_id($conn);

  // ===============================
  // PANGGIL PYTHON
  // ===============================
  $python = "../python/python.exe";
  $script = "../python/predict.py";

  // HANYA 4 PARAMETER SESUAI MODEL
  $command = "\"$python\" \"$script\" $usia $tinggi $lila $hb 2>&1";
  $output = shell_exec($command);

  if ($output === null || trim($output) === "") {
    die("Python tidak mengembalikan output.<pre>$command</pre>");
  }

  $result = json_decode($output, true);

  // ===============================
  // MAPPING HASIL KE ENUM DATABASE
  // ===============================
  $hasil_db = null;

  if ($result['hasil'] === 'STUNTING') {
    $hasil_db = 'Berisiko Stunting';
  } elseif ($result['hasil'] === 'NORMAL') {
    $hasil_db = 'Tidak Berisiko';
  }

  if (json_last_error() !== JSON_ERROR_NONE) {
    die("Output Python bukan JSON valid:<pre>$output</pre>");
  }

  // ===============================
  // SIMPAN HASIL PREDIKSI
  // ===============================
  $stmt = mysqli_prepare(
    $conn,
    "INSERT INTO prediksi (ibu_id, hasil, probabilitas)
     VALUES (?, ?, ?)"
  );

  mysqli_stmt_bind_param(
    $stmt,
    "isd",
    $ibu_id,
    $hasil_db,
    $result['probabilitas']
  );

  mysqli_stmt_execute($stmt);
  $prediksi_id = mysqli_insert_id($conn);

  // ===============================
  // SIMPAN FAKTOR RISIKO
  // ===============================
  $map = [
    'usia' => $usia,
    'tinggi_badan' => $tinggi,
    'lila' => $lila,
    'hb' => $hb
  ];

  foreach ($result['faktor'] as $param => $kontribusi) {

    $nilai = $map[$param] ?? 0;

    $stmt = mysqli_prepare(
      $conn,
      "INSERT INTO faktor_risiko
       (prediksi_id, parameter, nilai, kontribusi)
       VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
      $stmt,
      "isdd",
      $prediksi_id,
      $param,
      $nilai,
      $kontribusi
    );

    mysqli_stmt_execute($stmt);
  }

  // ===============================
  // PESAN VISUAL HASIL
  // ===============================
  $warna = $hasil_db == 'Berisiko Stunting' ? 'danger' : 'success';
  $label = $hasil_db == 'Berisiko Stunting' ? 'BERISIKO STUNTING' : 'TIDAK BERISIKO';

  $persen = round($result['probabilitas'] * 100, 2);

  $pesan = "
  <div class='card border-$warna mt-4 animate-result'>
    <div class='card-body text-center'>
      <h5 class='text-$warna mb-2'>$label</h5>
      <h1 class='fw-bold'>$persen%</h1>
      <p class='text-muted mb-0'>Probabilitas Prediksi</p>
    </div>
  </div>
  ";
}
?>

<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Prediksi Stunting</title>

  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico">
  <link rel="stylesheet" href="../assets/vendor/css/core.css">
  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css">
  <link rel="stylesheet" href="../assets/css/demo.css">
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">

  <style>
    .animate-result {
      animation: pop .5s ease;
    }

    @keyframes pop {
      from {
        transform: scale(.85);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }
  </style>

  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>
</head>

<body>

  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <?php include 'partials/sidebar.php'; ?>

      <div class="layout-page">
        <?php include 'partials/navbar.php'; ?>

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row justify-content-center">
              <div class="col-lg-6 col-md-8">

                <form method="POST">
                  <div class="card">
                    <h5 class="card-header text-center">Prediksi Potensi Stunting</h5>

                    <div class="card-body">

                      <?= $pesan ?>

                      <div class="mb-3">
                        <label class="form-label">Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="form-control" required>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Usia Ibu</label>
                        <input type="number" name="usia" class="form-control" required>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Tinggi Badan</label>
                        <div class="input-group">
                          <input type="number" step="0.1" name="tinggi_badan"
                            class="form-control" required>
                          <span class="input-group-text">cm</span>
                        </div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Lingkar Lengan Atas (LILA)</label>
                        <div class="input-group">
                          <input type="number" step="0.1" name="lingkar_lengan_atas"
                            class="form-control" required>
                          <span class="input-group-text">cm</span>
                        </div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Kadar Hemoglobin</label>
                        <div class="input-group">
                          <input type="number" step="0.1" name="kadar_hb" class="form-control"
                            required>
                          <span class="input-group-text">g/dL</span>
                        </div>
                      </div>

                      <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">Prediksi</button>
                      </div>

                    </div>
                  </div>
                </form>

              </div>
            </div>

          </div>

          <?php include 'partials/footer.php'; ?>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>
  <script src="../assets/js/main.js"></script>
  <div class="layout-overlay layout-menu-toggle"></div>



</body>

</html>