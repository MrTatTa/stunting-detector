<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="index.html" class="app-brand-link">
      <span class="app-brand-logo demo">
        <!-- SVG Logo -->
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-2">Sneat</span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
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
    <li class="menu-item <?= in_array($currentPage, ['table-data-ibu.php','table-data-parameter.php']) ? 'active open' : '' ?>">
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
