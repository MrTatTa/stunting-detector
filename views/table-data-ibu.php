<?php
require_once "../config.php";

/* ================================
   PROSES HAPUS
================================ */
if (isset($_POST['hapus_id'])) {

  $id = (int) $_POST['hapus_id'];

  if ($id > 0) {

    /* Ambil semua prediksi */
    $getPred = mysqli_query($conn, "
      SELECT id FROM prediksi
      WHERE ibu_id = $id AND deleted_at IS NULL
    ");

    /* Soft delete faktor_risiko */
    while ($row = mysqli_fetch_assoc($getPred)) {

      mysqli_query(
        $conn,
        "
        UPDATE faktor_risiko
        SET deleted_at = NOW()
        WHERE prediksi_id = " . (int)$row['id']
      );
    }

    /* Soft delete prediksi */
    mysqli_query($conn, "
      UPDATE prediksi
      SET deleted_at = NOW()
      WHERE ibu_id = $id
    ");

    /* Soft delete ibu */
    mysqli_query($conn, "
      UPDATE ibu_hamil
      SET deleted_at = NOW()
      WHERE id = $id
    ");

    /* set toast */
    $toast = "Data berhasil dihapus";
    $toast_type = "success";
  }
}
?>
<!doctype html>

<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>DATA | STIKES Semarang - Table Data Ibu</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

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
  <!-- Toast container -->
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">

    <?php if (!empty($toast)): ?>

      <div
        id="userToast"
        class="toast border-0 shadow"
        role="alert"
        aria-live="assertive"
        aria-atomic="true">

        <div class="alert alert-<?= $toast_type == 'success' ? 'success' : 'danger' ?>
                  alert-dismissible mb-0 d-flex align-items-center">

          <span id="userToastBody">
            <?= htmlspecialchars($toast) ?>
          </span>

          <button
            type="button"
            class="btn-close ms-auto"
            data-bs-dismiss="toast"
            aria-label="Close">
          </button>

        </div>

      </div>

    <?php endif; ?>

  </div>
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
              <div class="table-responsive text-nowrap">
                <div class="card mb-4">

                  <div class="card-body">
                    <div class="row align-items-center">

                      <!-- Judul (kiri) -->
                      <div class="col-md-3">
                        <h5 class="mb-0">Data Prediksi</h5>
                      </div>

                      <!-- Container kanan -->
                      <div class="col-md-9">
                        <div class="row g-3 justify-content-end">

                          <!-- Search -->
                          <div class="col-md-5">
                            <div class="input-group">
                              <span class="input-group-text bg-transparent">
                                <i class="bx bx-search"></i>
                              </span>
                              <input type="text" id="searchInput" class="form-control"
                                placeholder="Cari nama ibu...">
                            </div>
                          </div>

                          <!-- Filter -->
                          <div class="col-md-4">
                            <select id="filterSelect" class="form-select">
                              <option value="">Semua Status</option>
                              <option value="Berisiko Stunting">Berisiko Stunting</option>
                              <option value="Tidak Berisiko">Tidak Berisiko</option>
                            </select>
                          </div>

                        </div>
                      </div>

                    </div>
                  </div>

                </div>

                <?php
                $no = 1;

                $query = "
SELECT
  ih.*,
  pr.hasil,

  -- Ambil nilai usia dari nilai_parameter
  (
    SELECT np.nilai
    FROM nilai_parameter np
    JOIN parameter p ON p.id = np.parameter_id
    WHERE np.ibu_id = ih.id
    AND p.nama_parameter = 'usia'
    LIMIT 1
  ) AS usia

FROM ibu_hamil ih
LEFT JOIN prediksi pr
  ON pr.ibu_id = ih.id
  AND pr.deleted_at IS NULL

WHERE ih.deleted_at IS NULL
ORDER BY ih.created_at DESC
";

                $result = mysqli_query($conn, $query);
                ?>
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama Ibu</th>
                      <th>Usia</th>
                      <th>Hasil Prediksi</th>
                      <th>Tanggal Periksa</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>

                  <tbody class="table-border-bottom-0">

                    <?php if (mysqli_num_rows($result) > 0): ?>
                      <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                          <td><?= $no++ ?></td>

                          <td><?= htmlspecialchars($row['nama_ibu']) ?></td>

                          <td>
                            <?= $row['usia'] !== null
                              ? (int)$row['usia'] . " th"
                              : "-" ?>
                          </td>

                          <td>
                            <?php if ($row['hasil'] == 'Berisiko Stunting'): ?>
                              <span class="badge bg-label-danger">Berisiko Stunting</span>
                            <?php elseif ($row['hasil'] == 'Tidak Berisiko'): ?>
                              <span class="badge bg-label-success">Tidak Berisiko</span>
                            <?php else: ?>
                              <span class="badge bg-label-secondary">Belum Diprediksi</span>
                            <?php endif; ?>
                          </td>

                          <td><?= htmlspecialchars($row['created_at']) ?></td>

                          <td>
                            <div class="dropdown">
                              <button type="button"
                                class="btn p-0 dropdown-toggle hide-arrow"
                                data-bs-toggle="dropdown">
                                <i class="icon-base bx bx-dots-vertical-rounded"></i>
                              </button>

                              <div class="dropdown-menu">

                                <a class="dropdown-item"
                                  href="javascript:void(0);"
                                  onclick="showDetail(<?= (int)$row['id'] ?>)">
                                  <i class="icon-base bx bx-show me-1"></i> Detail
                                </a>

                                <a class="dropdown-item text-danger"
                                  href="javascript:void(0);"
                                  onclick="confirmDelete(<?= (int)$row['id'] ?>)">
                                  <i class="icon-base bx bx-trash me-1"></i> Hapus
                                </a>

                              </div>
                            </div>
                          </td>
                        </tr>
                      <?php endwhile; ?>

                    <?php else: ?>
                      <tr>
                        <td colspan="5" class="text-center text-muted py-4">
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

  <div id="modalContainer"></div>
  <div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <form method="POST">

          <input type="hidden" name="hapus_id" id="hapus_id">

          <div class="modal-header">
            <h5 class="modal-title">Konfirmasi Hapus</h5>
          </div>

          <div class="modal-body">
            Yakin ingin menghapus data ini?
          </div>

          <div class="modal-footer">

            <button type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal">
              Batal
            </button>

            <button type="submit"
              class="btn btn-danger">
              Hapus
            </button>

          </div>

        </form>

      </div>
    </div>
  </div>

  <script>
    (function() {
      const text = "DATA | STIKES Semarang - Table Data Ibu  • ";
      let i = 0;

      setInterval(() => {
        document.title = text.slice(i) + text.slice(0, i);
        i = (i + 1) % text.length;
      }, 120);
    })();
  </script>
  <script>
    function showDetail(id) {

      // Hapus modal lama jika ada
      document.getElementById('modalContainer').innerHTML = "";

      fetch('detail.php?id=' + id)
        .then(response => response.text())
        .then(html => {

          document.getElementById('modalContainer').innerHTML = html;

          // Tunggu sebentar agar DOM siap
          setTimeout(() => {
            const modalEl = document.getElementById('detailModal');
            if (modalEl) {
              const modal = new bootstrap.Modal(modalEl);
              modal.show();
            }
          }, 100);

        })
        .catch(error => {
          alert('Gagal memuat detail');
          console.error(error);
        });
    }
  </script>
  <script>
    function confirmDelete(id) {

      document.getElementById("hapus_id").value = id;

      new bootstrap.Modal(
        document.getElementById("deleteModal")
      ).show();

    }
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {

      const toastEl = document.getElementById("userToast");

      if (toastEl) {

        const toast = new bootstrap.Toast(toastEl, {
          delay: 3000
        });

        toast.show();

      }

    });
  </script>
  <!-- filter pencarian -->
  <script>
    const searchInput = document.getElementById("searchInput");
    const filterSelect = document.getElementById("filterSelect");

    function filterTable() {

      const search = searchInput.value.toLowerCase().trim();
      const filter = filterSelect.value.toLowerCase().trim();

      document.querySelectorAll("tbody tr").forEach(row => {

        // kolom ke-2 = Nama Ibu
        const nama = row.querySelector("td:nth-child(2)")
          ?.textContent.toLowerCase().trim() || "";

        // kolom ke-4 = Hasil Prediksi
        const status = row.querySelector("td:nth-child(4)")
          ?.textContent.toLowerCase().trim() || "";

        const cocokNama = nama.includes(search);

        const cocokStatus =
          filter === "" ||
          status.includes(filter);

        row.style.display = (cocokNama && cocokStatus) ? "" : "none";

      });

    }

    searchInput.addEventListener("input", filterTable);
    filterSelect.addEventListener("change", filterTable);
  </script>

</body>

</html>
