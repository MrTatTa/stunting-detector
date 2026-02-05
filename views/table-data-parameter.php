<?php
require_once "../config.php";

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
                    <button type="submit" class="btn btn-success">Train Model</button>
                  </form>
                </div>
              </div>

              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Parameter</th>
                    <th>Tipe Data</th>
                    <th>Status Aktif</th>
                    <th>Aksi</th>
                  </tr>
                </thead>

                <tbody class="table-border-bottom-0">
                  <?php
                  $no = 1;
                  $query = "SELECT * FROM parameter ORDER BY id ASC";
                  $result = mysqli_query($conn, $query);

                  if (mysqli_num_rows($result) > 0):
                    while ($row = mysqli_fetch_assoc($result)):
                  ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_parameter'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= $row['tipe_data'] ?></td>
                        <td>
                          <?php if ($row['status_aktif']): ?>
                            <span class="badge bg-label-success">Aktif</span>
                          <?php else: ?>
                            <span class="badge bg-label-secondary">Nonaktif</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="toggle_parameter.php?id=<?= $row['id'] ?>">
                                <i class="icon-base bx bx-refresh me-1"></i>
                                <?= $row['status_aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                              </a>
                              <a class="dropdown-item text-danger" href="hapus_parameter.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus parameter?')">
                                <i class="icon-base bx bx-trash me-1"></i> Hapus
                              </a>
                            </div>
                          </div>
                        </td>
                      </tr>
                    <?php
                    endwhile;
                  else:
                    ?>
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
</body>

</html>