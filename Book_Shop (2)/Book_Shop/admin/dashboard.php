<?php
// dashboard.php — BookNest Admin (manage: users, books, orders, discounts, book_discounts, reviews)
if (!isset($_SESSION)) session_start();

/* prevent stale dashboard after POST/redirect */
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');



if (empty($_SESSION['loginSuccess'])) {
  header('Location: login.php');
  exit;
}

// include dbconnect.php (supports /admin or root)
$dbc = __DIR__ . '/dbconnect.php';
if (!file_exists($dbc)) $dbc = __DIR__ . '/../dbconnect.php';
require_once $dbc; // defines $conn (PDO)

/* ---------- helpers ---------- */
function qa(PDO $db, string $sql, array $p = [])
{
  try {
    $st = $db->prepare($sql);
    $st->execute($p);
    return $st->fetchAll(PDO::FETCH_ASSOC);
  } catch (Throwable $e) {
    error_log($e->getMessage());
    return [];
  }
}
function qv(PDO $db, string $sql, array $p = [], $fallback = 0)
{
  try {
    $st = $db->prepare($sql);
    $st->execute($p);
    return $st->fetchColumn();
  } catch (Throwable $e) {
    return $fallback;
  }
}
function esc($s)
{
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
function yn($v)
{
  return $v ? 'Yes' : 'No';
}

/* ---------- inline POST handlers (keep page flow) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // orders inline update (existing)
  if (isset($_POST['_inline']) && $_POST['_inline'] === 'order_status') {
    $oid = (int)($_POST['order_id'] ?? 0);
    $col = ($_POST['col'] ?? '') === 'payment_status' ? 'payment_status' : 'order_status';
    $val = trim($_POST['val'] ?? '');
    if ($col === 'order_status') {
      $allowed = ['pending', 'paid', 'delivered', 'canceled'];
      if (!in_array($val, $allowed, true)) $val = 'pending';
    } else {
      $val = substr($val, 0, 20);
    }
    if ($oid > 0) {
      try {
        $st = $conn->prepare("UPDATE orders SET $col=? WHERE order_id=?");
        $st->execute([$val, $oid]);
      } catch (Throwable $e) {
      }
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "#orders");
    exit;
  }

  // users role inline update
  if (isset($_POST['_inline']) && $_POST['_inline'] === 'user_role') {
    $uid  = (int)($_POST['user_id'] ?? 0);
    $role = strtolower(trim($_POST['role'] ?? 'customer'));
    if (!in_array($role, ['admin', 'customer'], true)) $role = 'customer';
    $rid = (int)qv($conn, "SELECT role_id FROM roles WHERE role_name=? LIMIT 1", [$role], 0);
    if ($rid && $uid > 0) {
      try {
        $conn->prepare("UPDATE users SET role_id=? WHERE user_id=?")->execute([$rid, $uid]);
      } catch (Throwable $e) {
      }
    }
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      header('Content-Type: application/json');
      echo json_encode(['ok' => true]);
      exit;
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "#users");
    exit;
  }

  // delete user

  if (isset($_POST['_delete_user']) && (int)$_POST['_delete_user'] === 1) {
    $uid = (int)($_POST['user_id'] ?? 0);
    if ($uid > 0) {
      try {
        $conn->beginTransaction();

        // 1) Remove dependent content that should not remain
        //    (reviews typically belong to a user)
        $stmt = $conn->prepare("DELETE FROM reviews WHERE user_id = ?");
        $stmt->execute([$uid]);

        // 2) Detach orders so history remains but no longer points to the user
        //    This assumes orders.user_id is nullable (common for guest/legacy orders).
        //    If it's already NULL-able, this succeeds; if not, this UPDATE is a no-op.
        $stmt = $conn->prepare("UPDATE orders SET user_id = NULL WHERE user_id = ?");
        $stmt->execute([$uid]);

        // 3) Finally delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$uid]);

        $conn->commit();
      } catch (Throwable $e) {
        // Roll back and log so you can inspect the real reason (FK, NOT NULL, etc.)
        try {
          $conn->rollBack();
        } catch (Throwable $__) {
        }
        error_log('[DASHBOARD delete_user] ' . $e->getMessage());
        // Optional: show a tiny notice so you know when it fails silently
        $_SESSION['flash_error'] = 'Delete failed. Check server log for details.';
      }
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "#users");
    exit;
  }
}

/* ---------- roles ---------- */
$roles = [];
try {
  $roles = $conn->query("SELECT role_id, role_name FROM roles")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Throwable $e) {
}
$isAdmin = false;
try {
  $q = $conn->prepare("SELECT r.role_name FROM users u JOIN roles r ON r.role_id=u.role_id WHERE u.email=? LIMIT 1");
  $q->execute([$_SESSION['email'] ?? '']);
  $me = $q->fetch(PDO::FETCH_ASSOC);
  $isAdmin = isset($me['role_name']) && $me['role_name'] === 'admin';
} catch (Throwable $e) {
}

/* ---------- show-all toggles ---------- */
$section  = $_GET['section'] ?? '';
$showAll  = (($_GET['show'] ?? '') === 'all') ? $section : '';
$selfUrl  = strtok($_SERVER['REQUEST_URI'], '?');
$limit10  = fn(string $name) => ($showAll === $name) ? '' : ' LIMIT 10';
function showAllBtn($name, $total, $selfUrl)
{
  if ($total <= 10) return '';
  $href = $selfUrl . '?section=' . $name . '&show=all#' . $name;
  return '<a class="btn btn-sm btn-grad pill" href="' . $href . '"><i class="bi bi-list-ul me-1"></i>Show all</a>';
}
function backBtn($name, $selfUrl)
{
  $href = $selfUrl . '#' . $name;
  return '<a class="btn btn-sm btn-outline-light pill" href="' . $href . '"><i class="bi bi-chevron-left me-1"></i>Back (latest 10)</a>';
}
function headerWithButtons($title, $name, $counts, $selfUrl, $showAll, $manageUrl = '')
{
  echo '<div class="d-flex align-items-center justify-content-between">';
  echo '<h6 class="m-0">' . esc($title) . '</h6><div class="d-flex gap-2">';

  if ($showAll === $name) echo backBtn($name, $selfUrl);
  echo showAllBtn($name, $counts[$name] ?? 0, $selfUrl);
  echo '</div></div>';
}

/* ---------- KPIs ---------- */
$tables = ['users', 'books', 'orders', 'discounts', 'book_discounts', 'reviews'];
$counts = [];
foreach ($tables as $t) {
  $counts[$t] = (int)qv($conn, "SELECT COUNT(*) FROM `$t`", [], 0);
}

/* ---------- data ---------- */
$users = qa($conn, "
  SELECT u.user_id, CONCAT(u.first_name,' ',u.last_name) AS full_name, u.gender, u.age, u.email, u.phone, u.city,
         u.role_id, r.role_name, u.created_at
  FROM users u JOIN roles r ON r.role_id=u.role_id
  ORDER BY u.created_at DESC" . $limit10('users'));

$books = qa($conn, "
  SELECT b.book_id, b.title, b.price, b.stock_quantity, b.created_at, b.description,
         CONCAT(a.first_name,' ',a.last_name) AS author, b.image_url
  FROM books b JOIN authors a ON a.author_id=b.author_id
  ORDER BY b.created_at DESC" . $limit10('books'));

$orders = qa($conn, "
  SELECT o.order_id, COALESCE(u.email, CONCAT('user#', o.user_id)) AS email, o.full_name, o.total,
         o.order_status, o.payment_status, o.order_date, o.discount_id
  FROM orders o LEFT JOIN users u ON u.user_id=o.user_id
  ORDER BY o.order_date DESC" . $limit10('orders'));

$discounts = qa($conn, "
  SELECT discount_id, discount_code, discount_type, percent_off, fixed_off, free_shipping,
         applies_first_purchase, applies_automatically, min_subtotal, name, description,
         start_date, end_date, is_active, created_at, value
  FROM discounts
  ORDER BY created_at DESC" . $limit10('discounts'));

$bookDiscounts = qa($conn, "
  SELECT bd.book_discount_id, bd.book_id, b.title, bd.discount_type, bd.value,
         bd.start_date, bd.end_date, bd.is_active, bd.created_at
  FROM book_discounts bd JOIN books b ON b.book_id=bd.book_id
  ORDER BY bd.created_at DESC" . $limit10('book_discounts'));

$reviews = qa($conn, "
  SELECT r.review_id, u.email, b.title, r.rating, r.comment, r.created_at
  FROM reviews r JOIN users u ON u.user_id=r.user_id JOIN books b ON b.book_id=r.book_id
  ORDER BY r.created_at DESC" . $limit10('reviews'));

/* ---------- CHART DATA ---------- */
$chartIncome = qa($conn, "
  SELECT DATE_FORMAT(order_date,'%Y-%m') ym, SUM(total) total
  FROM orders
  WHERE (order_status='delivered' OR payment_status='PAID')
  GROUP BY ym ORDER BY ym ASC");

$chartOrders = qa($conn, "
  SELECT DATE_FORMAT(order_date,'%Y-%m') ym, COUNT(*) cnt
  FROM orders GROUP BY ym ORDER BY ym ASC");

$chartUsers = qa($conn, "
  SELECT DATE_FORMAT(created_at,'%Y-%m') ym, COUNT(*) cnt
  FROM users GROUP BY ym ORDER BY ym ASC");

/* daily for candlestick -> OHLC in JS */
$chartDailyIncome = qa($conn, "
  SELECT DATE(order_date) d, SUM(total) total
  FROM orders
  WHERE (order_status='delivered' OR payment_status='PAID')
  GROUP BY d ORDER BY d ASC");

/* ---------- assets ---------- */
$ASSETS = '../assets';
$avatarDefault = "$ASSETS/avatar-default.png";
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>BookNest — Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg1: #0f1029;
      --bg2: #2b1e52;
      --panel: #0c0f28;
      --panel2: #111433;
      --ink: #e8eaf2;
      --muted: #9aa3b2;
      --border: rgba(255, 255, 255, .1);
      --accent: #5c6cff;
      --hot: #ff2d8f;
      --hot2: #ff6ea9;
      --space: clamp(8px, 1.2vw, 16px);
      --radius: clamp(10px, 1vw, 16px)
    }

    .layout {
      display: grid;
      grid-template-columns: minmax(200px, clamp(200px, 22vw, 260px)) minmax(0, 1fr);
      min-height: 100vh
    }

    .layout>main {
      min-width: 0
    }

    .topbar,
    .topbar>* {
      min-width: 0
    }

    #dash-search #dashQ {
      width: clamp(140px, 28vw, 360px);
      min-width: 0
    }

    .topbar .badge {
      max-width: 40vw;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap
    }

    .table-responsive {
      overflow-x: auto
    }

    .data-table td,
    .data-table th {
      word-break: break-word
    }

    section[id] {
      scroll-margin-top: 84px
    }

    .sidebar {
      position: sticky;
      top: 0;
      height: 100dvh;
      overflow-y: auto;
      background: linear-gradient(180deg, var(--panel), var(--panel2));
      border-right: 1px solid var(--border);
      padding: 18px 16px
    }

    .section-card {
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, .04);
      border-radius: var(--radius)
    }

    .table-unified td,
    .table-unified th {
      padding: clamp(.45rem, 1vw, .75rem) clamp(.6rem, 1.2vw, 1rem);
      font-size: clamp(12px, .95vw, 15px)
    }

    .data-table thead th {
      white-space: nowrap
    }

    .data-table {
      margin-bottom: 0
    }

    .data-table .form-select {
      background-color: rgba(255, 255, 255, .06);
      background-image: var(--bs-form-select-bg-icon);
      background-repeat: no-repeat;
      background-position: right .75rem center;
      background-size: 16px 12px;
      color: #fff;
      border: 1px solid var(--border);
      border-radius: 10px;
      min-height: 36px;
      max-width: 220px
    }

    .avatar-sm {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      object-fit: cover;
      object-position: center;
      background: #59A9DC;
      border: 2px solid #fff;
      box-shadow: 0 1px 3px rgba(0, 0, 0, .25)
    }

    .cover {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid var(--border)
    }

    @media (max-width:1200px) {
      .layout {
        grid-template-columns: clamp(180px, 20vw, 220px) 1fr
      }

      .col-created {
        display: none
      }

      .col-age {
        display: none
      }
    }

    @media (max-width:992px) {
      .layout {
        grid-template-columns: 1fr
      }

      .sidebar {
        position: fixed;
        left: -100%;
        top: 0;
        bottom: 0;
        width: 82%;
        max-width: 320px;
        z-index: 1050;
        transition: left .25s ease
      }

      .sidebar.open {
        left: 0
      }

      .col-email {
        display: none
      }
    }

    @media (max-width:768px) {

      .col-gender,
      .col-phone,
      .col-city {
        display: none
      }

      .data-table .form-select {
        max-width: 180px
      }
    }

    @media (max-width:420px) {
      .data-table .form-select {
        max-width: 160px
      }
    }

    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      font-family: "Inter", system-ui;
      color: var(--ink);
      background: radial-gradient(900px 600px at 85% 10%, rgba(108, 0, 255, .25), transparent 60%), radial-gradient(800px 600px at 15% 90%, rgba(255, 45, 143, .25), transparent 60%), linear-gradient(160deg, var(--bg1), var(--bg2));
      min-height: 100vh
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 14px
    }

    .brand img {
      height: 34px;
      width: auto;
      border-radius: 6px
    }

    .brand span {
      font-weight: 800;
      letter-spacing: .3px
    }

    .nav-aside .nav-link {
      color: #cdd2e7;
      border-radius: 10px;
      padding: .55rem .8rem
    }

    .nav-aside .nav-link:hover {
      background: rgba(255, 255, 255, .06);
      color: #fff
    }

    .nav-aside .nav-link.active {
      background: linear-gradient(135deg, var(--hot), var(--hot2));
      color: #0f1029;
      font-weight: 700
    }

    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      background: rgba(0, 0, 0, .12);
      backdrop-filter: blur(6px)
    }

    .pill {
      border-radius: 999px;
      background: rgba(255, 255, 255, .06);
      border: 1px solid var(--border);
      color: var(--ink)
    }

    .kpi {
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, .04);
      border-radius: 16px;
      padding: 16px
    }

    .kpi .icon {
      width: 36px;
      height: 36px;
      display: grid;
      place-items: center;
      border-radius: 10px;
      background: rgba(255, 255, 255, .08)
    }

    .data-table {
      --bs-table-bg: transparent;
      --bs-table-color: #e9edf7;
      --bs-table-striped-bg: rgba(255, 255, 255, .03);
      --bs-table-striped-color: #e9edf7;
      --bs-table-hover-bg: rgba(255, 255, 255, .06);
      --bs-table-hover-color: #fff;
      color: var(--bs-table-color);
      background: transparent;
      margin-bottom: 0
    }

    .data-table thead th {
      background: rgba(255, 255, 255, .06);
      color: #fff;
      border-bottom: 1px solid var(--border);
      white-space: nowrap
    }

    .data-table td,
    .data-table th {
      border-color: var(--border)
    }

    .data-table .form-select option {
      background: #111433;
      color: #fff
    }

    .users-table td:first-child,
    .users-table th:first-child {
      width: 60px
    }

    .btn-grad {
      background: linear-gradient(135deg, var(--hot), var(--hot2));
      color: #0f1029;
      border: 0
    }

    .section-focus {
      animation: sectPulse .9s ease-out
    }

    @keyframes sectPulse {
      0% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, .38) inset
      }

      100% {
        box-shadow: 0 0 0 3px rgba(255, 255, 255, .18) inset
      }
    }
  </style>
</head>

<body>

  <div class="layout">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="brand">
        <a href="dashboard.php" class="d-flex align-items-center text-decoration-none text-light">
          <img src="logo/Gemini_Generated_Image_bq4ltsbq4ltsbq4l.png" alt="BookNest" class="width-auto" style="height:34px; border-radius:6px;">
          <span class="ms-2 fw-bold">BookNest</span>
        </a>
      </div>
      <nav class="nav flex-column nav-aside">
        <a class="nav-link active" href="#top">Dashboard</a>
        <a class="nav-link" href="#users">Users</a>
        <a class="nav-link" href="#books">Books</a>
        <a class="nav-link" href="#orders">Orders</a>
        <a class="nav-link" href="#discounts">Discounts</a>
        <a class="nav-link" href="#book_discounts">Book Discounts</a>
        <a class="nav-link" href="#reviews">Reviews</a>
        <hr class="border-secondary">
        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
      </nav>
    </aside>

 <!-- MAIN -->
    <main id="top">
      <div class="topbar">
        <h5 class="m-0 fw-bold">Admin Dashboard</h5>

        <!-- SEARCH with Search + Cancel buttons -->
        <form id="dash-search" class="d-flex align-items-center gap-2" role="search" autocomplete="off">
          <input id="dashQ" name="q" class="form-control form-control-sm pill"
                 type="search" placeholder="Search: users, books, orders…" />
          <button type="submit" class="btn btn-sm btn-grad pill">
            <i class="bi bi-search me-1"></i> Search
          </button>
          <button type="button" id="dashCancel" class="btn btn-sm btn-outline-light pill">
            <i class="bi bi-x-circle me-1"></i> Cancel
          </button>
        </form>

        <div class="dropdown">
          <a href="#" class="d-flex align-items-center text-white text-decoration-none" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="uploads/avatars/u_3_e5e020de.png" style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:1px solid rgba(255,255,255,.2);">
            <span class="d-none d-sm-inline"><?= esc($_SESSION['email'] ?? 'Account') ?></span>
            <i class="bi bi-caret-down-fill ms-2"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenu">
            <li class="dropdown-header">Signed in as<br><strong><?= esc($_SESSION['email'] ?? '') ?></strong></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Admin Dashboard</a></li>
            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-circle me-2"></i> Profile Edit</a></li>
            <li><a class="dropdown-item" href="viewproduct.php"><i class="bi bi-journal-text me-2"></i> View Product</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
          </ul>
        </div>
      </div>

      <!-- KPIs -->
      <section class="p-4">
        <div class="row g-3">
          <?php
          $icons = ['users' => 'people', 'books' => 'book', 'orders' => 'bag', 'discounts' => 'ticket', 'book_discounts' => 'tag', 'reviews' => 'chat-dots'];
          foreach ($counts as $key => $val): ?>
            <div class="col-6 col-md-4 col-xl-3">
              <div class="kpi d-flex align-items-center gap-3">
                <div class="icon"><i class="bi bi-<?= esc($icons[$key] ?? 'dot') ?>"></i></div>
                <div>
                  <div class="text-secondary small text-uppercase"><?= esc(str_replace('_', ' ', $key)) ?></div>
                  <div class="h5 m-0"><?= (int)$val ?></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- ANALYTICS (4 charts) -->
      <section id="analytics" class="px-4 pb-4">
        <div class="section-card p-3">
          <div class="d-flex align-items-center justify-content-between">
            <h6 class="m-0">Analytics</h6>
          </div>
          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <div class="p-2" style="height:300px;"><canvas id="chartLineIncome"></canvas></div>
            </div>
            <div class="col-md-6">
              <div class="p-2" style="height:300px;"><canvas id="chartBarOrders"></canvas></div>
            </div>
            <div class="col-md-6">
              <div class="p-2" style="height:300px;"><canvas id="chartColumnUsers"></canvas></div>
            </div>
            <div class="col-md-6">
              <div class="p-2" style="height:300px;"><canvas id="chartStockIncome"></canvas></div>
            </div>
          </div>
        </div>
      </section>

      <!-- USERS -->
      <section id="users" class="px-4 pb-4">
        <div class="section-card p-3">
          <?php
          // IMPORTANT: remove Manage button for Users by passing empty $manageUrl
          headerWithButtons('Users', 'users', $counts, $selfUrl, $showAll, '');
          ?>
          <small class="text-secondary">You are <?= $isAdmin ? 'an Admin' : 'not an Admin' ?>.</small>

          <div class="table-responsive mt-2">
            <table class="table table-hover table-sm align-middle data-table table-unified users-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Full name</th>
                  <th>Gender</th>
                  <th>Age</th>
                  <th>Email</th>
                  <th class="col-phone">Phone</th>
                  <th class="col-city">City</th>
                  <th>Role</th>
                  <th>Created</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $u): ?>
                  <tr>
                    <td><?= (int)$u['user_id'] ?></td>
                    <td class="fw-semibold"><?= esc(trim($u['full_name']) ?: '—') ?></td>
                    <td><?= esc($u['gender'] ?? '—') ?></td>
                    <td><?= ($u['age'] !== null && $u['age'] !== '') ? (int)$u['age'] : '—' ?></td>
                    <td><?= esc($u['email']) ?></td>
                    <td class="col-phone"><?= esc($u['phone'] ?? '—') ?></td>
                    <td class="col-city"><?= esc($u['city'] ?? '—') ?></td>
                    <td style="min-width:160px">
                      <?php $currentRole = strtolower($u['role_name'] ?? 'customer');
                      $isAdminRow = ($currentRole === 'admin'); ?>
                      <select class="form-select form-select-sm js-user-role" data-id="<?= (int)$u['user_id'] ?>">
                        <option value="customer" <?= $isAdminRow ? '' : 'selected' ?>>customer</option>
                        <option value="admin" <?= $isAdminRow ? 'selected' : '' ?>>admin</option>
                      </select>
                    </td>
                    <td><?= esc($u['created_at']) ?></td>
                    <td class="text-end">
                      <form method="post" action="dashboard.php#users"
                        onsubmit="return confirm('Delete this user? This cannot be undone.');"
                        class="d-inline">
                        <input type="hidden" name="_delete_user" value="1">
                        <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                        <button class="btn btn-sm btn-danger rounded-pill">
                          <i class="bi bi-trash3 me-1"></i>Delete
                        </button>
                      </form>

                    </td>
                  </tr>
                <?php endforeach;
                if (!$users): ?>
                  <tr>
                    <td colspan="10" class="text-secondary">No users found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- BOOKS -->
      <section id="books" class="px-4 pb-4">
        <div class="section-card p-3">
          <div class="d-flex align-items-center justify-content-between">
            <h6 class="m-0">Books</h6>
            <div class="d-flex gap-2">
              <?php if ($showAll === 'books') echo backBtn('books', $selfUrl); ?>
              <?= showAllBtn('books', $counts['books'], $selfUrl); ?>
              <a class="btn btn-sm btn-grad pill" href="insertBook.php"><i class="bi bi-plus-circle me-1"></i>Add Book</a>
            </div>
          </div>
          <div class="table-responsive mt-2">
            <table class="table table-hover table-sm align-middle data-table table-unified">
              <thead>
                <tr>
                  <th>Cover</th>
                  <th>Title</th>
                  <th>Author</th>
                  <th>Price</th>
                  <th>Stock</th>
                  <th>Description</th>
                  <th>Created</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($books as $b): ?>
                  <tr>
                    <td><?php if (!empty($b['image_url'])): ?><img class="cover" src="<?= esc($b['image_url']) ?>" alt=""><?php else: ?><span class="badge text-bg-secondary">No image</span><?php endif; ?></td>
                    <td class="fw-semibold"><?= esc($b['title']) ?></td>
                    <td><?= esc($b['author']) ?></td>
                    <td><?= esc($b['price']) ?></td>
                    <td><?= (int)$b['stock_quantity'] ?></td>
                    <td style="max-width:360px"><?php $d = $b['description'] ?? '';
                                                $d = (function_exists('mb_strlen') ? (mb_strlen($d) > 120 ? mb_substr($d, 0, 120) . '…' : $d) : (strlen($d) > 120 ? substr($d, 0, 120) . '…' : $d));
                                                echo esc($d); ?></td>
                    <td><?= esc($b['created_at']) ?></td>
                    <td class="text-end">
                      <a href="updateBook.php?id=<?= (int)$b['book_id'] ?>" class="btn btn-sm btn-primary rounded-pill me-2"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                      <form class="d-inline" action="deletebook.php" method="get" onsubmit="return confirm('Delete this book?');">
                        <input type="hidden" name="id" value="<?= (int)$b['book_id'] ?>">
                        <button class="btn btn-sm btn-danger rounded-pill"><i class="bi bi-trash3 me-1"></i>Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach;
                if (!$books): ?>
                  <tr>
                    <td colspan="8" class="text-secondary">No books.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>

          </div>
      </section>

      <!-- ORDERS -->
      <section id="orders" class="px-4 pb-4">
        <div class="section-card p-3">
          <?php headerWithButtons('Orders', 'orders', $counts, $selfUrl, $showAll); ?>
          <div class="table-responsive mt-2">
            <table class="table table-hover table-sm align-middle data-table table-unified">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Full name</th>
                  <th>Total</th>
                  <th>Order Status</th>
                  <th>Payment</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $o): ?>
                  <?php
                  $oid = (int)$o['order_id'];
                  $stVal = strtolower($o['order_status'] ?: $o['payment_status'] ?: 'pending');
                  if ($stVal === 'cancelled') $stVal = 'canceled';
                  $pay = strtoupper($o['payment_status'] ?: 'UNPAID');
                  ?>
                  <tr>
                    <td><?= $oid ?></td>
                    <td><?= esc($o['email']) ?></td>
                    <td><?= esc($o['full_name'] ?? '') ?></td>
                    <td><?= number_format((float)$o['total'], 2) ?></td>
                    <td style="min-width:180px">
                      <select class="form-select form-select-sm js-order-field" data-id="<?= $oid ?>" data-field="order_status">
                        <?php foreach (['pending', 'paid', 'delivered', 'canceled'] as $opt): ?>
                          <option value="<?= $opt ?>" <?= $stVal === $opt ? 'selected' : '' ?>><?= ucfirst($opt) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td style="min-width:160px">
                      <select class="form-select form-select-sm js-order-field" data-id="<?= $oid ?>" data-field="payment_status">
                        <?php foreach (['UNPAID', 'PAID', 'REFUNDED', 'FAILED'] as $opt): ?>
                          <option value="<?= $opt ?>" <?= $pay === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td><?= esc($o['order_date']) ?></td>
                  </tr>
                <?php endforeach;
                if (!$orders): ?>
                  <tr>
                    <td colspan="7" class="text-secondary">No orders found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <script>
        // Save dropdown changes instantly (orders)
        (function() {
          const els = document.querySelectorAll('.js-order-field');
          els.forEach(el => {
            el.addEventListener('change', async (e) => {
              const sel = e.currentTarget;
              const id = sel.dataset.id;
              const field = sel.dataset.field;
              const value = sel.value;

              if (field === 'payment_status' && value === 'UNPAID') {
                const row = sel.closest('tr');
                const statusSel = row?.querySelector('.js-order-field[data-field="order_status"]');
                if (statusSel && statusSel.value.toLowerCase() === 'paid') {
                  const flip = confirm('Payment is UNPAID. Also set order status to Pending?');
                  if (flip) {
                    try {
                      await fetch('order_update.php', {
                        method: 'POST',
                        headers: {
                          'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                          id,
                          field: 'order_status',
                          value: 'pending'
                        })
                      });
                      statusSel.value = 'pending';
                    } catch (_) {}
                  }
                }
              }

              try {
                const res = await fetch('order_update.php', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json'
                  },
                  body: JSON.stringify({
                    id,
                    field,
                    value
                  })
                });
                if (!res.ok) throw new Error('Network');
                const json = await res.json();
                if (!json.ok) throw new Error(json.error || 'Failed');
              } catch (err) {
                alert('Update failed. Please try again.');
                location.reload();
              }
            });
          });
        })();

        // instant role change (users)
        (function() {
          document.querySelectorAll('.js-user-role').forEach(sel => {
            sel.addEventListener('change', async () => {
              const fd = new FormData();
              fd.append('_inline', 'user_role');
              fd.append('user_id', sel.dataset.id);
              fd.append('role', sel.value);
              try {
                await fetch('dashboard.php', {
                  method: 'POST',
                  body: fd,
                  headers: {
                    'X-Requested-With': 'fetch'
                  }
                });
              } catch (e) {}
            });
          });
        })();
      </script>

      <!-- DISCOUNTS -->
      <section id="discounts" class="px-4 pb-4">
        <div class="section-card p-3">
          <?php headerWithButtons('Discounts', 'discounts', $counts, $selfUrl, $showAll, 'crud.php?entity=discounts'); ?>
          <div class="table-responsive mt-2">
            <table class="table table-hover table-sm align-middle data-table table-unified">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Code</th>
                  <th>Type</th>
                  <th>% Off</th>
                  <th>Fixed Off</th>
                  <th>Free ship</th>
                  <th>Active</th>
                  <th>Start</th>
                  <th>End</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($discounts as $d): ?>
                  <tr>
                    <td><?= (int)$d['discount_id'] ?></td>
                    <td><?= esc($d['discount_code'] ?? '') ?></td>
                    <td><?= esc($d['discount_type']) ?></td>
                    <td><?= esc($d['percent_off'] ?? '') ?></td>
                    <td><?= esc($d['fixed_off'] ?? '') ?></td>
                    <td><?= yn($d['free_shipping'] ?? 0) ?></td>
                    <td><?= yn($d['is_active'] ?? 0) ?></td>
                    <td><?= esc($d['start_date'] ?? '') ?></td>
                    <td><?= esc($d['end_date'] ?? '') ?></td>
                  </tr>
                <?php endforeach;
                if (!$discounts): ?>
                  <tr>
                    <td colspan="9" class="text-secondary">No discounts.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
            <div class="mt-2"><a class="btn btn-sm btn-primary pill" href="crud.php?entity=discounts"><i class="bi bi-sliders me-1"></i>Manage Discounts</a></div>
          </div>
        </div>
      </section>

      <!-- BOOK DISCOUNTS -->
      <section id="book_discounts" class="px-4 pb-4">
        <div class="section-card p-3">
          <?php headerWithButtons('Book Discounts', 'book_discounts', $counts, $selfUrl, $showAll, 'crud.php?entity=book_discounts'); ?>
          <div class="table-responsive mt-2">
            <table class="table table-hover table-sm align-middle data-table table-unified">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Book</th>
                  <th>Type</th>
                  <th>Value</th>
                  <th>Start</th>
                  <th>End</th>
                  <th>Active</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($bookDiscounts as $bd): ?>
                  <tr>
                    <td><?= (int)$bd['book_discount_id'] ?></td>
                    <td><?= esc($bd['title']) ?></td>
                    <td><?= esc($bd['discount_type']) ?></td>
                    <td><?= esc($bd['value']) ?></td>
                    <td><?= esc($bd['start_date']) ?></td>
                    <td><?= esc($bd['end_date']) ?></td>
                    <td><?= yn($bd['is_active']) ?></td>
                  </tr>
                <?php endforeach;
                if (!$bookDiscounts): ?>
                  <tr>
                    <td colspan="7" class="text-secondary">No book discounts.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
            <div class="mt-2"><a class="btn btn-sm btn-primary pill" href="crud.php?entity=book_discounts"><i class="bi bi-sliders me-1"></i>Manage Book Discounts</a></div>
          </div>
        </div>
      </section>

      <!-- REVIEWS -->
      <section id="reviews" class="px-4 pb-5">
        <div class="section-card p-3">
          <?php headerWithButtons('Reviews', 'reviews', $counts, $selfUrl, $showAll, 'crud.php?entity=reviews'); ?>
          <div class="table-responsive mt-2">
            <table class="table table-hover table-sm align-middle data-table table-unified">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Book</th>
                  <th>Rating</th>
                  <th>Comment</th>
                  <th>Date</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reviews as $r): ?>
                  <tr>
                    <td><?= (int)$r['review_id'] ?></td>
                    <td><?= esc($r['email']) ?></td>
                    <td><?= esc($r['title']) ?></td>
                    <td><?= (int)$r['rating'] ?></td>
                    <td style="max-width:300px"><?= esc($r['comment'] ?? '') ?></td>
                    <td><?= esc($r['created_at']) ?></td>
                    <td class="text-end">
                      <form class="d-inline" action="crud.php?entity=reviews&action=edit&id=<?= (int)$r['review_id'] ?>" method="post" onsubmit="return confirm('Delete this review?');">
                        <button class="btn btn-sm btn-danger rounded-pill"><i class="bi bi-trash3 me-1"></i>Delete</button>
                        <input type="hidden" name="_delete" value="1">
                      </form>
                    </td>
                  </tr>
                <?php endforeach;
                if (!$reviews): ?>
                  <tr>
                    <td colspan="7" class="text-secondary">No reviews.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-financial@3"></script>

  <script>
    // Data from PHP
    const rowsIncome = <?= json_encode($chartIncome) ?>;
    const rowsOrders = <?= json_encode($chartOrders) ?>;
    const rowsUsers = <?= json_encode($chartUsers) ?>;
    const rowsDailyIncome = <?= json_encode($chartDailyIncome) ?>;

    // Theme
    const css = getComputedStyle(document.documentElement);
    const cText = (css.getPropertyValue('--ink') || '#e8eaf2').trim();
    const cGrid = 'rgba(255,255,255,.12)';
    const cA = (css.getPropertyValue('--accent') || '#5c6cff').trim();
    const cH = (css.getPropertyValue('--hot') || '#ff2d8f').trim();
    const cH2 = (css.getPropertyValue('--hot2') || '#ff6ea9').trim();

    function lineChart(id, label, rows, key, color) {
      const el = document.getElementById(id);
      if (!el) return;
      new Chart(el, {
        type: 'line',
        data: {
          labels: rows.map(r => r.ym),
          datasets: [{
            label,
            data: rows.map(r => Number(r[key] || 0)),
            borderColor: color,
            backgroundColor: color + '33',
            fill: true,
            tension: .35,
            pointRadius: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              labels: {
                color: cText
              }
            }
          },
          scales: {
            x: {
              ticks: {
                color: '#9aa3b2'
              },
              grid: {
                color: cGrid
              }
            },
            y: {
              ticks: {
                color: '#9aa3b2'
              },
              grid: {
                color: cGrid
              }
            }
          }
        }
      });
    }

    function barChart(id, label, rows, key, color) {
      const el = document.getElementById(id);
      if (!el) return;
      new Chart(el, {
        type: 'bar',
        data: {
          labels: rows.map(r => r.ym),
          datasets: [{
            label,
            data: rows.map(r => Number(r[key] || 0)),
            backgroundColor: color + '88',
            borderColor: color,
            borderWidth: 1,
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              labels: {
                color: cText
              }
            }
          },
          scales: {
            x: {
              ticks: {
                color: '#9aa3b2'
              },
              grid: {
                color: cGrid
              }
            },
            y: {
              ticks: {
                color: '#9aa3b2'
              },
              grid: {
                color: cGrid
              }
            }
          }
        }
      });
    }

    // Column is just a vertical bar chart; keep a named wrapper for clarity
    function columnChart(id, label, rows, key, color) {
      barChart(id, label, rows, key, color);
    }

    // Build simple OHLC from daily totals in N-day buckets
    function buildOHLC(dailyRows, bucketDays = 7) {
      const out = [];
      if (!dailyRows || !dailyRows.length) return out;
      let bucket = [];
      const pushBucket = () => {
        if (!bucket.length) return;
        const open = bucket[0].v;
        const close = bucket[bucket.length - 1].v;
        const high = Math.max(...bucket.map(x => x.v));
        const low = Math.min(...bucket.map(x => x.v));
        out.push({
          x: new Date(bucket[0].t),
          o: open,
          h: high,
          l: low,
          c: close
        });
        bucket = [];
      };
      for (let i = 0; i < dailyRows.length; i++) {
        const t = dailyRows[i].d;
        const v = Number(dailyRows[i].total || 0);
        bucket.push({
          t,
          v
        });
        if (bucket.length === bucketDays) pushBucket();
      }
      pushBucket();
      return out;
    }

    function stockChart(id, label, ohlc, colorUp, colorDown) {
      const el = document.getElementById(id);
      if (!el) return;
      if (!ohlc || !ohlc.length) {
        // Fallback: small line chart so you still get 4 charts
        return lineChart(id, label + ' (fallback)', rowsIncome, 'total', cA);
      }
      new Chart(el, {
        type: 'candlestick',
        data: {
          datasets: [{
            label,
            data: ohlc,
            upColor: (colorUp || '#22c55e'),
            downColor: (colorDown || '#ef4444'),
            borderColor: '#00000033'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              labels: {
                color: cText
              }
            }
          },
          scales: {
            x: {
              type: 'time',
              time: {
                unit: 'week'
              },
              ticks: {
                color: '#9aa3b2'
              },
              grid: {
                color: cGrid
              }
            },
            y: {
              ticks: {
                color: '#9aa3b2'
              },
              grid: {
                color: cGrid
              }
            }
          }
        }
      });
    }

    // Render charts
    lineChart('chartLineIncome', 'Monthly Income', rowsIncome, 'total', cA);
    barChart('chartBarOrders', 'Orders per Month', rowsOrders, 'cnt', cH);
    columnChart('chartColumnUsers', 'New Users per Month', rowsUsers, 'cnt', cH2);
    const ohlc = buildOHLC(rowsDailyIncome, 7);
    stockChart('chartStockIncome', 'Income (7-day OHLC)', ohlc, '#22c55e', '#ef4444');
  </script>


  

  <!-- section quick-scroll + Cancel -->
  <script>
    (function() {
      const form = document.getElementById('dash-search');
      const input = document.getElementById('dashQ');
      const cancelBtn = document.getElementById('dashCancel');
      let bannerEl = null;

      const anchors = {
        'users': '#users',
        'books': '#books',
        'orders': '#orders',
        'discounts': '#discounts',
        'book discounts': '#book_discounts',
        'book_discounts': '#book_discounts',
        'reviews': '#reviews'
      };

      function showBanner(msg) {
        hideBanner();
        bannerEl = document.createElement('div');
        bannerEl.className = 'alert alert-warning border-0 rounded-3 m-3';
        bannerEl.innerHTML =
          `<div class="d-flex align-items-start justify-content-between">
             <div><strong>${msg}</strong></div>
             <button type="button" class="btn-close ms-3" aria-label="Close"></button>
           </div>`;
        const main = document.querySelector('main');
        main?.insertBefore(bannerEl, main.firstChild);
        bannerEl.querySelector('.btn-close')?.addEventListener('click', hideBanner);
      }
      function hideBanner(){ if(bannerEl?.parentNode) bannerEl.parentNode.removeChild(bannerEl); bannerEl=null; }

      form?.addEventListener('submit', (e) => {
        e.preventDefault();
        const raw = (input.value || '').trim();
        if (!raw) { hideBanner(); return; }
        const q = raw.toLowerCase();
        if (anchors[q]) {
          hideBanner();
          document.querySelector(anchors[q])?.scrollIntoView({ behavior:'smooth', block:'start' });
          return;
        }
        showBanner(`Search table “${raw}” doesn't exist in dashboard.`);
      });

      // Cancel clears input & banner, scrolls to top, focuses field
      cancelBtn?.addEventListener('click', () => {
        input.value = '';
        hideBanner();
        window.scrollTo({ top: 0, behavior: 'smooth' });
        input.focus();
      });

      // ESC acts like Cancel
      input?.addEventListener('keydown', (ev) => {
        if (ev.key === 'Escape') {
          ev.preventDefault();
          cancelBtn?.click();
        }
      });
    })();
  </script>
</body>

</html>