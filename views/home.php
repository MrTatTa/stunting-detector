<?php
require_once "../config.php";

/* ================== STATISTIK ================== */
$totalData = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT COUNT(*) total FROM ibu_hamil")
)['total'];

$beresiko = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT COUNT(*) total FROM prediksi WHERE hasil='Berisiko Stunting'")
)['total'];

$normal = mysqli_fetch_assoc(
  mysqli_query($conn, "SELECT COUNT(*) total FROM prediksi WHERE hasil='Tidak Berisiko'")
)['total'];

$persen = $totalData ? round(($beresiko / $totalData) * 100, 1) : 0;

/* ================== GRAFIK ================== */
$chart = mysqli_query($conn, "
    SELECT DATE(created_at) tgl, COUNT(*) jumlah
    FROM ibu_hamil
    GROUP BY DATE(created_at)
    ORDER BY tgl
");

$dates = [];
$counts = [];
while ($c = mysqli_fetch_assoc($chart)) {
  $dates[] = date('d M', strtotime($c['tgl']));
  $counts[] = $c['jumlah'];
}

/* ================== TERBARU ================== */
$recent = mysqli_query($conn, "
    SELECT ibu_hamil.nama_ibu, prediksi.hasil, ibu_hamil.created_at
    FROM ibu_hamil
    LEFT JOIN prediksi ON prediksi.ibu_id = ibu_hamil.id
    ORDER BY ibu_hamil.created_at DESC
    LIMIT 5
");
?>

<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Dashboard Statistik Stunting</title>

  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico">

  <!-- Core UI -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css">
  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css">
  <link rel="stylesheet" href="../assets/css/demo.css">
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">

  <!-- Sneat Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>

  <!-- Charts (always last) -->
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css">
</head>


<body>

  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <?php include 'partials/sidebar.php'; ?>

      <div class="layout-page">
        <?php include 'partials/navbar.php'; ?>

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">

            <!-- ===== STAT CARDS ===== -->
            <div class="row g-4">
              <div class="col-md-3">
                <div class="card text-center">
                  <div class="card-body">
                    <h6>Total Data</h6>
                    <h2><?= $totalData ?></h2>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="card text-center">
                  <div class="card-body">
                    <h6>Beresiko</h6>
                    <h2 class="text-danger"><?= $beresiko ?></h2>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="card text-center">
                  <div class="card-body">
                    <h6>Tidak Beresiko</h6>
                    <h2 class="text-success"><?= $normal ?></h2>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="card text-center">
                  <div class="card-body">
                    <h6>Persentase</h6>
                    <h2><?= $persen ?>%</h2>
                  </div>
                </div>
              </div>
            </div>

            <!-- ===== CHART ===== -->
            <div class="row mt-4">
              <div class="col-md-8">
                <div class="card">
                  <div class="card-body">
                    <h6>Perkembangan Data</h6>
                    <div id="lineChart"></div>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="card">
                  <div class="card-body">
                    <h6>Perbandingan</h6>
                    <div id="pieChart"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- ===== RECENT ===== -->
            <div class="row mt-4">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">

                    <h6>Prediksi Terbaru</h6>

                    <table class="table">
                      <thead>
                        <tr>
                          <th>Nama</th>
                          <th>Hasil</th>
                          <th>Tanggal</th>
                        </tr>
                      </thead>
                      <tbody>

                        <?php while ($r = mysqli_fetch_assoc($recent)): ?>
                          <tr>
                            <td><?= $r['nama_ibu'] ?></td>
                            <td><?= $r['hasil'] ?></td>
                            <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
                          </tr>
                        <?php endwhile; ?>

                      </tbody>
                    </table>

                  </div>
                </div>
              </div>
            </div>

          </div>
          <?php include 'partials/footer.php'; ?>
        </div>
      </div>
    </div>

    <div class="layout-overlay layout-menu-toggle"></div>
  </div>

  <!-- ===== JS ===== -->
  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>
  <script src="../assets/js/main.js"></script>

  <!-- ApexCharts -->
  <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>


  <script>
    new ApexCharts(document.querySelector("#lineChart"), {
      chart: {
        type: 'line',
        height: 300
      },
      series: [{
        name: 'Data',
        data: <?= json_encode($counts) ?>
      }],
      xaxis: {
        categories: <?= json_encode($dates) ?>
      }
    }).render();

    new ApexCharts(document.querySelector("#pieChart"), {
      chart: {
        type: 'donut',
        height: 300
      },
      series: [<?= $beresiko ?>, <?= $normal ?>],
      labels: ['Beresiko', 'Tidak Beresiko']
    }).render();
  </script>

</body>

</html>