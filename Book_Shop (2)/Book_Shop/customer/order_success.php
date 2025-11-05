<?php
// order_success.php — show REAL order status from orders.order_status (pending/paid/delivered/canceled)
if (!isset($_SESSION)) {
  session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';

// require login
if (empty($_SESSION['user_id'])) {
  header("Location: login.php?return=" . urlencode('order_success.php'));
  exit;
}

$userId  = (int)$_SESSION['user_id'];
$orderId = (int)($_GET['id'] ?? 0);
if ($orderId <= 0) {
  header("Location: product.php");
  exit;
}

/* -------- Load order (and ensure it belongs to user) -------- */
$ost = $conn->prepare("SELECT * FROM orders WHERE order_id = ? LIMIT 1");
$ost->execute([$orderId]);
$order = $ost->fetch(PDO::FETCH_ASSOC);

if (!$order || (isset($order['user_id']) && (int)$order['user_id'] !== $userId)) {
  header("Location: product.php");
  exit;
}

/* -------- Numbers -------- */
$total    = isset($order['total'])        ? (float)$order['total']        : (float)($order['total_amount'] ?? 0);
$subtotal = isset($order['subtotal'])     ? (float)$order['subtotal']     : null;
$shipping = isset($order['shipping_fee']) ? (float)$order['shipping_fee'] : (isset($order['shipping_cost']) ? (float)$order['shipping_cost'] : null);

/* -------- Optional latest payment (if table exists) -------- */
$payment = null;
try {
  $pst = $conn->prepare("
    SELECT payment_method, transaction_id, payment_date, amount
         , CASE WHEN (SELECT COUNT(*) 
                      FROM information_schema.COLUMNS 
                      WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME='payments' 
                        AND COLUMN_NAME='status') > 0
                THEN status ELSE NULL END AS status
    FROM payments 
    WHERE order_id = ? 
    ORDER BY payment_date DESC, payment_id DESC 
    LIMIT 1
  ");
  $pst->execute([$orderId]);
  $payment = $pst->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (Throwable $e) {
  // ignore
}

$rawStatus = strtolower(trim((string)($order['order_status'] ?? '')));
if ($rawStatus === '') {
  $rawStatus = strtolower(trim((string)($order['payment_status'] ?? '')));
}
if ($rawStatus === '') {
  $rawStatus = 'pending';
}
$valid = ['pending', 'paid', 'delivered', 'canceled', 'cancelled'];
if (!in_array($rawStatus, $valid, true)) {
  $rawStatus = 'pending';
}
if ($rawStatus === 'cancelled') $rawStatus = 'canceled'; // normalize

function status_badge_class(string $s): string
{
  // Choose nice Bootstrap-y looks
  switch ($s) {
    case 'paid':
      return 'bg-success-subtle text-success border';
    case 'delivered':
      return 'bg-primary-subtle text-primary border';
    case 'canceled':
      return 'bg-danger-subtle text-danger border';
    case 'pending':
    default:
      return 'bg-warning-subtle text-warning border';
  }
}

$statusLabel = ucfirst($rawStatus);
$statusClass = status_badge_class($rawStatus);

/* -------- Items with book details -------- */
$sqlItems = "
  SELECT oi.*, 
         b.title, b.image_url,
         CONCAT(a.first_name,' ',a.last_name) AS author_name
  FROM order_items oi
  LEFT JOIN books b   ON b.book_id = oi.book_id
  LEFT JOIN authors a ON a.author_id = b.author_id
  WHERE oi.order_id = ?
  ORDER BY b.title
";
$itst = $conn->prepare($sqlItems);
$itst->execute([$orderId]);
$items = $itst->fetchAll(PDO::FETCH_ASSOC);

/* -------- Helpers -------- */
function item_qty(array $r)
{
  return (int)($r['quantity'] ?? ($r['qty'] ?? 1));
}
function item_unit(array $r)
{
  return (float)($r['price_at_purchase'] ?? ($r['unit_price'] ?? ($r['price'] ?? 0)));
}
function item_total(array $r)
{
  if (isset($r['cost']))       return (float)$r['cost'];
  if (isset($r['line_total'])) return (float)$r['line_total'];
  return item_qty($r) * item_unit($r);
}

/* -------- Dates & title -------- */
$placedAt  = $order['order_date'] ?? ($order['created_at'] ?? date('Y-m-d H:i:s'));
$pageTitle = "Order Confirmed • BookNest";

require_once __DIR__ . '/partials/header.php';
?>

<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div>
        <h1 class="h4 fw-bold mb-1">Thank you! 🎉</h1>
        <div class="text-muted">Your order has been placed successfully.</div>
      </div>
      <div class="text-end">
        <div class="small text-muted">Order #</div>
        <div class="fs-5 fw-bold">#<?= (int)$orderId ?></div>
      </div>
    </div>
    <hr>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="small text-muted">Placed on</div>
        <div class="fw-semibold"><?= h($placedAt) ?></div>
      </div>
      <div class="col-md-4">
        <div class="small text-muted">Status</div>
        <span class="badge <?= $statusClass ?>"><?= h($statusLabel) ?></span>
      </div>
      <div class="col-md-4">
        <div class="small text-muted">Total</div>
        <div class="fw-bold">$<?= h(number_format($total, 2)) ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Left: items -->
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-4">
        <h2 class="h6 fw-bold mb-3">Items</h2>
        <ul class="list-unstyled m-0">
          <?php foreach ($items as $it):
            $qty       = item_qty($it);
            $unit      = item_unit($it);
            $totalLine = item_total($it);
          ?>
            <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
              <div class="d-flex align-items-center gap-2">
                <img src="<?= h($it['image_url'] ?: 'assets/img/book1.png') ?>" alt="" width="48" class="rounded">
                <div>
                  <div class="fw-semibold"><?= h($it['title'] ?? 'Book') ?></div>
                  <div class="text-muted small"><?= h($it['author_name'] ?? '') ?></div>
                  <div class="text-muted small">x<?= $qty ?> · $<?= h(number_format($unit, 2)) ?></div>
                </div>
              </div>
              <div class="fw-semibold">$<?= h(number_format($totalLine, 2)) ?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

  <!-- Right: summary & address -->
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm rounded-4 mb-3">
      <div class="card-body p-4">
        <h2 class="h6 fw-bold mb-3">Summary</h2>

        <?php if (!is_null($subtotal)): ?>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Subtotal</span>
            <span>$<?= h(number_format($subtotal, 2)) ?></span>
          </div>
        <?php endif; ?>

        <?php if (!is_null($shipping)): ?>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Shipping</span>
            <span>$<?= h(number_format($shipping, 2)) ?></span>
          </div>
        <?php endif; ?>

        <hr>
        <div class="d-flex justify-content-between fs-6 fw-bold">
          <span>Total</span>
          <span>$<?= h(number_format($total, 2)) ?></span>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-4">
        <h2 class="h6 fw-bold mb-3">Shipping Address</h2>
        <div class="small">
          <?php if (!empty($order['full_name'])): ?>
            <div class="fw-semibold"><?= h($order['full_name']) ?></div>
          <?php endif; ?>
          <?php if (!empty($order['phone'])): ?>
            <div>📞 <?= h($order['phone']) ?></div>
          <?php endif; ?>
          <?php if (!empty($order['street'])): ?>
            <div><?= h($order['street']) ?></div>
          <?php endif; ?>
          <div>
            <?= h($order['city'] ?? '') ?>
            <?php if (!empty($order['postal_code'])): ?> <?= h($order['postal_code']) ?><?php endif; ?>
          </div>
        </div>

        <?php if ($payment): ?>
          <hr>
          <h3 class="h6 fw-bold mb-2">Payment</h3>
          <div class="small">
            <div>Method: <span class="fw-semibold"><?= h($payment['payment_method']) ?></span></div>
            <?php if (!empty($payment['transaction_id'])): ?>
              <div>Txn: <code><?= h($payment['transaction_id']) ?></code></div>
            <?php endif; ?>
            <?php if (!empty($payment['payment_date'])): ?>
              <div>Date: <?= h($payment['payment_date']) ?></div>
            <?php endif; ?>
            <?php if (strtoupper((string)($payment['status'] ?? '')) === 'PAID'): ?>
              <div class="mt-1"><span class="badge bg-success-subtle text-success border">Paid</span></div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="d-flex gap-2 mt-4">
  <a class="btn btn-dark rounded-pill" href="product.php">Continue Shopping</a>
  <a class="btn btn-outline-secondary rounded-pill" href="orders.php">View My Orders</a>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>