<?php
// productdetail.php — Book detail + paid-buyer reviews (tabbed: Reviews / Related)
$pageTitle = "BookNest — Book";
if (!isset($_SESSION)) {
  session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';
require_once __DIR__ . '/includes/discounts.php';

/* ---------- Helpers ---------- */

// pretty price without decimals
if (!function_exists('fmt_price')) {
  function fmt_price(float $n): string
  {
    return number_format($n, 0, '.', ',');
  }
}

/* schema helpers (only if not provided elsewhere) */
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

/* SAME cover normalizer used on index.php + product.php */
if (!function_exists('cover_src')) {
  function cover_src(?string $raw): string
  {
    $u = trim((string)$raw);
    if ($u === '') return 'assets/img/book1.png';

    // full URL
    if (stripos($u, 'http://') === 0 || stripos($u, 'https://') === 0) {
      if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $u = preg_replace('#^http://#i', 'https://', $u);
      }
      return $u;
    }
    // already absolute
    if ($u[0] === '/') return $u;

    // remove ./ or ../ prefixes
    $u = preg_replace('#^(\./|(\.\./)+)#', '', $u);

    // saved from admin as "uploads/…"
    if (stripos($u, 'uploads/') === 0) return '/admin/' . $u;

    // saved as "admin/uploads/…"
    if (stripos($u, 'admin/') === 0) return '/' . ltrim($u, '/');

    // generic relative
    return '/' . ltrim($u, '/');
  }
}

// Has this user purchased (and paid) this book?
function user_has_purchased_book(PDO $conn, int $userId, int $bookId): bool
{
  if (!table_exists($conn, 'orders') || !table_exists($conn, 'order_items')) return false;

  $sql = "SELECT 1
          FROM orders o
          JOIN order_items oi ON o.order_id = oi.order_id
          WHERE o.user_id = ? AND oi.book_id = ?";

  if (col_exists($conn, 'orders', 'payment_status')) {
    $sql .= " AND UPPER(o.payment_status) = 'PAID'";
  } elseif (col_exists($conn, 'orders', 'status')) {
    $sql .= " AND LOWER(o.status) IN ('paid','completed','delivered','shipped')";
  }

  $sql .= " LIMIT 1";
  $st = $conn->prepare($sql);
  $st->execute([$userId, $bookId]);
  return (bool)$st->fetchColumn();
}

/* ---------- Load book id ---------- */
$bookId = (int)($_GET['id'] ?? 0);
if ($bookId <= 0) {
  require_once __DIR__ . '/partials/header.php';
  echo '<p class="text-muted">Book not found.</p>';
  require_once __DIR__ . '/partials/footer.php';
  exit;
}

/* ---------- Handle review submission (paid buyers only) ---------- */
$userId = (int)($_SESSION['user_id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
  $rating  = (int)($_POST['rating'] ?? 0);
  $comment = trim($_POST['comment'] ?? '');

  if ($userId && $rating >= 1 && $rating <= 5 && $comment !== '' && user_has_purchased_book($conn, $userId, $bookId)) {
    $already = $conn->prepare("SELECT review_id FROM reviews WHERE user_id=? AND book_id=? LIMIT 1");
    $already->execute([$userId, $bookId]);
    $rid = (int)($already->fetchColumn() ?: 0);

    if ($rid) {
      $up = $conn->prepare("UPDATE reviews SET rating=?, comment=?, created_at=NOW() WHERE review_id=?");
      $up->execute([$rating, $comment, $rid]);
    } else {
      $ins = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
      $ins->execute([$userId, $bookId, $rating, $comment]);
    }

    header("Location: productdetail.php?id=" . $bookId . "&review=success#reviews");
    exit;
  } else {
    header("Location: productdetail.php?id=" . $bookId . "&review=denied#reviews");
    exit;
  }
}

/* ---------- Fetch book + author + rating aggregate ---------- */
$st = $conn->prepare("
  SELECT b.*, CONCAT(a.first_name,' ',a.last_name) AS author_name,
         AVG(r.rating) AS avg_rating, COUNT(r.review_id) AS review_count
  FROM books b
  JOIN authors a ON a.author_id = b.author_id
  LEFT JOIN reviews r ON r.book_id = b.book_id
  WHERE b.book_id = ?
  GROUP BY b.book_id
");
$st->execute([$bookId]);
$book = $st->fetch(PDO::FETCH_ASSOC);

/* ---------- Categories for this book ---------- */
$cats = [];
if ($book) {
  $ct = $conn->prepare("
    SELECT c.category_name
    FROM book_categories bc
    JOIN categories c ON c.category_id = bc.category_id
    WHERE bc.book_id = ?
    ORDER BY c.category_name
  ");
  $ct->execute([$bookId]);
  $cats = $ct->fetchAll(PDO::FETCH_ASSOC);
}

/* ---------- Existing reviews for this book ---------- */
$reviews = [];
$rst = $conn->prepare("
  SELECT 
    r.rating, r.comment, r.created_at, r.user_id AS r_user_id,
    u.* 
  FROM reviews r
  JOIN users u ON u.user_id = r.user_id
  WHERE r.book_id = ?
  ORDER BY r.created_at DESC
");
$rst->execute([$bookId]);
$reviews = $rst->fetchAll(PDO::FETCH_ASSOC);

/* Build a safe display name */
function reviewer_name(array $u): string
{
  foreach (['full_name', 'user_name', 'name'] as $k) {
    if (!empty($u[$k])) return (string)$u[$k];
  }
  $first = trim((string)($u['first_name'] ?? ''));
  $last  = trim((string)($u['last_name'] ?? ''));
  if ($first !== '' || $last !== '') return trim("$first $last");
  if (!empty($u['email'])) return (string)$u['email'];
  return 'User #' . (int)($u['r_user_id'] ?? ($u['user_id'] ?? 0));
}

/* --------------------------- Related Books --------------------------- */
$catIdRows = $conn->prepare("SELECT bc.category_id FROM book_categories bc WHERE bc.book_id = ?");
$catIdRows->execute([$bookId]);
$catIds = array_map(fn($r) => (int)$r['category_id'], $catIdRows->fetchAll(PDO::FETCH_ASSOC));

$related = [];
if (!empty($catIds)) {
  $in = implode(',', array_fill(0, count($catIds), '?'));
  $params = array_merge([$bookId], $catIds);

  $sql = "
    SELECT b.book_id, b.title, b.price, b.image_url,
           CONCAT(a.first_name,' ',a.last_name) AS author_name,
           AVG(r.rating) AS avg_rating, COUNT(r.review_id) AS review_count,
           COUNT(DISTINCT bc2.category_id) AS overlap
    FROM books b
    JOIN authors a ON a.author_id = b.author_id
    LEFT JOIN reviews r ON r.book_id = b.book_id
    JOIN book_categories bc2 ON bc2.book_id = b.book_id
    WHERE b.book_id <> ?
      AND bc2.category_id IN ($in)
    GROUP BY b.book_id
    ORDER BY overlap DESC, b.created_at DESC
    LIMIT 10
  ";
  $stRel = $conn->prepare($sql);
  $stRel->execute($params);
  $related = $stRel->fetchAll(PDO::FETCH_ASSOC);
}
if (!$related && !empty($book['author_id'])) {
  $stRel = $conn->prepare("
    SELECT b.book_id, b.title, b.price, b.image_url,
           CONCAT(a.first_name,' ',a.last_name) AS author_name,
           AVG(r.rating) AS avg_rating, COUNT(r.review_id) AS review_count
    FROM books b
    JOIN authors a ON a.author_id = b.author_id
    LEFT JOIN reviews r ON r.book_id = b.book_id
    WHERE b.book_id <> ? AND b.author_id = ?
    GROUP BY b.book_id
    ORDER BY b.created_at DESC
    LIMIT 10
  ");
  $stRel->execute([$bookId, (int)$book['author_id']]);
  $related = $stRel->fetchAll(PDO::FETCH_ASSOC);
}

require_once __DIR__ . '/partials/header.php';
?>

<?php if (!$book): ?>
  <p class="text-muted">Book not found.</p>
<?php else: ?>
  <div class="row g-4 g-lg-5 align-items-start">
    <!-- Cover -->
    <div class="col-lg-5">
      <div class="product-cover-wrap">
        <img
          src="<?= h(cover_src($book['image_url'] ?? '')) ?>"
          alt="<?= h($book['title']) ?>"
          class="product-cover">
      </div>
    </div>

    <!-- Details -->
    <div class="col-lg-7">
      <h1 class="display-6 fw-bold mb-1"><?= h($book['title']) ?></h1>
      <div class="text-muted mb-2"><?= h($book['author_name']) ?></div>

      <?php if ($cats): ?>
        <div class="mb-2">
          <?php foreach ($cats as $c): ?>
            <span class="badge bg-light text-dark me-1"><?= h($c['category_name']) ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Rating stars -->
      <?php
      $avg = isset($book['avg_rating']) ? (float)$book['avg_rating'] : 0.0;
      $avgRounded = round($avg * 2) / 2;
      $count = (int)($book['review_count'] ?? 0);
      ?>
      <div class="d-flex align-items-center gap-2 mb-2">
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <?php if ($avgRounded >= $i): ?>
            <i class="bi bi-star-fill text-warning"></i>
          <?php elseif ($avgRounded >= $i - 0.5): ?>
            <i class="bi bi-star-half text-warning"></i>
          <?php else: ?>
            <i class="bi bi-star text-warning"></i>
          <?php endif; ?>
        <?php endfor; ?>
        <a href="#reviews" class="text-decoration-none small">(<?= $count ?> reviews)</a>

        <?php if ($userId && user_has_purchased_book($conn, $userId, (int)$book['book_id'])): ?>
          <a href="#reviews" class="btn btn-sm btn-outline-primary ms-2">Write a review</a>
        <?php endif; ?>
      </div>

      <!-- Price -->
      <?php
      $disc  = active_book_discount($conn, (int)$book['book_id']);
      $final = price_after_discount((float)$book['price'], $disc);
      ?>
      <div class="fs-1 fw-bold mb-3">
        <?php if ($disc): ?>
          <span class="text-muted text-decoration-line-through me-2">
            $<?= fmt_price((float)$book['price']) ?>
          </span>
          <span>$<?= fmt_price($final) ?></span>
          <span class="badge bg-danger align-middle ms-2"><?= h(discount_badge($disc)) ?></span>
        <?php else: ?>
          <span>$<?= fmt_price((float)$book['price']) ?></span>
        <?php endif; ?>
      </div>

      <!-- Specs -->
      <dl class="row small mb-3">
        <?php if (!empty($book['isbn'])): ?>
          <dt class="col-sm-2 text-muted">ISBN</dt>
          <dd class="col-sm-10"><?= h($book['isbn']) ?></dd>
        <?php endif; ?>
      </dl>

      <!-- Description -->
      <?php if (!empty($book['description'])): ?>
        <p class="mb-4"><?= nl2br(h($book['description'])) ?></p>
      <?php endif; ?>

      <!-- Add to cart -->
      <?php $stock = book_stock($conn, (int)$book['book_id']); ?>
      <?php if ($stock <= 0): ?>
        <div class="alert alert-warning mt-3 mb-0">Out of stock</div>
      <?php else: ?>
        <form method="post" action="cart.php" class="d-flex flex-wrap gap-2 align-items-center">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="book_id" value="<?= (int)$book['book_id'] ?>">
          <input type="number" name="qty" min="1" max="<?= (int)$stock ?>" value="1"
            class="form-control" style="max-width:120px">
          <button class="btn btn-primary rounded-pill px-4">Add to Cart</button>
        </form>
        <div class="small text-muted mt-1">In stock: <?= (int)$stock ?></div>
      <?php endif; ?>


    </div>
  </div>

  <!-- Tabs: Reviews / Related -->
  <hr class="my-5">
  <ul class="nav nav-tabs" id="pdTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link active" id="reviews-tab" data-bs-toggle="tab" href="#tab-reviews" role="tab" aria-controls="tab-reviews" aria-selected="true">
        Reviews (<?= $count ?>)
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="related-tab" data-bs-toggle="tab" href="#tab-related" role="tab" aria-controls="tab-related" aria-selected="false">
        Related Books
      </a>
    </li>
  </ul>

  <div class="tab-content pt-3">
    <!-- Reviews pane -->
    <div class="tab-pane fade show active" id="tab-reviews" role="tabpanel" aria-labelledby="reviews-tab">
      <div class="row g-4">
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 fw-bold mb-0">Customer Reviews</h2>
                <?php if (isset($_GET['review']) && $_GET['review'] === 'success'): ?>
                  <span class="badge bg-success-subtle text-success border">Thanks for your review!</span>
                <?php elseif (isset($_GET['review']) && $_GET['review'] === 'denied'): ?>
                  <span class="badge bg-warning-subtle text-warning border">You can only review purchased items.</span>
                <?php endif; ?>
              </div>

              <?php if ($reviews): ?>
                <?php
                $maxShow = 6;
                $extra   = max(0, count($reviews) - $maxShow);
                ?>
                <?php foreach (array_slice($reviews, 0, $maxShow) as $rv): ?>
                  <div class="mb-3 border-bottom pb-2">
                    <div class="fw-semibold"><?= h(reviewer_name($rv)) ?></div>
                    <div class="text-warning small">
                      <?= str_repeat("★", (int)$rv['rating']) . str_repeat("☆", 5 - (int)$rv['rating']) ?>
                    </div>
                    <div><?= nl2br(h($rv['comment'])) ?></div>
                    <div class="text-muted small"><?= h($rv['created_at']) ?></div>
                  </div>
                <?php endforeach; ?>

                <?php if ($extra > 0): ?>
                  <div class="collapse" id="moreReviews">
                    <?php foreach (array_slice($reviews, $maxShow) as $rv): ?>
                      <div class="mb-3 border-bottom pb-2">
                        <div class="fw-semibold"><?= h(reviewer_name($rv)) ?></div>
                        <div class="text-warning small">
                          <?= str_repeat("★", (int)$rv['rating']) . str_repeat("☆", 5 - (int)$rv['rating']) ?>
                        </div>
                        <div><?= nl2br(h($rv['comment'])) ?></div>
                        <div class="text-muted small"><?= h($rv['created_at']) ?></div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                  <button class="btn btn-sm btn-outline-secondary rounded-pill" type="button"
                    data-bs-toggle="collapse" data-bs-target="#moreReviews"
                    aria-expanded="false" aria-controls="moreReviews">
                    Show <?= $extra ?> more review<?= $extra > 1 ? 's' : '' ?>
                  </button>
                <?php endif; ?>
              <?php else: ?>
                <div class="text-muted">No reviews yet.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Review form (visible only to paid buyers) -->
        <div class="col-lg-5">
          <?php if ($userId && user_has_purchased_book($conn, $userId, (int)$book['book_id'])): ?>
            <div class="card border-0 shadow-sm rounded-4">
              <div class="card-body p-4">
                <h3 class="h6 fw-bold mb-3">Write a Review</h3>
                <form method="post" action="#reviews">
                  <div class="mb-3">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select" required>
                      <option value="">Choose...</option>
                      <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?= $i ?>"><?= $i ?> ★</option>
                      <?php endfor; ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Comment</label>
                    <textarea name="comment" class="form-control" rows="4" placeholder="Share what you liked or disliked..." required></textarea>
                  </div>
                  <button type="submit" name="submit_review" class="btn btn-primary rounded-pill">Submit Review</button>
                </form>
                <div class="text-muted small mt-2">You can update your review later—submitting again will overwrite it.</div>
              </div>
            </div>
          <?php else: ?>
            <div class="alert alert-light border small">
              Only customers who purchased this title can write a review.
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Related pane -->
    <div class="tab-pane fade" id="tab-related" role="tabpanel" aria-labelledby="related-tab">
      <?php if ($related): ?>
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h2 class="h6 fw-bold mb-0">Related Books</h2>
          <a class="btn btn-sm btn-outline-secondary rounded-pill" href="product.php">Browse all</a>
        </div>

        <div class="bn-grid">
          <?php foreach ($related as $rb): ?>
            <?php
            $discR  = active_book_discount($conn, (int)$rb['book_id']);
            $finalR = price_after_discount((float)$rb['price'], $discR);
            $avgR   = isset($rb['avg_rating']) ? (float)$rb['avg_rating'] : 0.0;
            $avgRoundedR = round($avgR * 2) / 2;
            ?>
            <div class="bn-card">
              <a href="<?= url('productdetail.php', ['id' => (int)$rb['book_id']]) ?>" class="text-decoration-none text-dark">
                <img src="<?= h(cover_src($rb['image_url'] ?? '')) ?>" alt="<?= h($rb['title']) ?>" class="bn-cover">
                <div class="bn-body">
                  <div class="bn-title text-clamp-2"><?= h($rb['title']) ?></div>
                  <div class="bn-author mb-1"><?= h($rb['author_name']) ?></div>
                  <div class="d-flex align-items-center gap-1 mb-1">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <?php if ($avgRoundedR >= $i): ?>
                        <i class="bi bi-star-fill text-warning"></i>
                      <?php elseif ($avgRoundedR >= $i - 0.5): ?>
                        <i class="bi bi-star-half text-warning"></i>
                      <?php else: ?>
                        <i class="bi bi-star text-warning"></i>
                      <?php endif; ?>
                    <?php endfor; ?>
                  </div>

                  <?php if ($discR): ?>
                    <div class="mt-1">
                      <span class="text-muted text-decoration-line-through me-2">
                        $<?= fmt_price((float)$rb['price']) ?>
                      </span>
                      <span class="fw-bold">$<?= fmt_price($finalR) ?></span>
                      <span class="badge bg-danger ms-2"><?= h(discount_badge($discR)) ?></span>
                    </div>
                  <?php else: ?>
                    <div class="fw-bold">$<?= fmt_price((float)$rb['price']) ?></div>
                  <?php endif; ?>
                </div>
              </a>

              <div class="px-3 pb-3 d-grid gap-2">
                <form method="post" action="cart.php" class="m-0">
                  <input type="hidden" name="action" value="add">
                  <input type="hidden" name="book_id" value="<?= (int)$rb['book_id'] ?>">
                  <button class="btn btn-sm btn-primary w-100 rounded-pill">Add to Cart</button>
                </form>
                <a href="<?= url('productdetail.php', ['id' => (int)$rb['book_id']]) ?>"
                  class="btn btn-sm btn-outline-secondary w-100 rounded-pill">View Details</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="alert alert-light border">No related books found.</div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<script>
  // Activate the right tab if the URL includes #reviews or #related (or after submitting a review)
  document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const hash = (window.location.hash || '').toLowerCase();

    function showTab(id) {
      const trigger = document.querySelector(`a[href="#${id}"]`);
      if (trigger && window.bootstrap) {
        new bootstrap.Tab(trigger).show();
        document.getElementById(id)?.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    }

    if (params.has('review') || hash.includes('reviews')) showTab('tab-reviews');
    if (hash.includes('related')) showTab('tab-related');
  });
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>