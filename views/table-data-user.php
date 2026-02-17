<?php
require_once "../config.php";

$toast = "";
$toast_type = ""; // success / error

if (isset($_POST['tambah_user'])) {

  $nama     = trim($_POST['nama']);
  $username = trim($_POST['username']);
  $password = $_POST['password'];
  $role     = $_POST['role'];

  // validasi kosong
  if ($nama == "" || $username == "" || $password == "" || $role == "") {

    $toast = "Semua field wajib diisi";
    $toast_type = "error";
  } else {

    // cek username sudah ada atau belum
    $check = mysqli_prepare(
      $conn,
      "SELECT id FROM users WHERE username = ?"
    );

    mysqli_stmt_bind_param($check, "s", $username);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {

      $toast = "Username sudah digunakan";
      $toast_type = "error";
    } else {

      // hash password
      $password_hash = password_hash($password, PASSWORD_DEFAULT);

      // insert user
      $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO users (nama, username, password, role)
         VALUES (?, ?, ?, ?)"
      );

      mysqli_stmt_bind_param(
        $stmt,
        "ssss",
        $nama,
        $username,
        $password_hash,
        $role
      );

      if (mysqli_stmt_execute($stmt)) {

        $toast = "User berhasil ditambahkan";
        $toast_type = "success";
      } else {

        $toast = "Gagal menambahkan user";
        $toast_type = "error";
      }

      mysqli_stmt_close($stmt);
    }

    mysqli_stmt_close($check);
  }
}

/* ===============================
   PROSES HAPUS USER
================================ */
if (isset($_POST['hapus_id'])) {

  $id = intval($_POST['hapus_id']);

  if ($id <= 0) {

    $toast = "ID user tidak valid";
    $toast_type = "error";
  } else {

    // optional: cegah hapus user sendiri
    // if ($id == $_SESSION['user_id']) {
    //   $toast = "Tidak dapat menghapus akun sendiri";
    //   $toast_type = "error";
    // }

    $stmt = mysqli_prepare(
      $conn,
      "DELETE FROM users WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {

      if (mysqli_stmt_affected_rows($stmt) > 0) {

        $toast = "User berhasil dihapus";
        $toast_type = "success";
      } else {

        $toast = "User tidak ditemukan";
        $toast_type = "error";
      }
    } else {

      $toast = "Gagal menghapus user";
      $toast_type = "error";
    }

    mysqli_stmt_close($stmt);
  }
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

  <title>DATA | STIKES Semarang - Table Data User</title>

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
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data User</h5>
                <div>
                  <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                    Tambah User
                  </button>
                </div>
              </div>

              <?php
              $result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
              ?>
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                  </tr>
                </thead>

                <tbody>

                  <?php $no = 1;
                  while ($row = mysqli_fetch_assoc($result)): ?>

                    <tr>

                      <td><?= $no++ ?></td>

                      <td><?= htmlspecialchars($row['nama']) ?></td>

                      <td><?= htmlspecialchars($row['username']) ?></td>

                      <td>
                        <?php if ($row['role'] == 'admin'): ?>
                          <span class="badge bg-label-primary">Admin</span>
                        <?php else: ?>
                          <span class="badge bg-label-info">Petugas</span>
                        <?php endif; ?>
                      </td>

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
                              onclick="openPasswordModal(
    <?= (int)$row['id'] ?>,
    '<?= htmlspecialchars($row['username'], ENT_QUOTES) ?>'
  )">

                              <i class="icon-base bx bx-key me-1"></i>
                              Ganti Password

                            </a>

                            <button class="dropdown-item text-danger"
                              onclick="confirmDelete(<?= $row['id'] ?>)">
                              <i class="icon-base bx bx-trash me-1"></i>
                              Hapus
                            </button>

                          </div>
                        </div>
                      </td>

                    </tr>

                  <?php endwhile; ?>

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
  <!-- MODAL TAMBAH USER -->
  <div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <form method="POST">

          <!-- Header -->
          <div class="modal-header">
            <h5 class="modal-title">Tambah User</h5>
          </div>

          <!-- Body -->
          <div class="modal-body">

            <!-- Nama -->
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text"
                name="nama"
                class="form-control"
                placeholder="Masukkan nama lengkap"
                required>
            </div>

            <!-- Username -->
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text"
                name="username"
                class="form-control"
                placeholder="Masukkan username"
                required>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label class="form-label">Password</label>

              <div class="input-group">
                <input type="password"
                  name="password"
                  id="passwordTambah"
                  class="form-control"
                  placeholder="Masukkan password"
                  required>

                <button type="button"
                  class="btn btn-outline-secondary"
                  onclick="togglePasswordTambah()">
                  <i class="bx bx-show"></i>
                </button>
              </div>

            </div>

            <!-- Role -->
            <div class="mb-3">
              <label class="form-label">Role</label>

              <select name="role"
                class="form-select"
                required>

                <option value="">Pilih role</option>
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>

              </select>
            </div>

          </div>

          <!-- Footer -->
          <div class="modal-footer">

            <button type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal">
              Batal
            </button>

            <button type="submit"
              name="tambah_user"
              class="btn btn-primary">
              Simpan
            </button>

          </div>

        </form>

      </div>
    </div>
  </div>
  <script>
    function togglePasswordTambah() {

      const input = document.getElementById("passwordTambah");

      if (input.type === "password") {
        input.type = "text";
      } else {
        input.type = "password";
      }

    }
  </script>

  <!-- MODAL DELETE -->
  <div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title text-danger">Konfirmasi Hapus</h5>
        </div>

        <div class="modal-body">
          Apakah Anda yakin ingin menghapus data ini?
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <form method="POST">
            <input type="hidden" name="hapus_id" id="hapus_id">

            <button type="submit" class="btn btn-danger">
              Ya, Hapus
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>
  <script>
    function confirmDelete(id) {

      // set id ke input hidden
      document.getElementById("hapus_id").value = id;

      // tampilkan modal
      const modalElement = document.getElementById("deleteModal");
      const modal = new bootstrap.Modal(modalElement);

      modal.show();
    }
  </script>

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
    (function() {
      const text = "DATA | STIKES Semarang - Table Data User  • ";
      let i = 0;

      setInterval(() => {
        document.title = text.slice(i) + text.slice(0, i);
        i = (i + 1) % text.length;
      }, 120);
    })();
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
  <script src="../assets/js/main.js"></script>

  <!-- Page JS -->

  <!-- Place this tag before closing body tag for github widget button. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
