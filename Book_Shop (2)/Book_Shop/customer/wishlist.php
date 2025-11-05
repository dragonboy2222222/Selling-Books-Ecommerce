<?php
// wishlist.php — shows the user's saved books
if (!isset($_SESSION)) { session_start(); }
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';

if (empty($_SESSION['user_id'])) {
  $ret = urlencode('wishlist.php');
  header("Location: login.php?return={$ret}");
  exit;
}

$userId = (int)$_SESSION['user_id'];

// Pull wishlist + avg rating
$st = $conn->prepare("
  SELECT 
    b.book_id,
    b.title,
    b.price,
    b.image_url,
    CONCAT(a.first_name,' ',a.last_name) AS author_name,
    AVG(r.rating)      AS avg_rating,
    COUNT(r.review_id) AS review_count
  FROM wishlists w
  JOIN books b   ON b.book_id = w.book_id
  JOIN authors a ON a.author_id = b.author_id
  LEFT JOIN reviews r ON r.book_id = b.book_id
  WHERE w.user_id = ?
  GROUP BY b.book_id
  ORDER BY b.title ASC
");
$st->execute([$userId]);
$items = $st->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "My Wishlist • BookNest";
require_once __DIR__ . '/partials/header.php';
?>

<h1 class="h4 fw-bold mb-3">My Wishlist</h1>

<?php if (!$items): ?>
  <div class="alert alert-info">Your wishlist is empty.</div>
  <a href="product.php" class="btn btn-primary rounded-pill">Browse Books</a>
<?php else: ?>
  <div class="bn-grid">
    <?php foreach ($items as $bk): ?>
      <div class="bn-card">
        <a href="<?= url('productdetail.php', ['id' => (int)$bk['book_id']]) ?>" class="text-decoration-none text-dark">
          <img
            src="<?= h($bk['image_url'] ?: 'assets/img/book1.png') ?>"
            class="bn-cover"
            alt="<?= h($bk['title']) ?>"
          >
          <div class="bn-body">
            <div class="bn-title text-clamp-2"><?= h($bk['title']) ?></div>
            <div class="bn-author"><?= h($bk['author_name']) ?></div>

            <?php
              $avg = $bk['avg_rating'] !== null ? (float)$bk['avg_rating'] : 0.0;
              $avgRounded = round($avg * 2) / 2; // nearest .5
            ?>
            <div class="bn-rating text-warning d-flex align-items-center gap-1 mb-1">
              <?php for ($i=1; $i<=5; $i++): ?>
                <?php if ($avgRounded >= $i): ?>
                  <i class="bi bi-star-fill"></i>
                <?php elseif ($avgRounded >= $i - 0.5): ?>
                  <i class="bi bi-star-half"></i>
                <?php else: ?>
                  <i class="bi bi-star"></i>
                <?php endif; ?>
              <?php endfor; ?>
            </div>

            <div class="bn-price">$<?= h(number_format((float)$bk['price'], 2)) ?></div>
          </div>
        </a>

        <div class="d-flex gap-2 px-3 pb-3">
          <!-- Add to Cart -->
          <form method="post" action="cart.php" class="flex-fill">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="book_id" value="<?= (int)$bk['book_id'] ?>">
            <button class="btn btn-sm btn-primary w-100 rounded-pill">
              <i class="bi bi-cart"></i> Add to Cart
            </button>
          </form>

          <!-- Remove from Wishlist -->
          <form method="post" action="wishlist_remove.php">
            <input type="hidden" name="book_id" value="<?= (int)$bk['book_id'] ?>">
            <button class="btn btn-sm btn-outline-danger rounded-pill" title="Remove from wishlist">
              <i class="bi bi-trash"></i>
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
