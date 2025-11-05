<?php
// ---------- Prepare user mini-avatar (do this before the navbar) ----------
$avatarDefault = 'assets/avatar-default.png';
$avatarMini    = $avatarDefault;
$displayEmail  = $_SESSION['email'] ?? 'Guest';

if (!empty($_SESSION['email'])) {
  try {
    $q = $conn->prepare("SELECT profile_image_url FROM users WHERE email=? LIMIT 1");
    $q->execute([$_SESSION['email']]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if (!empty($row['profile_image_url'])) {
      $avatarMini = $row['profile_image_url'];
    }
  } catch (Throwable $e) { /* ignore */
  }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top py-2">
  <div class="container-fluid">

    <!-- Brand (Logo + title) -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
      <img src="logo\Gemini_Generated_Image_bq4ltsbq4ltsbq4l.png" style="width:40px; height:40px; border-radius:8px; margin-left: 10px;">

    </a>

    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#mainNav" aria-controls="mainNav"
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collapsible content -->
    <div class="collapse navbar-collapse" id="mainNav">
      <!-- Left links -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if (!empty($_SESSION['loginSuccess'])): ?>
          <li class="nav-item">
            <a class="nav-link<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? ' active' : '' ?>"
              href="dashboard.php">Admin Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?= basename($_SERVER['PHP_SELF']) === 'viewProduct.php' ? ' active' : '' ?>"
              href="viewProduct.php">View Product</a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Search (shows when logged in) -->
      <?php if (!empty($_SESSION['loginSuccess'])): ?>
        <form class="d-flex my-2 my-lg-0 order-2 order-lg-1" role="search" method="get" action="viewProduct.php">
          <input name="tsearch" class="form-control form-control-sm me-2"
            type="search" placeholder="Search products..." aria-label="Search">
          <button name="bsearch" class="btn btn-sm btn-outline-light" type="submit">Search</button>
        </form>
      <?php endif; ?>

      <!-- Right side: user + logout -->
      <ul class="navbar-nav ms-lg-3 mb-2 mb-lg-0 order-1 order-lg-2">
        <?php if (!empty($_SESSION['loginSuccess'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              <img src="uploads\avatars\u_3_e5e020de.png" style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:1px solid rgba(255,255,255,.2);">
              <span class="d-none d-sm-inline"><?= htmlspecialchars($displayEmail) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li class="dropdown-item-text small text-muted">
                Signed in as<br><strong><?= htmlspecialchars($displayEmail) ?></strong>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
              <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-circle me-2"></i>Profile Edit</a></li>
              <li><a class="dropdown-item" href="viewProduct.php"><i class="bi bi-card-list me-2"></i>View Product</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Log in</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<style>
  /* Small, responsive logo and avatar */
  .brand-logo {
    height: 36px;
    width: auto;
    border-radius: 6px;
    object-fit: contain;
  }

  .avatar-mini {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid rgba(255, 255, 255, .2);
  }

  @media (max-width: 576px) {
    .brand-logo {
      height: 30px;
    }

    .avatar-mini {
      width: 28px;
      height: 28px;
    }
  }
</style>