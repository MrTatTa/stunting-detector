<?php
require_once "../config.php";

/* ===============================
   PROSES TAMBAH PARAMETER
================================ */
if (isset($_POST['tambah_parameter'])) {

  $nama = trim($_POST['nama_parameter']);
  $tipe = trim($_POST['tipe_data']);

  if ($nama !== "" && $tipe !== "") {

    $stmt = mysqli_prepare(
      $conn,
      "INSERT INTO parameter (nama_parameter, tipe_data, status_aktif)
       VALUES (?, ?, 1)"
    );

    mysqli_stmt_bind_param($stmt, "ss", $nama, $tipe);
    mysqli_stmt_execute($stmt);
  }

  header("Location: table-data-parameter.php");
  exit;
}

/* ===============================
   TOGGLE STATUS
================================ */
if (isset($_POST['toggle_parameter'])) {

  $id = (int)$_POST['id'];

  mysqli_query($conn, "
    UPDATE parameter
    SET status_aktif = IF(status_aktif = 1, 0, 1)
    WHERE id = $id
  ");

  header("Location: table-data-parameter.php");
  exit;
}

/* ===============================
   HAPUS PARAMETER
================================ */
if (isset($_POST['hapus_parameter'])) {

  $id = (int)$_POST['id'];

  mysqli_query($conn, "DELETE FROM parameter WHERE id = $id");

  header("Location: table-data-parameter.php");
  exit;
}
?>

<!doctype html>

<html
  lang="en"
  class="layout-menu-fixed layout-compact"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Demo: Tables - Basic Tables | Sneat - Bootstrap Dashboard FREE</title>

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
  <!-- LOADER OVERLAY -->
  <div id="loaderOverlay" style="
  display:none;
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(255,255,255,0.8);
  z-index:9999;
  justify-content:center;
  align-items:center;
  flex-direction:column;
">

    <div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status"></div>
    <p class="mt-3 fw-semibold">Sedang melakukan training model...</p>

  </div>

  <!-- Toast container -->
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div
      id="trainToast"
      class="toast border-0 shadow"
      role="alert"
      aria-live="assertive"
      aria-atomic="true">
      <div class="alert alert-success alert-dismissible mb-0 d-flex align-items-center">
        <span id="trainToastBody">
          Training model berhasil
        </span>
        <button
          type="button"
          class="btn-close ms-auto"
          data-bs-dismiss="toast"
          aria-label="Close"></button>
      </div>
    </div>
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
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Parameter</h5>
                <div>
                  <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalTambahParameter">
                    Tambah Parameter
                  </button>
                  <form action="train_model.php" method="post" style="display:inline;">
                    <button id="trainBtn" class="btn btn-success">Train Model</button>
                  </form>
                </div>
              </div>

              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Parameter</th>
                    <th>Tipe Data</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>

                <tbody>
                  <?php
                  $no = 1;
                  $result = mysqli_query($conn, "SELECT * FROM parameter ORDER BY id ASC");

                  if (mysqli_num_rows($result) > 0):
                    while ($row = mysqli_fetch_assoc($result)):
                  ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_parameter']) ?></td>
                        <td><?= htmlspecialchars($row['tipe_data']) ?></td>

                        <td>
                          <?php if ($row['status_aktif']): ?>
                            <span class="badge bg-success">Aktif</span>
                          <?php else: ?>
                            <span class="badge bg-secondary">Nonaktif</span>
                          <?php endif; ?>
                        </td>

                        <td>
                          <div class="dropdown">
                            <button type="button"
                              class="btn p-0 dropdown-toggle hide-arrow"
                              data-bs-toggle="dropdown">
                              <i class="bx bx-dots-vertical-rounded"></i>
                            </button>

                            <div class="dropdown-menu">

                              <!-- TOGGLE -->
                              <form method="POST">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit"
                                  name="toggle_parameter"
                                  class="dropdown-item">
                                  <i class="bx bx-refresh me-1"></i>
                                  <?= $row['status_aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                </button>
                              </form>

                              <!-- HAPUS -->
                              <form method="POST"
                                onsubmit="return confirm('Yakin hapus parameter?')">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit"
                                  name="hapus_parameter"
                                  class="dropdown-item text-danger">
                                  <i class="bx bx-trash me-1"></i> Hapus
                                </button>
                              </form>

                            </div>
                          </div>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="text-center text-muted py-4">
                        Tidak ada parameter
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
  <!-- MODAL TAMBAH -->
  <div class="modal fade" id="modalTambahParameter" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <form method="POST">

          <div class="modal-header">
            <h5 class="modal-title">Tambah Parameter</h5>
          </div>

          <div class="modal-body">

            <div class="mb-3">
              <label class="form-label">Nama Parameter</label>
              <input type="text"
                name="nama_parameter"
                class="form-control"
                required>
            </div>

            <div class="mb-3">
              <label class="form-label">Jenis Input</label>
              <select name="tipe_data"
                class="form-select"
                required>
                <option value="">Pilih jenis data</option>

                <option value="int">
                  Angka Bulat (contoh: 20, 35)
                </option>

                <option value="float">
                  Angka Desimal (contoh: 150.5, 11.2)
                </option>

                <option value="string">
                  Teks (contoh: Normal, Berisiko)
                </option>

                <option value="date">
                  Tanggal (contoh: 12-02-2026)
                </option>

              </select>
            </div>

          </div>

          <div class="modal-footer">
            <button type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal">
              Batal
            </button>

            <button type="submit"
              name="tambah_parameter"
              class="btn btn-primary">
              Simpan
            </button>
          </div>

        </form>

      </div>
    </div>
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
  <script>
    document.getElementById('trainBtn').addEventListener('click', function(e) {
      e.preventDefault();

      // tampilkan overlay loader
      const loader = document.getElementById('loaderOverlay');
      loader.style.display = 'flex';

      fetch('train_model.php', {
          method: 'POST'
        })
        .then(res => res.json())
        .then(data => {
          loader.style.display = 'none';

          const toastEl = document.getElementById('trainToast');
          const toastBody = document.getElementById('trainToastBody');
          const alertBox = toastEl.querySelector('.alert');

          // reset class alert
          alertBox.classList.remove('alert-success', 'alert-danger');

          if (data.success) {
            alertBox.classList.add('alert-success');
            toastBody.textContent = 'Training model berhasil';
          } else {
            alertBox.classList.add('alert-danger');
            toastBody.textContent = '' + (data.message ?? 'Training gagal');
          }

          const toast = new bootstrap.Toast(toastEl, {
            delay: 4000
          });
          toast.show();
        })
        .catch(() => {
          loader.style.display = 'none';

          const toastEl = document.getElementById('trainToast');
          const toastBody = document.getElementById('trainToastBody');
          const alertBox = toastEl.querySelector('.alert');

          alertBox.classList.remove('alert-success');
          alertBox.classList.add('alert-danger');
          toastBody.textContent = '❌ Terjadi kesalahan sistem';

          const toast = new bootstrap.Toast(toastEl, {
            delay: 4000
          });
          toast.show();
        });
    });
  </script>
  <script src="../assets/js/main.js"></script>

  <!-- Page JS -->

  <!-- Place this tag before closing body tag for github widget button. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
