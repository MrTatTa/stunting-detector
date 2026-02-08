<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo d-flex align-items-center justify-content-center position-relative"
    style="padding: 20px 12px; height: 180px;">

    <a href="home.php"
      class="app-brand-link d-flex flex-column align-items-center justify-content-center text-center w-100">

      <img
        src="../assets/img/logo-no-bg.png"
        alt="Logo STIKES Semarang"
        style="
        width: 100px;
        max-width: 100%;
        height: auto;
        object-fit: contain;
        margin-bottom: 10px;
      " />

      <span
        class="app-brand-text menu-text fw-semibold text-uppercase"
        style="
        font-size: 1rem;
        letter-spacing: 1px;
        line-height: 1.3;
      ">
        STIKES SEMARANG
      </span>
    </a>

    <a href="javascript:void(0);"
      class="layout-menu-toggle menu-link text-large position-absolute top-0 end-0 mt-2 me-2">
      <i class="bx bx-chevron-left d-none d-xl-block"></i>
    </a>
  </div>

  <div class="menu-divider mt-0"></div>
  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboards -->
    <li class="menu-item <?= $currentPage == 'home.php' ? 'active open' : '' ?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-home-smile"></i>
        <div class="text-truncate">Dashboards</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?= $currentPage == 'home.php' ? 'active' : '' ?>">
          <a href="home.php" class="menu-link">
            <div class="text-truncate">Analytics</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Apps & Pages</span>
    </li>
    <!-- Pages -->
    <li class="menu-item <?= $currentPage == 'prediksi.php' ? 'active' : '' ?>">
      <a href="prediksi.php" class="menu-link">
        <i class="menu-icon tf-icons bx bx-calculator"></i>
        <div class="text-truncate">Prediksi</div>
      </a>
    </li>

    <!-- Tables -->
    <li class="menu-item <?= in_array($currentPage, ['table-data-ibu.php', 'table-data-parameter.php']) ? 'active open' : '' ?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-bar-chart"></i>
        <div class="text-truncate">Tables</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item <?= $currentPage == 'table-data-ibu.php' ? 'active' : '' ?>">
          <a href="table-data-ibu.php" class="menu-link">
            <div class="text-truncate">Data Ibu</div>
          </a>
        </li>
        <li class="menu-item <?= $currentPage == 'table-data-parameter.php' ? 'active' : '' ?>">
          <a href="table-data-parameter.php" class="menu-link">
            <div class="text-truncate">Data Parameter</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Misc</span>
    </li>
    <!-- Misc Menu Items -->
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-dock-top"></i>
        <div class="text-truncate" data-i18n="Account Settings">Account Settings</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="html/pages-account-settings-account.html" class="menu-link">
            <div class="text-truncate" data-i18n="Account">Account</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="html/pages-account-settings-notifications.html" class="menu-link">
            <div class="text-truncate" data-i18n="Notifications">Notifications</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="html/pages-account-settings-connections.html" class="menu-link">
            <div class="text-truncate" data-i18n="Connections">Connections</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-lock-open-alt"></i>
        <div class="text-truncate" data-i18n="Authentications">Authentications</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="html/auth-login-basic.html" class="menu-link" target="_blank">
            <div class="text-truncate" data-i18n="Basic">Login</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="html/auth-register-basic.html" class="menu-link" target="_blank">
            <div class="text-truncate" data-i18n="Basic">Register</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="html/auth-forgot-password-basic.html" class="menu-link" target="_blank">
            <div class="text-truncate" data-i18n="Basic">Forgot Password</div>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</aside>