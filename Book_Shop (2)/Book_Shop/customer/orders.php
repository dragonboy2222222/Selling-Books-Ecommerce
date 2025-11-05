<?php
// orders.php — Customer order history (dynamic effective_status with payment overrides)
if (!isset($_SESSION)) {
  session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';

// Require login
if (empty($_SESSION['user_id'])) {
  header("Location: login.php?return=" . urlencode('orders.php'));
  exit;
}

$userId = (int)$_SESSION['user_id'];

/* ----------------- Utilities ----------------- */
function find_orders_date_col(PDO $conn): ?string
{
  foreach (['order_date', 'created_at', 'created', 'placed_at', 'purchase_date', 'date', 'datetime', 'ordered_at'] as $c) {
    if (col_exists($conn, 'orders', $c)) return $c;
  }
  return null;
}

function order_total(array $o): float
{
  if (isset($o['total']))        return (float)$o['total'];
  if (isset($o['total_amount'])) return (float)$o['total_amount'];
  $subtotal = isset($o['subtotal']) ? (float)$o['subtotal'] : 0.0;
  $ship = 0.0;
  if (isset($o['shipping_fee']))     $ship = (float)$o['shipping_fee'];
  elseif (isset($o['shipping_cost'])) $ship = (float)$o['shipping_cost'];
  return $subtotal + $ship;
}

/** Map a status string to label + badge class */
function badge_from_status(string $s): array
{
  $s = strtolower(trim($s));
  if ($s === 'cancelled') $s = 'canceled';
  switch ($s) {
    case 'paid':
      return ['Paid',        'bg-success'];
    case 'delivered':
      return ['Delivered',   'bg-primary'];
    case 'canceled':
      return ['Canceled',    'bg-danger'];
    case 'unpaid':
      return ['Unpaid',      'bg-secondary'];
    case 'failed':
      return ['Failed',      'bg-danger'];
    case 'refunded':
      return ['Refunded',    'bg-info text-dark'];
    case 'processing':
    case 'confirmed':
    case 'in_progress':
      return ['Processing', 'bg-warning text-dark'];
    case 'pending':
    default:
      return ['Pending',     'bg-secondary'];
  }
}

/* ----------------- Build a column-safe effective_status ----------------- */
/*
   Priority rule:
   1) If payment_status exists and is NOT 'PAID' -> use that (unpaid/failed/refunded).
   2) Else, if order_status exists -> use order_status.
   3) Else, if payment_status='PAID' -> 'paid'
   4) Else -> 'pending'
*/
$hasOrder   = col_exists($conn, 'orders', 'order_status');
$hasPayment = col_exists($conn, 'orders', 'payment_status');
$hasStatus  = col_exists($conn, 'orders', 'status'); // rare legacy

if ($hasOrder || $hasPayment || $hasStatus) {
  $cases = [];

  if ($hasPayment) {
    // If payment is present and not PAID, it overrides
    $cases[] = "WHEN o.payment_status IS NOT NULL AND UPPER(o.payment_status) <> 'PAID' THEN LOWER(o.payment_status)";
  }

  if ($hasOrder) {
    // Otherwise use order_status if present
    $cases[] = "WHEN o.order_status IS NOT NULL AND o.order_status <> '' THEN LOWER(o.order_status)";
  } elseif ($hasStatus) {
    // Legacy fallback if there is a generic 'status' column
    $cases[] = "WHEN o.status IS NOT NULL AND o.status <> '' THEN LOWER(o.status)";
  }

  if ($hasPayment) {
    // If still undecided and payment is PAID, call it paid
    $cases[] = "WHEN UPPER(o.payment_status) = 'PAID' THEN 'paid'";
  }

  // Final default
  $cases[] = "ELSE 'pending'";

  $statusExpr = "CASE " . implode(' ', $cases) . " END AS effective_status";
} else {
  // No recognizable columns — always pending
  $statusExpr = "'pending' AS effective_status";
}

/* ----------------- Fetch orders ----------------- */
$dateCol = find_orders_date_col($conn);

$sql = "
  SELECT
    o.*,
    $statusExpr
  FROM orders o
  WHERE o.user_id = ?
";
$sql .= $dateCol ? " ORDER BY o.`$dateCol` DESC, o.order_id DESC"
  : " ORDER BY o.order_id DESC";

$st = $conn->prepare($sql);
$st->execute([$userId]);
$orders = $st->fetchAll(PDO::FETCH_ASSOC);

/* ----------------- Fetch items per order ----------------- */
$itemsByOrder = [];
if ($orders) {
  $orderIds = array_map(fn($o) => (int)$o['order_id'], $orders);
  $in = implode(',', array_fill(0, count($orderIds), '?'));

  $it = $conn->prepare("
    SELECT oi.order_id, oi.book_id, b.title, oi.quantity, oi.price_at_purchase
    FROM order_items oi
    JOIN books b ON b.book_id = oi.book_id
    WHERE oi.order_id IN ($in)
    ORDER BY oi.order_id, b.title
  ");
  $it->execute($orderIds);

  while ($row = $it->fetch(PDO::FETCH_ASSOC)) {
    $oid = (int)$row['order_id'];
    $itemsByOrder[$oid][] = [
      'book_id'           => (int)$row['book_id'],
      'title'             => $row['title'],
      'quantity'          => (int)$row['quantity'],
      'price_at_purchase' => (float)$row['price_at_purchase'],
    ];
  }
}

$pageTitle = "My Orders • BookNest";
require_once __DIR__ . '/partials/header.php';
?>

<h1 class="h4 fw-bold mb-3">My Orders</h1>

<?php if (!$orders): ?>
  <div class="alert alert-info">You have no orders yet.</div>
  <a href="product.php" class="btn btn-primary rounded-pill">Shop Now</a>
<?php else: ?>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Products</th>
          <th><?= h($dateCol ? ucwords(str_replace('_', ' ', $dateCol)) : 'Date') ?></th>
          <th>Status</th>
          <th class="text-end">Total</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <?php
          $oid     = (int)$o['order_id'];
          $items   = $itemsByOrder[$oid] ?? [];
          $titles  = $items ? implode(', ', array_map(fn($x) => $x['title'], $items)) : 'No items';
          $dateVal = $dateCol && isset($o[$dateCol]) ? $o[$dateCol] : '';
          [$statusLabel, $badgeClass] = badge_from_status((string)($o['effective_status'] ?? 'pending'));
          $canReview = order_allows_reviews($o); // from helpers.php

          // ID for the collapsible details row
          $collapseId = "reviews-{$oid}";
          ?>
          <!-- main order row -->
          <tr>
            <td><?= $oid ?></td>
            <td style="max-width:520px;">
              <div class="text-truncate" title="<?= h($titles) ?>"><?= h($titles) ?></div>
            </td>
            <td><?= h($dateVal) ?></td>
            <td><span class="badge <?= h($badgeClass) ?>"><?= h($statusLabel) ?></span></td>
            <td class="text-end">$<?= number_format(order_total($o), 2) ?></td>
            <td class="text-nowrap">
              <a href="order_success.php?id=<?= $oid ?>" class="btn btn-sm btn-outline-primary rounded-pill me-1">View</a>

              <?php if ($items && $canReview): ?>
                <button
                  class="btn btn-sm btn-outline-secondary rounded-pill"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#<?= $collapseId ?>"
                  aria-expanded="false"
                  aria-controls="<?= $collapseId ?>">
                  Review items
                </button>
              <?php else: ?>
                <span class="text-muted small">Reviews available later</span>
              <?php endif; ?>
            </td>
          </tr>

          <!-- collapsible under-row with the book list -->
          <?php if ($items && $canReview): ?>
            <tr class="collapse" id="<?= $collapseId ?>">
              <!-- colspan must equal your table column count (here: 6) -->
              <td colspan="6" class="bg-body-tertiary">
                <ul class="list-group list-group-flush">
                  <?php foreach ($items as $it): ?>
                    <li class="list-group-item d-flex align-items-center justify-content-between">
                      <div class="me-3 text-truncate">
                        <?= h($it['title']) ?>
                        <?php if (!empty($it['quantity'])): ?>
                          <span class="text-muted"> · x<?= (int)$it['quantity'] ?></span>
                        <?php endif; ?>
                      </div>
                      <a
                        href="productdetail.php?id=<?= (int)$it['book_id'] ?>#reviews"
                        class="btn btn-sm btn-outline-primary rounded-pill">
                        Review
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </td>
            </tr>
          <?php endif; ?>

        <?php endforeach; ?>
      </tbody>

    </table>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/partials/footer.php'; ?>