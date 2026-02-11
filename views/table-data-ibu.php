<?php
require_once "../config.php";

?>
<!doctype html>

<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Prediksi Stunting</title>

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
  <!-- build:css assets/vendor/css/theme.css  -->

  <link rel="stylesheet" href="../assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />

  <!-- Vendors CSS -->

  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <!-- endbuild -->

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
            <!-- Hoverable Table rows -->
            <div class="card">
              <h5 class="card-header">Data Prediksi</h5>
              <div class="table-responsive text-nowrap">
                <div class="card mb-4">
                  <div class="card-body">
                    <div class="row g-3 align-items-center">

                      <div class="col-md-6">
                        <div class="input-group">
                          <span class="input-group-text bg-transparent">
                            <i class="bx bx-search"></i>
                          </span>
                          <input type="text" id="searchInput" class="form-control"
                            placeholder="Cari nama ibu...">
                        </div>
                      </div>

                      <div class="col-md-4">
                        <select id="filterSelect" class="form-select">
                          <option value="">Semua Status</option>
                          <option value="Berisiko">Berisiko</option>
                          <option value="Tidak">Tidak Berisiko</option>
                        </select>
                      </div>

                      <div class="col-md-2 text-md-end">
                        <span class="badge bg-label-primary w-100 py-2">
                          Filter Data
                        </span>
                      </div>

                    </div>
                  </div>
                </div>


                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama Ibu</th>
                      <th>Usia</th>
                      <th>Hasil Prediksi</th>
                      <th>Aksi</th>
                    </tr>

                  </thead>

                  <tbody class="table-border-bottom-0">
                    <?php
                    $no = 1;
                    $query = "
      SELECT ibu_hamil.*, prediksi.hasil
      FROM ibu_hamil
      LEFT JOIN prediksi ON prediksi.ibu_id = ibu_hamil.id
      ORDER BY ibu_hamil.created_at DESC
    ";
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0) :
                      while ($row = mysqli_fetch_assoc($result)) :
                    ?>
                        <tr>
                          <td><?= $no++ ?></td>
                          <td><?= htmlspecialchars($row['nama_ibu']) ?></td>
                          <td><?= (int)$row['usia'] ?> th</td>

                          <td>
                            <?php if ($row['hasil'] == 'Berisiko Stunting'): ?>
                              <span class="badge bg-label-danger">Berisiko</span>
                            <?php elseif ($row['hasil'] == 'Tidak Berisiko'): ?>
                              <span class="badge bg-label-success">Tidak Berisiko</span>
                            <?php else: ?>
                              <span class="badge bg-label-secondary">Belum Diprediksi</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <div class="dropdown">
                              <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                data-bs-toggle="dropdown">
                                <i class="icon-base bx bx-dots-vertical-rounded"></i>
                              </button>
                              <div class="dropdown-menu">
                                <a class="dropdown-item"
                                  href="detail.php?id=<?= (int)$row['id'] ?>">
                                  <i class="icon-base bx bx-show me-1"></i> Detail
                                </a>

                                <a class="dropdown-item text-danger"
                                  href="hapus.php?id=<?= (int)$row['id'] ?>"
                                  onclick="return confirm('Yakin hapus data?')">
                                  <i class="icon-base bx bx-trash me-1"></i> Hapus
                                </a>

                              </div>
                            </div>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    <?php else : ?>
                      <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                          Tidak ada data
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <!--/ Hoverable Table rows -->
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

  <!-- Main JS -->

  <script src="../assets/js/main.js"></script>

  <!-- Page JS -->

  <!-- Place this tag before closing body tag for github widget button. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>

  <!-- Smooth Toast Popup -->
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body" id="toastMessage"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const msg = sessionStorage.getItem("toast_success");

      if (msg) {
        document.getElementById("toastMessage").innerText = msg;
        const toast = new bootstrap.Toast(document.getElementById("successToast"), {
          delay: 3000
        });
        toast.show();
        sessionStorage.removeItem("toast_success");
      }
    });
  </script>
  <!-- filter pencarian -->
  <script>
    const searchInput = document.getElementById("searchInput");
    const filterSelect = document.getElementById("filterSelect");

    function filterTable() {
      const search = searchInput.value.toLowerCase();
      const filter = filterSelect.value.toLowerCase();

      document.querySelectorAll("tbody tr").forEach(row => {

        // kolom nama ibu
        const nama = row.querySelector("td:nth-child(2)")?.innerText.toLowerCase() || "";

        // kolom hasil (badge)
        const status = row.querySelector("td:nth-child(7)")?.innerText.toLowerCase() || "";

        const cocokNama = nama.includes(search);
        const cocokStatus = filter === "" || status.includes(filter);

        row.style.display = cocokNama && cocokStatus ? "" : "none";
      });
    }

    searchInput.addEventListener("input", filterTable);
    filterSelect.addEventListener("change", filterTable);
  </script>

</body>

</html>