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
  // PESAN SUKSES
  // ===============================
  $pesan = "<div class='alert alert-success'>
              Data berhasil disimpan dan diprediksi.
            </div>";
}
?>

<!doctype html>

<html
  lang="en"
  class="layout-menu-fixed layout-compact"
  data-../assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Demo: Dashboard - Analytics | Sneat - Bootstrap Dashboard FREE</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />

  <!-- Core CSS -->
  <!-- build:css ../assets/vendor/css/theme.css  -->

  <link rel="stylesheet" href="../assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />

  <!-- Vendors CSS -->

  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <!-- endbuild -->

  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

  <!-- Page CSS -->

  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>
  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

  <script src="../assets/js/config.js"></script>
</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->
      <?php include 'partials/sidebar.php'; ?>
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Navbar -->
        <?php include 'partials/navbar.php'; ?>
        <!-- / Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row g-6 mb-6">
              <!-- Basic -->
              <form method="POST">
                <div class="col-md-6">
                  <div class="card">
                    <h5 class="card-header">Prediksi Potensi Stunting</h5>

                    <div class="card-body demo-vertical-spacing demo-only-element">

                      <?= $pesan ?>

                      <div>
                        <label class="form-label">Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="form-control" placeholder="Nama lengkap" required>
                      </div>

                      <div>
                        <label class="form-label">Usia Ibu</label>
                        <input type="number" name="usia" class="form-control" placeholder="Masukkan usia ibu" required>
                      </div>

                      <div>
                        <label class="form-label">Tinggi Badan</label>
                        <div class="input-group">
                          <input type="number" step="0.1" name="tinggi_badan" class="form-control" placeholder="Masukkan tinggi badan" required>
                          <span class="input-group-text">cm</span>
                        </div>
                      </div>

                      <div>
                        <label class="form-label">Lingkar Lengan Atas (LILA)</label>
                        <div class="input-group">
                          <input type="number" step="0.1" name="lingkar_lengan_atas" class="form-control" placeholder="Masukkan lingkar lengan atas" required>
                          <span class="input-group-text">cm</span>
                        </div>
                      </div>

                      <div>
                        <label class="form-label">Kadar Hemoglobin</label>
                        <div class="input-group">
                          <input type="number" step="0.1" name="kadar_hb" class="form-control" placeholder="Masukkan kadar hemoglobin" required>
                          <span class="input-group-text">g/dL</span>
                        </div>
                      </div>

                      <div class="demo-inline-spacing text-end">
                        <button type="submit" class="btn btn-primary">Prediksi</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                      </div>

                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- / Content -->

          <!-- Footer -->
          <?php include 'partials/footer.php'; ?>
          <!-- / Footer -->

          <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>
  <!-- / Layout wrapper -->

  <!-- Core JS -->

  <script src="../assets/vendor/libs/jquery/jquery.js"></script>

  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>

  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

  <script src="../assets/vendor/js/menu.js"></script>

  <!-- endbuild -->

  <!-- Vendors JS -->
  <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

  <!-- Main JS -->

  <script src="../assets/js/main.js"></script>

  <!-- Page JS -->
  <script src="../assets/js/dashboards-analytics.js"></script>

  <!-- Place this tag before closing body tag for github widget button. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
