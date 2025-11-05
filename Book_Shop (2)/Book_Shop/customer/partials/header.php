<?php
// /customer/partials/header.php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/dbconnect.php';
require_once __DIR__ . '/../includes/discounts.php';
if (!isset($_SESSION)) {
  session_start();
}

$pageTitle = $pageTitle ?? 'BookNest';

/* ---- Merge guest cart after login so badge shows correct count ---- */
if (!empty($_SESSION['user_id']) && !empty($_SESSION['cart'])) {
  try {
    cart_merge_session_into_db($conn);
  } catch (Throwable $e) { /* ignore */
  }
}

/* ---- Data for nav + cart ---- */
try {
  $navCats = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC")
    ->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  $navCats = [];
}

/* IMPORTANT: pass $conn so DB carts show the right count */
try {
  $cartCount = cart_count($conn);
} catch (Throwable $e) {
  $cartCount = 0;
}

/* ---- Profile bits ---- */
$displayEmail = $_SESSION['user_email'] ?? null;
$displayName  = $_SESSION['user_name']  ?? null;

/* ---- Avatar ---- */
$avatarMini = 'assets/img/avatar-default.png';
if ($displayEmail) {
  try {
    $q = $conn->prepare("SELECT profile_image_url FROM users WHERE email=? LIMIT 1");
    $q->execute([$displayEmail]);
    if ($row = $q->fetch(PDO::FETCH_ASSOC)) {
      if (!empty($row['profile_image_url'])) $avatarMini = $row['profile_image_url'];
    }
  } catch (Throwable $e) { /* ignore lookup */
  }
}

/* ---- Welcome discount banner (first purchase, auto) ---- */
$welcomeText = null;
if (!empty($_SESSION['user_id'])) {
  $auto = get_auto_first_purchase_discount($conn);
  if ($auto && is_first_paid_order($conn, (int)$_SESSION['user_id'])) {
    $parts = [];
    if (!empty($auto['percent_off'])) {
      $parts[] = rtrim(rtrim(number_format($auto['percent_off'], 2, '.', ''), '0'), '.') . '% OFF';
    }
    if (!empty($auto['free_shipping'])) {
      $parts[] = 'Free Shipping';
    }
    if (!empty($parts)) {
      $welcomeText = implode(' + ', $parts);
    }
  }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= h($pageTitle) ?></title>

  <!-- Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Swiper (home hero) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
</head>

<body>
  <header class="bn-header border-bottom">
    <!-- Welcome banner (only when eligible) -->
    <?php if ($welcomeText): ?>
      <div class="alert alert-success text-center rounded-0 mb-0 py-2">
        ✨ Welcome offer for your first order: <strong><?= h($welcomeText) ?></strong> — applied automatically at
        checkout.
      </div>
    <?php endif; ?>

    <div class="container-xxl d-flex align-items-center justify-content-between py-3 gap-3">

      <a class="navbar-brand d-flex align-items-center fw-bold fs-4 text-dark mb-0" href="index.php">
        <img class="bn-logo me-2" src="assets/img/logo.png" alt="BookNest logo">
        <span class="fw-bold">BookNest</span>
      </a>


      <!-- Search -->
      <form action="product.php" method="get" class="bn-search d-none d-md-flex flex-grow-1 mx-3" role="search">
        <div class="input-group bn-search-group">
          <input type="search" name="q" id="bnSearchInput" class="form-control"
            placeholder="Search books, authors, ISBN..." value="<?= h($_GET['q'] ?? '') ?>"
            aria-label="Search books">
          <button class="btn btn-primary" type="submit">Search</button>
          <button class="btn btn-outline-secondary" type="button" id="bnSearchClear">Cancel</button>
        </div>
      </form>

      <!-- Right side: Cart + Profile/Login -->
      <div class="d-flex align-items-center gap-2">

        <!-- Cart -->
        <a class="btn btn-dark rounded-pill position-relative" href="cart.php" aria-label="Cart">
          <i class="bi bi-cart3"></i>
          <?php if ($cartCount > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
              style="font-size:.7rem; min-width:1.25rem; line-height:1.1rem;">
              <?= (int)$cartCount ?>
            </span>
          <?php endif; ?>
        </a>

        <?php if (!empty($displayEmail)): ?>
          <!-- Profile dropdown -->
          <div class="dropdown">
            <button class="btn btn-light rounded-pill d-flex align-items-center gap-2 px-3"
              data-bs-toggle="dropdown" aria-expanded="false">
              <img src="assets\img\download.png"
                style="width:24px; height:24px; object-fit:cover; border-radius:50%;" alt="Avatar">
              <span class="d-none d-sm-inline"><?= h($displayName ?: $displayEmail) ?></span>
              <i class="bi bi-caret-down-fill small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
              <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>My Profile</a>
              </li>
              <li><a class="dropdown-item" href="orders.php"><i class="bi bi-bag me-2"></i>My Orders</a></li>
              <li><a class="dropdown-item" href="wishlist.php"><i class="bi bi-heart me-2"></i>My Wishlist</a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i
                    class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="login.php" class="btn btn-dark rounded-pill">Login</a>
        <?php endif; ?>

      </div>
    </div>

    <!-- Category chips -->
    <div class="container-xxl py-2">
      <nav class="bn-cats d-flex flex-wrap gap-2">
        <?php foreach ($navCats as $c): ?>
          <a class="btn btn-light btn-sm rounded-pill"
            href="<?= url('product.php', ['category' => (int)$c['category_id']]) ?>"><?= h($c['category_name']) ?></a>
        <?php endforeach; ?>
      </nav>
    </div>
  </header>

  <main class="bn-main py-4">
    <div class="container-xxl">