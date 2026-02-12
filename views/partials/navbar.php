<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme shadow-sm"
  id="layout-navbar">

  <!-- Toggle Sidebar -->
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 d-xl-none">
    <a class="nav-item nav-link px-0" href="javascript:void(0)">
      <i class="icon-base bx bx-menu icon-md"></i>
    </a>
  </div>

  <div class="navbar-nav me-auto d-flex align-items-center gap-2">
    <span class="ms-2 text-success">
    </span>

    <span class="fw-bold fs-5 text-primary">
    </span>
  </div>

  <!-- Right Side -->
  <ul class="navbar-nav flex-row align-items-center gap-3">

    <!-- Status kecil (opsional keren) -->
    <!-- <li class="d-none d-md-block">
      <span class="badge bg-label-success px-3 py-2">
        Sistem Aktif
      </span>
    </li> -->

    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow p-0" href="#" data-bs-toggle="dropdown">
        <div class="d-flex align-items-center gap-2">

          <div class="avatar avatar-online">
            <img src="../assets/img/avatars/1.png" class="rounded-circle" />
          </div>

          <div class="d-none d-md-block text-start">
            <div class="fw-semibold lh-1">Admin</div>
            <small class="text-muted">Petugas Kesehatan</small>
          </div>

          <i class="icon-base bx bx-chevron-down text-muted"></i>
        </div>
      </a>

      <ul class="dropdown-menu dropdown-menu-end shadow-sm mt-2">

        <li class="px-3 py-2">
          <div class="fw-semibold">Admin</div>
          <small class="text-muted">Petugas Kesehatan</small>
        </li>

        <li>
          <hr class="dropdown-divider">
        </li>

        <li>
          <a class="dropdown-item text-danger" href="../logout.php">
            <i class="icon-base bx bx-power-off me-2"></i> Logout
          </a>
        </li>

      </ul>
    </li>

  </ul>

</nav>
