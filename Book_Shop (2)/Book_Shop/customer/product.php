<?php
// product.php — LIST ONLY (filters on the RIGHT)
$pageTitle = "BookNest — Shop";
if (!isset($_SESSION)) {
  session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';
require_once __DIR__ . '/includes/discounts.php';
require_once __DIR__ . '/partials/header.php';

/* ---------- helpers ---------- */
if (!function_exists('fmt_price')) {
  function fmt_price(float $n): string
  {
    return number_format($n, 0, '.', ',');
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
/* same cover normalizer used in index.php */
if (!function_exists('cover_src')) {
  function cover_src(?string $raw): string
  {
    $u = trim((string)$raw);
    if ($u === '') return 'assets/img/book1.png';
    if (stripos($u, 'http://') === 0 || stripos($u, 'https://') === 0) {
      if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') $u = preg_replace('#^http://#i', 'https://', $u);
      return $u;
    }
    if ($u[0] === '/') return $u;
    $u = preg_replace('#^(\./|(\.\./)+)#', '', $u);
    if (stripos($u, 'uploads/') === 0) return '/admin/' . $u;
    if (stripos($u, 'admin/') === 0)   return '/' . ltrim($u, '/');
    return '/' . ltrim($u, '/');
  }
}

/* ----------- filters ----------- */
$q        = trim($_GET['q'] ?? '');
$cat      = (int)($_GET['category'] ?? 0);
$sort     = $_GET['sort'] ?? 'newest';
$page     = max(1, (int)($_GET['page'] ?? 1));
$pageSize = 12;
$offset   = ($page - 1) * $pageSize;

/* ----------- query builder ----------- */
$where = [];
$args  = [];

if ($q !== '') {
  $where[] = "(
      b.title LIKE ?
      OR b.isbn LIKE ?
      OR EXISTS (
        SELECT 1 FROM authors a
        WHERE a.author_id = b.author_id
          AND (a.first_name LIKE ? OR a.last_name LIKE ?)
      )
      OR EXISTS (
        SELECT 1
        FROM book_categories bc
        JOIN categories c ON c.category_id = bc.category_id
        WHERE bc.book_id = b.book_id
          AND c.category_name LIKE ?
      )
  )";
  $args[] = "%$q%";
  $args[] = "%$q%";
  $args[] = "%$q%";
  $args[] = "%$q%";
  $args[] = "%$q%";
}

/* category filter */
if ($cat > 0) {
  $where[] = "EXISTS(
    SELECT 1 FROM book_categories bc
    WHERE bc.book_id = b.book_id AND bc.category_id = ?
  )";
  $args[] = $cat;
}

/* sort */
if ($sort === 'price_asc')      $order = "b.price ASC";
elseif ($sort === 'price_desc') $order = "b.price DESC";
elseif ($sort === 'title')      $order = "b.title ASC";
else                            $order = col_exists($conn, 'books', 'created_at') ? "b.created_at DESC, b.book_id DESC" : "b.book_id DESC";

$sqlWhere = $where ? ("WHERE " . implode(" AND ", $where)) : "";

/* total count */
$cst = $conn->prepare("SELECT COUNT(*) c FROM books b $sqlWhere");
$cst->execute($args);
$total = (int)$cst->fetch()['c'];

/* page of items */
$pst = $conn->prepare("
  SELECT b.book_id, b.title, b.price, b.image_url,
         CONCAT(a.first_name,' ',a.last_name) AS author_name,
         AVG(r.rating) AS avg_rating, COUNT(r.review_id) AS review_count
  FROM books b
  JOIN authors a ON a.author_id = b.author_id
  LEFT JOIN reviews r ON r.book_id = b.book_id
  $sqlWhere
  GROUP BY b.book_id
  ORDER BY $order
  LIMIT $pageSize OFFSET $offset
");
$pst->execute($args);
$items = $pst->fetchAll(PDO::FETCH_ASSOC);

/* categories for sidebar */
$cats = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row g-4">
  <!-- Grid (left) -->
  <section class="col-lg-9 order-lg-1">
    <div class="bn-grid">
      <?php foreach ($items as $bk): ?>
        <?php
        $inWish = false;
        if (!empty($_SESSION['user_id'])) {
          $wst = $conn->prepare("SELECT 1 FROM wishlists WHERE user_id=? AND book_id=?");
          $wst->execute([$_SESSION['user_id'], $bk['book_id']]);
          $inWish = (bool)$wst->fetchColumn();
        }
        $avg = isset($bk['avg_rating']) ? (float)$bk['avg_rating'] : 0.0;
        $avgRounded = round($avg * 2) / 2;

        $disc  = active_book_discount($conn, (int)$bk['book_id']);
        $final = price_after_discount((float)$bk['price'], $disc);

        $cover = cover_src($bk['image_url'] ?? '');
        ?>
        <div class="bn-card position-relative">

          <!-- Wishlist icon -->
          <form class="js-wish-form position-absolute"
            style="top:10px; right:10px; z-index:5;" method="post"
            action="<?= $inWish ? 'wishlist_remove.php' : 'wishlist_add.php' ?>">
            <input type="hidden" name="book_id" value="<?= (int)$bk['book_id'] ?>">
            <button type="submit"
              class="btn btn-sm rounded-circle border bg-white shadow-sm js-wish-btn"
              data-inwish="<?= $inWish ? '1' : '0' ?>"
              title="<?= $inWish ? 'Remove from Wishlist' : 'Add to Wishlist' ?>"
              style="width:10px; height:10px; display:flex; align-items:center; justify-content:center;">
              <i class="bi <?= $inWish ? 'bi-heart-fill text-danger' : 'bi-heart text-secondary' ?> fs-5"></i>
            </button>
          </form>

          <!-- Book card -->
          <a href="<?= url('productdetail.php', ['id' => (int)$bk['book_id']]) ?>" class="text-decoration-none text-dark">
            <img src="<?= h($cover) ?>" alt="<?= h($bk['title']) ?>" class="bn-cover">
            <div class="bn-body">
              <div class="bn-title text-clamp-2"><?= h($bk['title']) ?></div>
              <div class="bn-author mb-1"><?= h($bk['author_name']) ?></div>

              <div class="d-flex align-items-center gap-1 mb-1">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <?php if ($avgRounded >= $i): ?>
                    <i class="bi bi-star-fill text-warning"></i>
                  <?php elseif ($avgRounded >= $i - 0.5): ?>
                    <i class="bi bi-star-half text-warning"></i>
                  <?php else: ?>
                    <i class="bi bi-star text-warning"></i>
                  <?php endif; ?>
                <?php endfor; ?>
                <span class="text-muted small">(<?= (int)$bk['review_count'] ?>)</span>
              </div>

              <?php if ($disc): ?>
                <div class="mt-1">
                  <span class="text-muted text-decoration-line-through me-2">
                    $<?= fmt_price((float)$bk['price']) ?>
                  </span>
                  <span class="fw-bold">$<?= fmt_price($final) ?></span>
                  <span class="badge bg-danger ms-2"><?= h(discount_badge($disc)) ?></span>
                </div>
              <?php else: ?>
                <div class="fw-bold">$<?= fmt_price((float)$bk['price']) ?></div>
              <?php endif; ?>
            </div>
          </a>

          <!-- Actions -->
          <div class="px-3 pb-3 d-grid gap-2">
            <form method="post" action="cart.php" class="m-0">
              <input type="hidden" name="action" value="add">
              <input type="hidden" name="book_id" value="<?= (int)$bk['book_id'] ?>">
              <button class="btn btn-sm btn-primary w-100 rounded-pill">
                <i class="bi bi-cart"></i> Add to Cart
              </button>
            </form>
            <a href="<?= url('productdetail.php', ['id' => (int)$bk['book_id']]) ?>"
              class="btn btn-sm btn-outline-secondary w-100 rounded-pill">View Details</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php
    $totalPages = max(1, (int)ceil($total / $pageSize));
    if ($totalPages > 1):
    ?>
      <nav class="mt-3">
        <ul class="pagination">
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
              <a class="page-link"
                href="<?= url('product.php', ['q' => $q, 'category' => $cat, 'sort' => $sort, 'page' => $p]) ?>">
                <?= $p ?>
              </a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </section>

  <!-- Filters (right) -->
  <aside class="col-lg-3 order-lg-2 ps-lg-3">
    <form method="get" class="card p-3 border-0 shadow-sm rounded-4 sticky-top" style="top: 90px;">
      <div class="mb-2 fw-semibold">Search</div>
      <input class="form-control mb-3" name="q" value="<?= h($q) ?>" placeholder="Title, ISBN, author, category">

      <div class="mb-2 fw-semibold">Category</div>
      <select class="form-select mb-3" name="category">
        <option value="">All</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= (int)$c['category_id'] ?>" <?= $cat === (int)$c['category_id'] ? 'selected' : '' ?>>
            <?= h($c['category_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <div class="mb-2 fw-semibold">Sort</div>
      <select class="form-select mb-3" name="sort">
        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low → High</option>
        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High → Low</option>
        <option value="title" <?= $sort === 'title' ? 'selected' : '' ?>>Title (A–Z)</option>
      </select>

      <div class="d-grid gap-2">
        <button class="btn btn-dark rounded-pill">Apply</button>
        <a class="btn btn-outline-secondary rounded-pill" href="product.php">Reset</a>
      </div>
    </form>
  </aside>
</div>

<!-- JS wishlist toggle (unchanged) -->
<script>
  document.addEventListener('click', async (e) => {
    const form = e.target.closest('.js-wish-form');
    if (!form) return;
    e.preventDefault();
    const btn = form.querySelector('.js-wish-btn');
    const icon = btn.querySelector('i');
    const fd = new FormData(form);
    try {
      const res = await fetch(form.action, {
        method: 'POST',
        body: fd
      });
      if (!res.ok) return;
      const inWish = btn.getAttribute('data-inwish') === '1';
      if (inWish) {
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-outline-secondary');
        icon.classList.remove('bi-heart-fill');
        icon.classList.add('bi-heart');
        btn.setAttribute('data-inwish', '0');
        btn.title = 'Add to Wishlist';
        form.action = 'wishlist_add.php';
      } else {
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-danger');
        icon.classList.remove('bi-heart');
        icon.classList.add('bi-heart-fill');
        btn.setAttribute('data-inwish', '1');
        btn.title = 'Remove from Wishlist';
        form.action = 'wishlist_remove.php';
      }
    } catch (err) {
      console.error(err);
    }
  });
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>