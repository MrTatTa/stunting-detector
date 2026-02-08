<?php
require_once "../config.php";

$id = (int) ($_GET['id'] ?? 0);

$query = "
SELECT ibu_hamil.*, prediksi.hasil, prediksi.probabilitas
FROM ibu_hamil
LEFT JOIN prediksi ON prediksi.ibu_id = ibu_hamil.id
WHERE ibu_hamil.id = $id
LIMIT 1
";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) die("Data tidak ditemukan");
?>

<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detail Pemeriksaan Ibu</title>

  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico">
  <link rel="stylesheet" href="../assets/vendor/css/core.css">
  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css">
  <link rel="stylesheet" href="../assets/css/demo.css">
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">

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
          <div class="container-xxl container-p-y">

            <div class="card shadow-sm">
              <div class="card-body">

                <h4 class="mb-4">Detail Pemeriksaan Ibu</h4>

                <!-- METRIC BOX -->
                <div class="row g-3 mb-4">

                  <div class="col-md-4">
                    <div class="card h-100 text-center shadow-sm">
                      <div class="card-body py-4">
                        <small class="text-muted">Tinggi Badan</small>
                        <h3 class="fw-bold mb-0"><?= $data['tinggi_badan'] ?> cm</h3>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="card h-100 text-center shadow-sm">
                      <div class="card-body py-4">
                        <small class="text-muted">LILA</small>
                        <h3 class="fw-bold mb-0"><?= $data['lingkar_lengan_atas'] ?> cm</h3>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="card h-100 text-center shadow-sm">
                      <div class="card-body py-4">
                        <small class="text-muted">Hemoglobin</small>
                        <h3 class="fw-bold mb-0"><?= $data['kadar_hb'] ?> g/dL</h3>
                      </div>
                    </div>
                  </div>

                </div>

                <?php
                $tb   = $data['tinggi_badan'];
                $lila = $data['lingkar_lengan_atas'];
                $hb   = $data['kadar_hb'];
                $prob = $data['probabilitas'] * 100;

                $saran = [];

                /* Risiko dari AI */
                if ($prob >= 80) $tingkat = "Tinggi";
                elseif ($prob >= 50) $tingkat = "Sedang";
                else $tingkat = "Rendah";

                /* Analisis fisik */
                if ($hb < 10) $saran[] = "Hb sangat rendah, tingkatkan zat besi dan konsultasi tenaga kesehatan.";
                elseif ($hb < 11) $saran[] = "Hb sedikit rendah, konsumsi bayam, daging merah, telur, vitamin C.";

                if ($lila < 22) $saran[] = "LILA sangat kecil, indikasi kekurangan gizi serius.";
                elseif ($lila < 23.5) $saran[] = "LILA di bawah normal, perbanyak protein dan susu ibu hamil.";

                if ($tb < 145) $saran[] = "Postur ibu sangat pendek, nutrisi harus dijaga ekstra.";
                elseif ($tb < 150) $saran[] = "Tinggi ibu relatif pendek, jaga pola makan bergizi.";

                if ($hb < 10 || $lila < 22 || $tb < 145) $tingkat = "Tinggi";

                if (empty($saran)) {
                  $saran[] = "Status gizi ibu sangat baik, pertahankan pola makan sehat.";
                }

                $motivasi_baik = [
                  "🌿 Kondisi ibu sangat baik untuk tumbuh kembang bayi.",
                  "💚 Pertahankan kebiasaan sehat ini.",
                  "✨ Kehamilan berjalan optimal."
                ];

                $motivasi_resiko = [
                  "💪 Perubahan kecil hari ini berdampak besar untuk anak.",
                  "🌈 Tetap semangat memperbaiki nutrisi.",
                  "🍼 Ibu hebat selalu berusaha terbaik."
                ];

                $motivasi = $tingkat == "Rendah"
                  ? $motivasi_baik[array_rand($motivasi_baik)]
                  : $motivasi_resiko[array_rand($motivasi_resiko)];

                $warna = $tingkat == "Tinggi" ? "danger" : ($tingkat == "Sedang" ? "warning" : "success");
                ?>

                <!-- HASIL -->
                <div class="mt-3">

                  <h5 class="mb-2">Hasil Prediksi</h5>

                  <div class="d-flex align-items-center gap-3 mb-2 flex-wrap">

                    <?php if ($data['hasil'] == 'Berisiko Stunting'): ?>
                      <span class="badge bg-label-danger px-3 py-2 fs-6">Berisiko Stunting</span>
                    <?php else: ?>
                      <span class="badge bg-label-success px-3 py-2 fs-6">Tidak Berisiko</span>
                    <?php endif; ?>

                    <span class="text-muted">
                      Probabilitas: <b><?= round($prob, 2) ?>%</b>
                    </span>

                  </div>

                  <div class="alert alert-<?= $warna ?> mb-0">

                    <h6 class="mb-2">📊 Tingkat Risiko: <b><?= $tingkat ?></b></h6>

                    <ul class="ps-3 mb-2">
                      <?php foreach ($saran as $item): ?>
                        <li><?= $item ?></li>
                      <?php endforeach; ?>
                    </ul>

                    <div class="fw-semibold"><?= $motivasi ?></div>

                  </div>

                </div>

              </div>
            </div>

          </div>
          <?php include 'partials/footer.php'; ?>
        </div>
      </div>
    </div>

    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/js/main.js"></script>

</body>

</html>