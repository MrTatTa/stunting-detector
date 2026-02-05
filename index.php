<?php
require_once "config.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  try {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
      throw new Exception("Username dan password wajib diisi");
    }

    $stmt = mysqli_prepare(
      $conn,
      "SELECT id, username, password, role
       FROM users
       WHERE username = ?
       LIMIT 1"
    );

    if (!$stmt) {
      throw new Exception("Kesalahan sistem");
    }

    mysqli_stmt_bind_param($stmt, "s", $username);

    if (!mysqli_stmt_execute($stmt)) {
      throw new Exception("Kesalahan sistem");
    }

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
      throw new Exception("Username tidak ditemukan");
    }

    if (!password_verify($password, $user['password'])) {
      throw new Exception("Password salah");
    }

    // LOGIN BERHASIL
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role']     = $user['role'];

    header("Location: views/home.php");
    exit;
  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}
?>

<!doctype html>

<html
  lang="en"
  class="layout-wide customizer-hide"
  data-assets-path="assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Demo: Login Basic - Pages | Sneat - Bootstrap Dashboard FREE</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="assets/vendor/fonts/iconify-icons.css" />

  <!-- Core CSS -->
  <!-- build:css assets/vendor/css/theme.css  -->

  <link rel="stylesheet" href="assets/vendor/css/core.css" />
  <link rel="stylesheet" href="assets/css/demo.css" />

  <!-- Vendors CSS -->

  <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <!-- endbuild -->

  <!-- Page CSS -->
  <!-- Page -->
  <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css" />

  <!-- Helpers -->
  <script src="assets/vendor/js/helpers.js"></script>
  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

  <script src="assets/js/config.js"></script>
  <style>
    html,
    body {
      width: 100%;
      height: 100%;
      margin: 0;
      overflow: hidden;
      background: transparent !important;
    }

    /* Matikan background auth bawaan Sneat */
    .authentication-wrapper::before {
      display: none !important;
    }

    #bg-dots {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 0;
      background: #f5f7ff;
    }

    .authentication-wrapper,
    .container-xxl {
      position: relative;
      z-index: 2;
    }
  </style>
</head>

<body>
  <canvas id="bg-dots"></canvas>

  <!-- Content -->

  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <!-- <div class="authentication-inner"> -->
      <!-- Register -->
      <div class="card px-sm-6 px-0">
        <div class="card-body">
          <!-- Logo -->

          <!-- /Logo -->
          <h4 class="mb-1">Selamat Datang! 👋</h4>
          <p class="mb-6">Silahkan login untuk melanjutkan</p>

          <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
              <?= htmlspecialchars($error) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>
          <form id="formAuthentication" class="mb-6" method="POST">
            <div class="mb-6">
              <label for="username" class="form-label">Username</label>
              <input
                type="text"
                class="form-control"
                id="username"
                name="username"
                placeholder="Masukkan username"
                autofocus
                required />
            </div>

            <div class="mb-6 form-password-toggle">
              <label class="form-label" for="password">Password</label>
              <div class="input-group input-group-merge">
                <input
                  type="password"
                  id="password"
                  class="form-control"
                  name="password"
                  placeholder="••••••••••"
                  required />
                <span class="input-group-text cursor-pointer">
                  <i class="icon-base bx bx-hide"></i>
                </span>
              </div>
            </div>

            <div class="mb-6">
              <button type="submit" class="btn btn-primary d-grid w-100">
                Login
              </button>
            </div>
          </form>
        </div>
      </div>
      <!-- /Register -->
    </div>
  </div>
  </div>

  <!-- / Content -->

  <!-- Core JS -->

  <script src="assets/vendor/libs/jquery/jquery.js"></script>

  <script src="assets/vendor/libs/popper/popper.js"></script>
  <script src="assets/vendor/js/bootstrap.js"></script>

  <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

  <script src="assets/vendor/js/menu.js"></script>

  <!-- endbuild -->

  <!-- Vendors JS -->

  <!-- Main JS -->

  <script src="assets/js/main.js"></script>

  <!-- Page JS -->

  <!-- Place this tag before closing body tag for github widget button. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const canvas = document.getElementById('bg-dots');
      const ctx = canvas.getContext('2d');

      function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
      }
      resize();
      window.addEventListener('resize', resize);

      const mouse = {
        x: -9999,
        y: -9999,
        radius: 80
      };

      window.addEventListener('mousemove', e => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
      });

      window.addEventListener('mouseleave', () => {
        mouse.x = -9999;
        mouse.y = -9999;
      });

      const spacing = 40; // jarak grid
      const dots = [];

      class Dot {
        constructor(x, y) {
          this.baseX = x;
          this.baseY = y;
          this.x = x;
          this.y = y;
          this.size = 3;
        }

        draw() {
          ctx.beginPath();
          ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
          ctx.fillStyle = '#c4ceff';
          ctx.fill();
        }

        update() {
          const dx = this.x - mouse.x;
          const dy = this.y - mouse.y;
          const dist = Math.sqrt(dx * dx + dy * dy);

          if (dist < mouse.radius) {
            const force = (mouse.radius - dist) / mouse.radius;
            this.x += (dx / dist) * force * 6;
            this.y += (dy / dist) * force * 6;
          } else {
            // balik ke posisi grid
            this.x += (this.baseX - this.x) * 0.08;
            this.y += (this.baseY - this.y) * 0.08;
          }

          this.draw();
        }
      }

      function init() {
        dots.length = 0;
        for (let y = spacing / 2; y < canvas.height; y += spacing) {
          for (let x = spacing / 2; x < canvas.width; x += spacing) {
            dots.push(new Dot(x, y));
          }
        }
      }

      init();
      window.addEventListener('resize', init);

      function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        dots.forEach(dot => dot.update());
        requestAnimationFrame(animate);
      }

      animate();
    });
  </script>
</body>

</html>