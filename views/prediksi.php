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
              <div class="col-md-6">
                <div class="card">
                  <h5 class="card-header">Prediksi Potensi Stunting</h5>
                  <div class="card-body demo-vertical-spacing demo-only-element">
                    <div class="input-group">
                      <span class="input-group-text" id="basic-addon11">Nama Ibu</span>
                      <input
                        type="text"
                        class="form-control"
                        placeholder="Nama Lengkap"
                        aria-label="Nama Lengkap"
                        aria-describedby="basic-addon11" />
                    </div>

                    <div class="input-group">
                      <span class="input-group-text" id="basic-addon11">Usia Ibu</span>
                      <input
                        type="text"
                        class="form-control"
                        placeholder="Usia Ibu"
                        aria-label="Usia Ibu"
                        aria-describedby="basic-addon11" />
                    </div>

                    <div class="form-password-toggle">
                      <label class="form-label" for="basic-default-password12">Tinggi Badan</label>
                      <div class="input-group">
                        <input
                          type="text"
                          class="form-control"
                          id="basic-default-password12"
                          placeholder="Masukkan Tinggi Badan Ibu"
                          aria-describedby="basic-default-password2" />
                        <span class="input-group-text" id="basic-addon13">cm</span>
                      </div>
                    </div>

                    <div class="form-password-toggle">
                      <label class="form-label" for="basic-default-password12">LILA</label>
                      <div class="input-group">
                        <input
                          type="text"
                          class="form-control"
                          id="basic-default-password12"
                          placeholder="Masukkan Lingkar Lengan Atas"
                          aria-describedby="basic-default-password2" />
                        <span class="input-group-text" id="basic-addon13">cm</span>
                      </div>
                    </div>

                    <div class="form-password-toggle">
                      <label class="form-label" for="basic-default-password12">Kadar Hemoglobin</label>
                      <div class="input-group">
                        <input
                          type="text"
                          class="form-control"
                          id="basic-default-password12"
                          placeholder="Masukkan Kadar Hemoglobin"
                          aria-describedby="basic-default-password2" />
                        <span class="input-group-text" id="basic-addon13">g/Dl</span>
                      </div>
                    </div>

                    <div class="demo-inline-spacing text-end">
                      <button type="submit" class="btn btn-primary">Prediksi</button>
                      <button type="button" class="btn btn-secondary">Batal</button>
                    </div>

                  </div>
                </div>
              </div>
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