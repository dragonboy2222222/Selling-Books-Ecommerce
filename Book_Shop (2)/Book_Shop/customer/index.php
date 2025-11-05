<?php
// customer/index.php — BookNest Home
$pageTitle = "BookNest — Home";
if (!isset($_SESSION)) {
  session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';
require_once __DIR__ . '/includes/discounts.php';
require_once __DIR__ . '/partials/header.php';

/* -------- helpers for schema detection (only if not already defined) -------- */
if (!function_exists('table_exists')) {
  function table_exists(PDO $conn, string $table): bool
  {
    $q = $conn->prepare("SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? LIMIT 1");
    $q->execute([$table]);
    return (bool)$q->fetchColumn();
  }
}
if (!function_exists('col_exists')) {
  function col_exists(PDO $conn, string $table, string $column): bool
  {
    $q = $conn->prepare("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1");
    $q->execute([$table, $column]);
    return (bool)$q->fetchColumn();
  }
}

/* ---------- cover path normalizer (works for admin uploads) ---------- */
if (!function_exists('cover_src')) {
  function cover_src(?string $raw): string
  {
    $u = trim((string)$raw);
    if ($u === '') return 'assets/img/book1.png';

    // External URL
    if (stripos($u, 'http://') === 0 || stripos($u, 'https://') === 0) {
      if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $u = preg_replace('#^http://#i', 'https://', $u);
      }
      return $u;
    }

    // Already root-absolute
    if ($u[0] === '/') return $u;

    // Clean ./ or ../ prefixes
    $u = preg_replace('#^(\./|(\.\./)+)#', '', $u);

    // Saved from admin as "uploads/…"
    if (stripos($u, 'uploads/') === 0) return '/admin/' . $u;

    // Saved as "admin/uploads/…"
    if (stripos($u, 'admin/') === 0) return '/' . ltrim($u, '/');

    // Generic relative → make root absolute
    return '/' . ltrim($u, '/');
  }
}

/* ------------------------------- CONFIG ----------------------------------- */
$cardsPerRow = 5;
$rowsToShow  = 2;
$LIMIT       = $cardsPerRow * $rowsToShow;

/* -------------------- data fetch (ratings from reviews, image_url) --------- */
function selectBookCardSQL(string $where = '', string $orderBy = '', string $limitPlaceholder = '?'): string
{
  return "
    SELECT 
      b.book_id,
      b.title,
      b.price,
      b.image_url,
      CONCAT(a.first_name,' ',a.last_name) AS author_name,
      ROUND(COALESCE(AVG(r.rating), 0), 1)  AS rating_manual,
      COUNT(r.review_id)                    AS reviews_manual,
      MAX(b.created_at)                     AS created_sort
    FROM books b
    JOIN authors a ON a.author_id = b.author_id
    LEFT JOIN reviews r ON r.book_id = b.book_id
    $where
    GROUP BY b.book_id
    $orderBy
    LIMIT $limitPlaceholder
  ";
}

/* New Arrivals (newest) */
$orderBooksBy = "ORDER BY created_sort DESC, b.book_id DESC";
$stNew = $conn->prepare(selectBookCardSQL('', $orderBooksBy));
$stNew->bindValue(1, $LIMIT, PDO::PARAM_INT);
$stNew->execute();
$newArrivals = $stNew->fetchAll(PDO::FETCH_ASSOC);

/* Best Selling (all-time by order count) */
$bestSelling = [];
if (table_exists($conn, 'order_items')) {
  $orderBooksBy2 = col_exists($conn, 'books', 'created_at') ? " , MAX(bb.created_at) DESC" : "";
  $stBest = $conn->prepare("
    SELECT 
      bb.book_id, bb.title, bb.price, bb.image_url,
      ROUND(COALESCE(AVG(rr.rating), 0), 1)  AS rating_manual,
      COUNT(rr.review_id)                    AS reviews_manual,
      CONCAT(aa.first_name,' ',aa.last_name) AS author_name,
      COUNT(*) AS sold_count
    FROM order_items oi
    JOIN books bb   ON bb.book_id   = oi.book_id
    JOIN authors aa ON aa.author_id = bb.author_id
    LEFT JOIN reviews rr ON rr.book_id = bb.book_id
    GROUP BY bb.book_id
    ORDER BY sold_count DESC $orderBooksBy2
    LIMIT ?
  ");
  $stBest->bindValue(1, $LIMIT, PDO::PARAM_INT);
  $stBest->execute();
  $bestSelling = $stBest->fetchAll(PDO::FETCH_ASSOC);
}
if (!$bestSelling) $bestSelling = $newArrivals;

/* Popular This Month (last 30 days) */
$popularMonth = [];
if (table_exists($conn, 'orders') && table_exists($conn, 'order_items')) {
  $conds = [];
  if (col_exists($conn, 'orders', 'order_date')) $conds[] = "o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
  if (col_exists($conn, 'orders', 'created_at')) $conds[] = "o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
  $whereDate = $conds ? ("WHERE " . implode(" OR ", $conds)) : "";
  $orderBooksBy3 = col_exists($conn, 'books', 'created_at') ? " , MAX(b.created_at) DESC" : "";

  $sqlPop = "
    SELECT 
      b.book_id, b.title, b.price, b.image_url,
      ROUND(COALESCE(AVG(rr.rating), 0), 1)  AS rating_manual,
      COUNT(rr.review_id)                    AS reviews_manual,
      CONCAT(a.first_name,' ',a.last_name) AS author_name,
      COUNT(*) AS sold_30d
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.order_id
    JOIN books b        ON b.book_id   = oi.book_id
    JOIN authors a      ON a.author_id = b.author_id
    LEFT JOIN reviews rr ON rr.book_id = b.book_id
    $whereDate
    GROUP BY b.book_id
    ORDER BY sold_30d DESC $orderBooksBy3
    LIMIT ?
  ";
  $stPop = $conn->prepare($sqlPop);
  $stPop->bindValue(1, $LIMIT, PDO::PARAM_INT);
  $stPop->execute();
  $popularMonth = $stPop->fetchAll(PDO::FETCH_ASSOC);
}
if (!$popularMonth) $popularMonth = $newArrivals;

/* ------------- ensure each section has exactly $LIMIT items ------------- */
function fillToLimit(array $primary, array $fallback, int $limit): array
{
  $seen = [];
  $out  = [];
  foreach ($primary as $r) {
    if (!isset($seen[$r['book_id']])) {
      $out[] = $r;
      $seen[$r['book_id']] = true;
      if (count($out) >= $limit) return $out;
    }
  }
  foreach ($fallback as $r) {
    if (!isset($seen[$r['book_id']])) {
      $out[] = $r;
      $seen[$r['book_id']] = true;
      if (count($out) >= $limit) return $out;
    }
  }
  return $out;
}

$newArrivals  = fillToLimit($newArrivals,  $newArrivals,  $LIMIT);
$bestSelling  = fillToLimit($bestSelling,  $newArrivals,  $LIMIT);
$popularMonth = fillToLimit($popularMonth, $newArrivals,  $LIMIT);

/* ----------------------------- utilities ---------------------------------- */
function stars_from_manual($rating): string
{
  $n = max(0, min(5, (float)$rating));
  $full = floor($n);
  $half = ($n - $full) >= 0.5 ? 1 : 0;
  return str_repeat('<i class="bi bi-star-fill text-warning"></i>', $full)
    . ($half ? '<i class="bi bi-star-half text-warning"></i>' : '')
    . str_repeat('<i class="bi bi-star text-warning"></i>', 5 - $full - $half);
}
?>

<!-- HERO (unchanged) -->
<section class="bn-hero mb-4">
  <div class="row g-0 align-items-stretch">
    <div class="col-lg-6 left">
      <h1 class="display-5 mb-3">Discover Your Next <span class="text-primary">Favorite</span> Book</h1>
      <p class="lead mb-4">Curated reads across fiction, tech, self-help, and more—delivered with a clean, fast experience.</p>
      <div class="d-flex gap-2">
        <a class="btn btn-primary btn-lg rounded-pill px-4" href="product.php?sort=newest">Shop New Arrivals</a>
        <a class="btn btn-outline-dark btn-lg rounded-pill px-4" href="product.php">Browse All</a>
      </div>
    </div>
    <div class="col-lg-6 p-3 p-lg-4">
      <div class="swiper">
        <div class="swiper-wrapper">
          <div class="swiper-slide"><img class="w-100 rounded-4" src="assets/img/book2.jpg" alt=""></div>
          <div class="swiper-slide"><img class="w-100 rounded-4" src="assets/img/book3.jpg" alt=""></div>
          <div class="swiper-slide"><img class="w-100 rounded-4" src="assets/img/book4.png" alt=""></div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </div>
</section>

<?php if (empty($_SESSION['user_id'])): ?>
  <!-- Welcome banner (unchanged) -->
  <section id="offer" class="card border-0 shadow-sm rounded-4 mb-5">
    <div class="card-body p-4 p-lg-5">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="flex-grow-1">
          <h2 class="h3 fw-bold mb-3">Welcome Offers for New Readers</h2>
          <ul class="list-unstyled mb-0 fs-5">
            <li class="mb-2">🎉 <strong>10% OFF</strong> your first order</li>
            <li class="mb-2">🚚 <strong>Free Shipping</strong> on the first purchase</li>
            <li class="mb-2">💖 Save favorites with a wishlist after you sign up</li>
          </ul>
        </div>
        <div class="text-end">
          <a class="btn btn-dark btn-lg rounded-pill px-4 me-2" href="signup.php">Create Account</a>
          <a class="btn btn-outline-dark btn-lg rounded-pill px-4" href="login.php">Log In</a>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php
/* ------------- reusable grid ------------- */
function renderBookGrid(string $title, array $books, PDO $conn): void
{ ?>
  <section class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <h2 class="h6 fw-bold mb-0"><?= h($title) ?></h2>
      <a class="btn btn-sm btn-outline-secondary rounded-pill" href="product.php">See all</a>
    </div>

    <div class="bn-grid">
      <?php foreach ($books as $bk): ?>
        <div class="bn-card">
          <a href="<?= url('productdetail.php', ['id' => (int)$bk['book_id']]) ?>" class="text-decoration-none text-dark">
            <?php $img = cover_src($bk['image_url'] ?? ''); ?>
            <img src="<?= h($img) ?>" alt="<?= h($bk['title']) ?>" class="bn-cover">

            <div class="bn-body">
              <div class="bn-title text-clamp-2"><?= h($bk['title']) ?></div>
              <div class="bn-author mb-1"><?= h($bk['author_name']) ?></div>

              <div class="bn-rating d-flex align-items-center gap-1">
                <?= stars_from_manual($bk['rating_manual']) ?>
                <span class="text-muted small">(<?= (int)$bk['reviews_manual'] ?>)</span>
              </div>

              <?php
              $disc  = active_book_discount($conn, (int)$bk['book_id']);
              $final = price_after_discount((float)$bk['price'], $disc);
              ?>
              <?php if ($disc): ?>
                <div class="mt-1">
                  <span class="text-muted text-decoration-line-through me-2">
                    $<?= number_format((float)$bk['price'], 2) ?>
                  </span>
                  <span class="fw-bold">$<?= number_format($final, 2) ?></span>
                  <span class="badge bg-danger ms-2"><?= h(discount_badge($disc)) ?></span>
                </div>
              <?php else: ?>
                <div class="fw-bold mt-1">$<?= number_format((float)$bk['price'], 2) ?></div>
              <?php endif; ?>
            </div>
          </a>

          <div class="d-flex gap-2 px-3 pb-3">
            <form method="post" action="cart.php" class="flex-fill m-0">
              <input type="hidden" name="action" value="add">
              <input type="hidden" name="book_id" value="<?= (int)$bk['book_id'] ?>">
              <button class="btn btn-sm btn-primary w-100 rounded-pill">
                <i class="bi bi-cart"></i> Add to Cart
              </button>
            </form>

            <?php
            $inWish = false;
            if (!empty($_SESSION['user_id'])) {
              $wst = $conn->prepare("SELECT 1 FROM wishlists WHERE user_id=? AND book_id=?");
              $wst->execute([$_SESSION['user_id'], $bk['book_id']]);
              $inWish = (bool)$wst->fetchColumn();
            }
            ?>
            <form class="js-wish-form m-0" method="post"
              action="<?= $inWish ? 'wishlist_remove.php' : 'wishlist_add.php' ?>">
              <input type="hidden" name="book_id" value="<?= (int)$bk['book_id'] ?>">
              <button type="submit"
                class="btn btn-sm rounded-circle js-wish-btn
                       <?= $inWish ? 'btn-danger' : 'btn-outline-secondary' ?>"
                title="<?= $inWish ? 'Remove from Wishlist' : 'Add to Wishlist' ?>">
                <i class="bi <?= $inWish ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if (empty($books)): ?>
        <p class="text-muted">No books yet.</p>
      <?php endif; ?>
    </div>
  </section>
<?php } ?>

<!-- Sections -->
<?php
renderBookGrid("New Arrivals",            $newArrivals,  $conn);
renderBookGrid("Best Selling",            $bestSelling,  $conn);
renderBookGrid("Popular Books This Month", $popularMonth, $conn);
?>

<?php require_once __DIR__ . '/partials/footer.php'; ?>