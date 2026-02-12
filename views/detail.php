<?php
require_once "../config.php";

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
  echo "<div class='p-4 text-center text-danger'>ID tidak valid</div>";
  exit;
}

/* =========================
   AMBIL DATA IBU + PREDIKSI
========================= */
$query = "
SELECT ih.nama_ibu, pr.hasil, pr.probabilitas
FROM ibu_hamil ih
LEFT JOIN prediksi pr
  ON pr.ibu_id = ih.id
  AND pr.deleted_at IS NULL
WHERE ih.id = $id
AND ih.deleted_at IS NULL
LIMIT 1
";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
  echo "<div class='p-4 text-center text-danger'>Data tidak ditemukan</div>";
  exit;
}

/* =========================
   AMBIL SEMUA PARAMETER
========================= */
$query_param = "
SELECT p.nama_parameter, np.nilai
FROM nilai_parameter np
JOIN parameter p ON p.id = np.parameter_id
WHERE np.ibu_id = $id
ORDER BY p.id ASC
";

$result_param = mysqli_query($conn, $query_param);

$parameters = [];
while ($row = mysqli_fetch_assoc($result_param)) {
  $parameters[$row['nama_parameter']] = $row['nilai'];
}

/* =========================
   ANALISIS RISIKO
========================= */
$prob = isset($data['probabilitas']) ? $data['probabilitas'] * 100 : 0;

if ($prob >= 80) $tingkat = "Tinggi";
elseif ($prob >= 50) $tingkat = "Sedang";
else $tingkat = "Rendah";

$parameter_dominan = "Kondisi Umum";
$edukasi = [];

$usia = $parameters['usia'] ?? null;
$tb   = $parameters['tinggi_badan'] ?? null;
$lila = $parameters['lingkar_lengan_atas'] ?? null;
$hb   = $parameters['kadar_hb'] ?? null;

/* Rule edukasi lama tetap bisa dipakai */
if ($tingkat != "Rendah") {

  if ($hb !== null && $hb < 11) {
    $parameter_dominan = "Kadar Hemoglobin (HB)";
  } elseif ($lila !== null && $lila < 23.5) {
    $parameter_dominan = "Lingkar Lengan Atas (LILA)";
  } elseif ($tb !== null && $tb < 150) {
    $parameter_dominan = "Tinggi Badan Ibu";
  } elseif ($usia !== null && ($usia < 20 || $usia > 35)) {
    $parameter_dominan = "Usia Ibu";
  }

  $edukasi = [
    "Kesadaran diri ibu hamil",
    "Rutin meminum MMS dan Tablet Tambah Darah",
    "Pola makan bergizi seimbang",
    "ANC minimal 6–8x selama kehamilan"
  ];
} else {

  $parameter_dominan = "Kondisi Umum Baik";
  $edukasi = [
    "Pertahankan pola hidup sehat",
    "Konsumsi gizi seimbang",
    "ANC rutin",
    "Pantau pertumbuhan janin"
  ];
}

$warna = $tingkat == "Tinggi" ? "danger" : ($tingkat == "Sedang" ? "warning" : "success");
?>

<!-- =========================
     MODAL DETAIL
========================= -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow">

      <div class="modal-header">
        <h5 class="modal-title">
          Detail Pemeriksaan - <?= htmlspecialchars($data['nama_ibu']) ?>
        </h5>
      </div>

      <div class="modal-body">

        <!-- =========================
             SEMUA PARAMETER DINAMIS
        ========================= -->
        <div class="row g-3 mb-4">

          <?php foreach ($parameters as $nama => $nilai): ?>
            <div class="col-md-4">
              <div class="card text-center border-0 bg-light">
                <div class="card-body py-3">
                  <small class="text-muted">
                    <?= ucwords(str_replace('_', ' ', $nama)) ?>
                  </small>
                  <h4 class="fw-bold mb-0">
                    <?= htmlspecialchars($nilai) ?>
                  </h4>
                </div>
              </div>
            </div>
          <?php endforeach; ?>

        </div>

        <!-- =========================
             HASIL PREDIKSI
        ========================= -->
        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">

          <?php if ($data['hasil'] == 'Berisiko Stunting'): ?>
            <span class="badge bg-label-danger px-3 py-2">Berisiko Stunting</span>
          <?php elseif ($data['hasil'] == 'Tidak Berisiko'): ?>
            <span class="badge bg-label-success px-3 py-2">Tidak Berisiko</span>
          <?php else: ?>
            <span class="badge bg-label-secondary px-3 py-2">Belum Diprediksi</span>
          <?php endif; ?>

          <span class="text-muted">
            Probabilitas: <strong><?= round($prob, 2) ?>%</strong>
          </span>
        </div>

        <div class="progress mb-3" style="height: 8px;">
          <div class="progress-bar bg-<?= $warna ?>"
            style="width: <?= round($prob, 2) ?>%">
          </div>
        </div>

        <!-- =========================
             ANALISIS & EDUKASI
        ========================= -->
        <div class="alert alert-<?= $warna ?> mb-0">

          <strong>Tingkat Risiko: <?= $tingkat ?></strong><br>
          <small class="text-muted">
            Parameter Dominan: <b><?= $parameter_dominan ?></b>
          </small>

          <hr>

          <strong>Edukasi:</strong>
          <ul class="ps-3 mt-2 mb-0">
            <?php foreach ($edukasi as $item): ?>
              <li><?= $item ?></li>
            <?php endforeach; ?>
          </ul>

        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>

    </div>
  </div>
</div>

<script>
  var modal = new bootstrap.Modal(document.getElementById('detailModal'));
  modal.show();
</script>