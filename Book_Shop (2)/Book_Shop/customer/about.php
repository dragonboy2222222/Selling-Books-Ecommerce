<?php
// about.php — BookNest About (responsive, JS-free, scoped styles)
$pageTitle = "BookNest — About";
if (!isset($_SESSION)) {
  session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';
require_once __DIR__ . '/partials/header.php';

/* ---------- Safe helpers ---------- */
if (!function_exists('table_exists')) {
  function table_exists(PDO $conn, string $table): bool
  {
    $q = $conn->prepare("SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME=? LIMIT 1");
    $q->execute([$table]);
    return (bool)$q->fetchColumn();
  }
}
if (!function_exists('col_exists')) {
  function col_exists(PDO $conn, string $table, string $col): bool
  {
    $q = $conn->prepare("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=? LIMIT 1");
    $q->execute([$table, $col]);
    return (bool)$q->fetchColumn();
  }
}
function count_rows(PDO $conn, string $table): int
{
  if (!table_exists($conn, $table)) return 0;
  return (int)$conn->query("SELECT COUNT(*) c FROM `$table`")->fetch(PDO::FETCH_ASSOC)['c'];
}

/* ---------- Live stats ---------- */
$stats = [
  'books'      => count_rows($conn, 'books'),
  'authors'    => count_rows($conn, 'authors'),
  'categories' => count_rows($conn, 'categories'),
  'users'      => count_rows($conn, 'users'),
];

/* ---------- Promotions ---------- */
$activePromos = 0;
$booksOnSale = 0;
$hasWelcome = 0;

if (table_exists($conn, 'discounts')) {
  $w = "is_active=1";
  if (col_exists($conn, 'discounts', 'start_date')) $w .= " AND (start_date IS NULL OR start_date <= CURDATE())";
  if (col_exists($conn, 'discounts', 'end_date'))   $w .= " AND (end_date   IS NULL OR end_date   >= CURDATE())";
  $activePromos = (int)$conn->query("SELECT COUNT(*) c FROM discounts WHERE $w")->fetch(PDO::FETCH_ASSOC)['c'];
  if (col_exists($conn, 'discounts', 'applies_first_purchase')) {
    $hasWelcome = (int)$conn->query("SELECT COUNT(*) c FROM discounts WHERE $w AND applies_first_purchase=1")->fetch(PDO::FETCH_ASSOC)['c'];
  }
}
if (table_exists($conn, 'book_discounts')) {
  if (col_exists($conn, 'book_discounts', 'start_date') && col_exists($conn, 'book_discounts', 'end_date')) {
    $booksOnSale = (int)$conn->query("SELECT COUNT(*) c FROM book_discounts WHERE is_active=1 AND CURDATE() BETWEEN start_date AND end_date")->fetch(PDO::FETCH_ASSOC)['c'];
  } else {
    $booksOnSale = (int)$conn->query("SELECT COUNT(*) c FROM book_discounts WHERE is_active=1")->fetch(PDO::FETCH_ASSOC)['c'];
  }
}
?>

<!-- Scoped styles so nothing else on the site is affected -->
<style>
  .about-page .kicker {
    letter-spacing: .08em;
    text-transform: uppercase;
    font-size: .8rem;
    color: #6c757d
  }

  .about-page .stat-card {
    border: 1px solid #eef0f3;
    border-radius: 16px;
    padding: 16px;
    background: #fff
  }

  .about-page .stat-num {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1
  }

  .about-page .stat-label {
    font-size: .85rem;
    color: #6c757d
  }

  .about-page .soft-badge {
    background: #f3f6ff;
    border: 1px solid #e3e9ff;
    color: #2f54eb;
    border-radius: 999px;
    padding: .25rem .6rem;
    font-size: .8rem
  }

  .about-page .promo-card {
    border: 1px solid #eef0f3;
    border-radius: 16px;
    background: #fff;
    padding: 16px
  }

  .about-page .check-item {
    display: flex;
    gap: .5rem;
    align-items: flex-start;
    margin: .45rem 0
  }

  .about-page .check-item i {
    color: #22c55e
  }

  .about-page .bullet-item {
    margin: .45rem 0;
    color: #495057
  }

  .about-page .promise {
    border: 1px dashed #e5e7eb;
    border-radius: 16px;
    padding: 16px;
    background: #fcfcfd
  }
</style>

<div class="about-page">
  <!-- HERO -->
  <section class="mb-4">
    <div class="row g-4 align-items-center">
      <div class="col-lg-7">
        <div class="kicker mb-2">Welcome</div>
        <h1 class="display-6 fw-bold mb-2">About BookNest</h1>
        <p class="lead text-muted mb-0">
          BookNest is a clean, fast online bookstore for effortless browsing and transparent pricing.
          We show real-time prices straight from our database and keep checkout simple and secure.
        </p>
      </div>

      <div class="col-lg-5">
        <div class="row g-3 row-cols-2">
          <div class="col">
            <div class="stat-card h-100 text-center">
              <div class="mb-1">📚</div>
              <div class="stat-num"><?= (int)$stats['books'] ?></div>
              <div class="stat-label">Books</div>
            </div>
          </div>
          <div class="col">
            <div class="stat-card h-100 text-center">
              <div class="mb-1">✍️</div>
              <div class="stat-num"><?= (int)$stats['authors'] ?></div>
              <div class="stat-label">Authors</div>
            </div>
          </div>
          <div class="col">
            <div class="stat-card h-100 text-center">
              <div class="mb-1">🏷️</div>
              <div class="stat-num"><?= (int)$stats['categories'] ?></div>
              <div class="stat-label">Categories</div>
            </div>
          </div>
          <div class="col">
            <div class="stat-card h-100 text-center">
              <div class="mb-1">👥</div>
              <div class="stat-num"><?= (int)$stats['users'] ?></div>
              <div class="stat-label">Users</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- DISCOUNTS & PROMOS -->
  <section class="mb-5">
    <h2 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
      Discounts &amp; Promotions
      <span class="soft-badge">On sale: <?= (int)$booksOnSale ?></span>
      <span class="soft-badge">Promos: <?= (int)$activePromos ?></span>
    </h2>

    <div class="row g-3 row-cols-1 row-cols-lg-2">
      <div class="col d-flex">
        <div class="promo-card h-100 w-100">
          <h3 class="h6 fw-bold mb-2">How discounts work</h3>
          <div class="check-item">
            <i class="bi bi-check2-circle"></i>
            <span>Sale books show a red badge (e.g., <b>-20%</b>) and a strike-through original price.</span>
          </div>
          <div class="check-item">
            <i class="bi bi-check2-circle"></i>
            <span>Order-level promotions (coupons or auto promos) apply in cart/checkout.</span>
          </div>
          <div class="check-item">
            <i class="bi bi-check2-circle"></i>
            <span>Per-book sale price can combine with an order promo. Free-shipping promos remove the fee.</span>
          </div>
          <div class="check-item">
            <i class="bi bi-check2-circle"></i>
            <span>Prices are shown without decimals for a clean look (e.g., <b>$1,090</b>).</span>
          </div>
        </div>
      </div>

      <div class="col d-flex">
        <div class="promo-card h-100 w-100">
          <h3 class="h6 fw-bold mb-2">What’s active now</h3>
          <ul class="mb-0 ps-3">
            <li class="bullet-item"><b><?= (int)$booksOnSale ?></b> book<?= $booksOnSale == 1 ? '' : 's' ?> currently on sale.</li>
            <li class="bullet-item"><b><?= (int)$activePromos ?></b> site promotion<?= $activePromos == 1 ? '' : 's' ?> active (coupons or auto deals).</li>
            <li class="bullet-item">
              <b>Welcome offer:</b>
              <?= $hasWelcome ? 'first-time customers get an automatic discount at checkout.' : 'occasionally available—watch the cart for details.' ?>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- PROMISE -->
  <section class="mb-4">
    <h2 class="h5 fw-bold mb-2">Our Promise</h2>
    <div class="promise">
      <ul class="list-unstyled text-muted mb-0">
        <li class="mb-1">• Clean, distraction-free browsing.</li>
        <li class="mb-1">• Accurate, transparent pricing straight from our database.</li>
        <li class="mb-1">• Fast search, reliable cart, and smooth checkout.</li>
        <li class="mb-1">• Fair discounts: what you see is what you pay.</li>
      </ul>
    </div>
  </section>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>