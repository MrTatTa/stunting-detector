<?php
require_once "../config.php";

$pesan = "";

/* ======================================================
   PROSES FORM
====================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $nama_ibu = htmlspecialchars(trim($_POST['nama_ibu']));
  $created_by = $_SESSION['user_id'] ?? null;

  if (empty($nama_ibu)) {
    $pesan = "<div class='alert alert-danger'>Nama ibu wajib diisi.</div>";
  } else {

    /* ================================
       AMBIL PARAMETER AKTIF
    ================================= */
    $param_query = mysqli_query(
      $conn,
      "SELECT * FROM parameter
       WHERE status_aktif = 1
       ORDER BY id ASC"
    );

    $data_input = [];
    $nilai_for_python = [];

    while ($param = mysqli_fetch_assoc($param_query)) {

      $nama_param = $param['nama_parameter'];
      $nilai = $_POST[$nama_param] ?? null;

      if ($nilai === null || $nilai === '') {
        $pesan = "<div class='alert alert-danger'>
                    Input " . ucwords(str_replace('_', ' ', $nama_param)) . " tidak valid.
                  </div>";
        break;
      }

      $data_input[] = [
        'parameter_id' => $param['id'],
        'nama' => $nama_param,
        'nilai' => (float)$nilai
      ];

      $nilai_for_python[] = (float)$nilai;
    }

    if (empty($pesan)) {

      /* ================================
         INSERT IBU (IDENTITAS SAJA)
      ================================= */
      $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO ibu_hamil (nama_ibu, created_by)
         VALUES (?, ?)"
      );

      mysqli_stmt_bind_param(
        $stmt,
        "si",
        $nama_ibu,
        $created_by
      );

      if (!mysqli_stmt_execute($stmt)) {
        $pesan = "<div class='alert alert-danger'>Gagal menyimpan data ibu.</div>";
      } else {

        $ibu_id = mysqli_insert_id($conn);

        /* ================================
           INSERT NILAI PARAMETER
        ================================= */
        foreach ($data_input as $item) {

          $stmt_np = mysqli_prepare(
            $conn,
            "INSERT INTO nilai_parameter
             (ibu_id, parameter_id, nilai)
             VALUES (?, ?, ?)"
          );

          mysqli_stmt_bind_param(
            $stmt_np,
            "iid",
            $ibu_id,
            $item['parameter_id'],
            $item['nilai']
          );

          mysqli_stmt_execute($stmt_np);
        }

        /* ================================
           PANGGIL PYTHON (DYNAMIC ARG)
        ================================= */
        $python = "../python/python.exe";
        $script = "../python/predict.py";

        $args = implode(" ", $nilai_for_python);
        $command = "\"$python\" \"$script\" $args 2>&1";

        $output = shell_exec($command);

        if ($output === null || trim($output) === "") {
          die("Python tidak mengembalikan output.<pre>$command</pre>");
        }

        $result = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
          die("Output Python bukan JSON valid:<pre>$output</pre>");
        }

        /* ================================
   VALIDASI OUTPUT PYTHON
================================ */

        if (!isset($result['hasil']) || !isset($result['probabilitas'])) {

          die("
    Python tidak mengembalikan format yang benar:
    <pre>$output</pre>
  ");
        }

        /* ================================
        MAPPING HASIL
        ================================ */

        $hasil_db = null;

        if ($result['hasil'] === 'STUNTING') {
          $hasil_db = 'Berisiko Stunting';
        } elseif ($result['hasil'] === 'NORMAL') {
          $hasil_db = 'Tidak Berisiko';
        }

        if ($hasil_db === null) {
          die("Nilai hasil dari Python tidak dikenali: " . $result['hasil']);
        }

        /* ================================
        SIMPAN HASIL PREDIKSI
        ================================= */
        $stmt_pred = mysqli_prepare(
          $conn,
          "INSERT INTO prediksi
           (ibu_id, hasil, probabilitas)
           VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
          $stmt_pred,
          "isd",
          $ibu_id,
          $hasil_db,
          $result['probabilitas']
        );

        mysqli_stmt_execute($stmt_pred);
        $prediksi_id = mysqli_insert_id($conn);

        /* ================================
           SIMPAN FAKTOR RISIKO
        ================================= */
        foreach ($result['faktor'] as $param => $kontribusi) {

          // cari nilai dari data_input
          $nilai = 0;
          foreach ($data_input as $item) {
            if ($item['nama'] == $param) {
              $nilai = $item['nilai'];
              break;
            }
          }

          $stmt_fr = mysqli_prepare(
            $conn,
            "INSERT INTO faktor_risiko
             (prediksi_id, parameter, nilai, kontribusi)
             VALUES (?, ?, ?, ?)"
          );

          mysqli_stmt_bind_param(
            $stmt_fr,
            "isdd",
            $prediksi_id,
            $param,
            $nilai,
            $kontribusi
          );

          mysqli_stmt_execute($stmt_fr);
        }

        /* ================================
           TAMPILKAN HASIL
        ================================= */
        $warna = $hasil_db == 'Berisiko Stunting' ? 'danger' : 'success';
        $label = $hasil_db == 'Berisiko Stunting'
          ? 'BERISIKO STUNTING'
          : 'TIDAK BERISIKO';

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
    }
  }
}

/* ======================================================
   AMBIL PARAMETER UNTUK FORM
====================================================== */
$parameters = mysqli_query(
  $conn,
  "SELECT * FROM parameter
   WHERE status_aktif = 1
   ORDER BY id ASC"
);
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

                      <?php while ($param = mysqli_fetch_assoc($parameters)): ?>
                        <div class="mb-3">
                          <label class="form-label">
                            <?= ucwords(str_replace('_', ' ', $param['nama_parameter'])) ?>
                          </label>
                          <input
                            type="number"
                            step="0.01"
                            name="<?= $param['nama_parameter'] ?>"
                            class="form-control"
                            required>
                        </div>
                      <?php endwhile; ?>

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
