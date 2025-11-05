<?php
// checkout.php — DB-cart + discounts, robust to schema differences
if (!isset($_SESSION)) {
  session_start();
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';
require_once __DIR__ . '/includes/discounts.php';

if (empty($_SESSION['user_id'])) {
  header('Location: login.php?return=' . urlencode('checkout.php'));
  exit;
}
$userId = (int)$_SESSION['user_id'];

/* ---------- Helpers ---------- */
function shipping_fee_for(string $method): float
{
  return $method === 'express' ? 9.99 : 4.99;
}

/* ---------- Load cart (DB for logged-in, fallback to session) ---------- */
if (function_exists('cart_merge_session_into_db')) {
  cart_merge_session_into_db($conn); // merge guest cart after login
}

$lines   = [];  // each: book_id, title, author_name, qty, unit_base, unit_final, line_total
$subtotal_before_order = 0.0;

// Prefer DB cart if table exists; else fallback to session cart
if (table_exists($conn, 'cart_items') && function_exists('cart_fetch_items')) {
  list($rawItems, $ignored) = cart_fetch_items($conn);

  // fallback to session items if DB cart empty
  if (!$rawItems && !empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    if ($ids) {
      $in  = implode(',', array_fill(0, count($ids), '?'));
      $st  = $conn->prepare("
        SELECT b.book_id, b.title, b.price, CONCAT(a.first_name,' ',a.last_name) AS author_name
        FROM books b JOIN authors a ON a.author_id=b.author_id
        WHERE b.book_id IN ($in) ORDER BY b.title
      ");
      $st->execute($ids);
      while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
        $rawItems[] = [
          'book_id'     => (int)$r['book_id'],
          'title'       => $r['title'],
          'author_name' => $r['author_name'],
          'price'       => (float)$r['price'],
          'qty'         => (int)($_SESSION['cart'][$r['book_id']] ?? 1),
        ];
      }
    }
  }
} else {
  $rawItems = [];
  $cart = $_SESSION['cart'] ?? [];
  if ($cart) {
    $ids = array_keys($cart);
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $st  = $conn->prepare("
      SELECT b.book_id, b.title, b.price, CONCAT(a.first_name,' ',a.last_name) AS author_name
      FROM books b JOIN authors a ON a.author_id=b.author_id
      WHERE b.book_id IN ($in) ORDER BY b.title
    ");
    $st->execute($ids);
    while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
      $rawItems[] = [
        'book_id'     => (int)$r['book_id'],
        'title'       => $r['title'],
        'author_name' => $r['author_name'],
        'price'       => (float)$r['price'],
        'qty'         => (int)($cart[$r['book_id']] ?? 1),
      ];
    }
  }
}

if (!$rawItems) {
  header('Location: product.php');
  exit;
}

/* ---------- Per-book discounts + lines ---------- */
foreach ($rawItems as $r) {
  $qty  = max(1, (int)$r['qty']);
  $base = (float)$r['price'];

  $disc = active_book_discount($conn, (int)$r['book_id']);
  $unit = price_after_discount($base, $disc);
  $line = round($unit * $qty, 2);

  $lines[] = [
    'book_id'     => (int)$r['book_id'],
    'title'       => $r['title'],
    'author_name' => $r['author_name'] ?? '',
    'qty'         => $qty,
    'unit_base'   => $base,
    'unit_final'  => $unit,
    'line_total'  => $line,
  ];
  $subtotal_before_order += $line;
}

/* ---------- Order-level discount (coupon / auto-welcome) ---------- */
$delivery_choice = $_POST['delivery_method'] ?? 'standard';
$ship_fee        = shipping_fee_for($delivery_choice);

$applied_discount = null;
$discount_value   = 0.0;

$code = $_SESSION['coupon_code'] ?? '';
if ($code !== '') {
  $applied_discount = find_active_discount_by_code($conn, $code, $subtotal_before_order);
}
if (!$applied_discount) {
  $auto = get_auto_first_purchase_discount($conn);
  if ($auto && is_first_paid_order($conn, $userId)) {
    if (empty($auto['min_subtotal']) || $subtotal_before_order >= (float)$auto['min_subtotal']) {
      $applied_discount = $auto;
    }
  }
}
if ($applied_discount) {
  list($sub_after, $ship_after, $discount_value) =
    apply_order_level_discount($subtotal_before_order, $ship_fee, $applied_discount);
  $subtotal_before_order = $sub_after;
  $ship_fee              = $ship_after;
}

$preview_total = round($subtotal_before_order + $ship_fee, 2);

/* ---------- Handle submit ---------- */
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullName  = trim($_POST['full_name'] ?? '');
  $phone     = trim($_POST['phone'] ?? '');
  $city      = trim($_POST['city'] ?? '');
  $street    = trim($_POST['street'] ?? '');
  $postal    = trim($_POST['postal_code'] ?? '');
  $payMethod = trim($_POST['payment_method'] ?? 'COD');
  $delivery_choice = ($_POST['delivery_method'] ?? 'standard') === 'express' ? 'express' : 'standard';

  if ($fullName === '') $errors[] = 'Full name is required.';
  if ($phone === '')    $errors[] = 'Phone is required.';
  if ($city === '')     $errors[] = 'City is required.';
  if ($street === '')   $errors[] = 'Street/Address is required.';

  $ship_fee = shipping_fee_for($delivery_choice);
  $commit_subtotal = array_reduce($lines, fn($c, $x) => $c + $x['line_total'], 0.0);
  $appliedDiscountId = null;
  $discount_value_commit = 0.0;

  if ($applied_discount) {
    list($sub_after2, $ship_after2, $discount_value_commit) =
      apply_order_level_discount($commit_subtotal, $ship_fee, $applied_discount);
    $commit_subtotal   = $sub_after2;
    $ship_fee          = $ship_after2;
    $appliedDiscountId = (int)$applied_discount['discount_id'];
  }
  $total_commit = round($commit_subtotal + $ship_fee, 2);

  if (!$errors) {
    try {
      $conn->beginTransaction();

      /* ---- orders insert (auto-detect columns) ---- */
      $orderCols = [];
      $baseCols = [
        'user_id'     => $userId,
        'full_name'   => $fullName,
        'phone'       => $phone,
        'city'        => $city,
        'street'      => $street,
        'postal_code' => $postal,
        'order_date'  => date('Y-m-d H:i:s'),
      ];
      foreach ($baseCols as $c => $v) {
        if (col_exists($conn, 'orders', $c)) {
          $orderCols[$c] = $v;
        }
      }

      // totals
      if (col_exists($conn, 'orders', 'subtotal'))      $orderCols['subtotal']      = $commit_subtotal;
      if (col_exists($conn, 'orders', 'shipping_fee'))  $orderCols['shipping_fee']  = $ship_fee;
      if (col_exists($conn, 'orders', 'shipping_cost')) $orderCols['shipping_cost'] = $ship_fee;
      if (col_exists($conn, 'orders', 'total'))         $orderCols['total']         = $total_commit;
      if (!isset($orderCols['total']) && col_exists($conn, 'orders', 'total_amount')) {
        $orderCols['total_amount'] = $total_commit;
      }

      // delivery / payment metadata if present
      if (col_exists($conn, 'orders', 'shipping_method_id') && isset($_POST['delivery_method_id'])) {
        $orderCols['shipping_method_id'] = (int)$_POST['delivery_method_id'];
      }
      if (col_exists($conn, 'orders', 'payment_method')) {
        $orderCols['payment_method'] = $payMethod;
      }

      // link applied discount if present
      if ($appliedDiscountId && col_exists($conn, 'orders', 'discount_id')) {
        $orderCols['discount_id'] = $appliedDiscountId;
      }

      // >>> Mark as paid immediately (no matter the method)
      if (col_exists($conn, 'orders', 'payment_status')) $orderCols['payment_status'] = 'PAID';
      if (col_exists($conn, 'orders', 'status'))         $orderCols['status']         = 'paid';

      if (!$orderCols) throw new RuntimeException('Orders table is missing required columns.');

      $sql = "INSERT INTO orders (" . implode(',', array_keys($orderCols)) . ")
              VALUES (" . implode(',', array_fill(0, count($orderCols), '?')) . ")";
      $ost = $conn->prepare($sql);
      $ost->execute(array_values($orderCols));
      $orderId = (int)$conn->lastInsertId();

      // Double-ensure paid (defensive)
      if (col_exists($conn, 'orders', 'payment_status')) {
        $conn->prepare("UPDATE orders SET payment_status='PAID' WHERE order_id=?")->execute([$orderId]);
      }
      if (col_exists($conn, 'orders', 'status')) {
        $conn->prepare("UPDATE orders SET status='paid' WHERE order_id=?")->execute([$orderId]);
      }

      /* ---- order_items (auto-map to your actual column names) ---- */
      // required: order_id, book_id, quantity-like, unit_price-like, line_total-like
      $qtyCol   = col_exists($conn, 'order_items', 'quantity')         ? 'quantity'
        : (col_exists($conn, 'order_items', 'qty')             ? 'qty' : null);
      $unitCol  = col_exists($conn, 'order_items', 'price_at_purchase') ? 'price_at_purchase'
        : (col_exists($conn, 'order_items', 'unit_price')      ? 'unit_price'
          : (col_exists($conn, 'order_items', 'price')           ? 'price' : null));
      $totalCol = col_exists($conn, 'order_items', 'line_total')       ? 'line_total'
        : (col_exists($conn, 'order_items', 'total')           ? 'total'
          : (col_exists($conn, 'order_items', 'cost')            ? 'cost' : null));

      if (!$qtyCol || !$unitCol || !$totalCol) {
        throw new RuntimeException('order_items needs quantity/price/total columns (quantity, price_at_purchase|unit_price|price, line_total|total|cost).');
      }

      $cols = ['order_id', 'book_id', $qtyCol, $unitCol, $totalCol];
      $sqlItems = "INSERT INTO order_items (" . implode(',', $cols) . ") VALUES (?,?,?,?,?)";
      $it = $conn->prepare($sqlItems);

      foreach ($lines as $ln) {
        $it->execute([
          $orderId,
          $ln['book_id'],
          $ln['qty'],
          $ln['unit_final'],            // unit price actually charged
          $ln['line_total']             // computed line total
        ]);
      }

      /* ---- payments (optional) ---- */
      if (table_exists($conn, 'payments')) {
        $present = [];
        foreach (['order_id', 'amount', 'payment_method', 'transaction_id', 'payment_date', 'status', 'currency'] as $c) {
          if (col_exists($conn, 'payments', $c)) $present[] = $c;
        }
        if (in_array('order_id', $present, true) && in_array('amount', $present, true) && in_array('payment_method', $present, true)) {
          $tx = strtoupper(preg_replace('/\s+/', '_', $payMethod)) . '_' . uniqid();
          $vals = [];
          foreach ($present as $c) {
            $vals[] = match ($c) {
              'order_id'       => $orderId,
              'amount'         => $total_commit,
              'payment_method' => $payMethod,
              'transaction_id' => $tx,
              'payment_date'   => date('Y-m-d H:i:s'),
              'status'         => 'PAID',
              'currency'       => 'USD', // change if needed
              default          => null,
            };
          }
          $sqlp = "INSERT INTO payments (" . implode(',', $present) . ") VALUES (" . implode(',', array_fill(0, count($present), '?')) . ")";
          $pm = $conn->prepare($sqlp);
          $pm->execute($vals);
        }
      }

      /* ---- clear cart ---- */
      if (table_exists($conn, 'cart_items')) {
        $del = $conn->prepare("DELETE FROM cart_items WHERE user_id=?");
        $del->execute([$userId]);
      }
      unset($_SESSION['cart'], $_SESSION['coupon_code']);

      $conn->commit();
      header("Location: order_success.php?id=" . $orderId);
      exit;
    } catch (Throwable $e) {
      if ($conn->inTransaction()) $conn->rollBack();
      $errors[] = "Checkout failed: " . $e->getMessage();
    }
  }
}

/* ---------- Render ---------- */
$pageTitle = "Checkout • BookNest";
require_once __DIR__ . '/partials/header.php';
?>
<h1 class="h4 fw-bold mb-3">Checkout</h1>

<div class="row g-4">
  <div class="col-lg-7">
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form method="post" class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-4">
        <div class="mb-3">
          <label class="form-label">Full name</label>
          <input name="full_name" class="form-control" value="<?= h($_POST['full_name'] ?? ($_SESSION['user_name'] ?? '')) ?>" required>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control" value="<?= h($_POST['phone'] ?? '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">City</label>
            <input name="city" class="form-control" value="<?= h($_POST['city'] ?? '') ?>" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Street / Address</label>
          <input name="street" class="form-control" value="<?= h($_POST['street'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Postal code (optional)</label>
          <input name="postal_code" class="form-control" value="<?= h($_POST['postal_code'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label d-block">Delivery method</label>
          <?php $chosen = $delivery_choice; ?>
          <label class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="delivery_method" value="standard" <?= $chosen === 'standard' ? 'checked' : '' ?>>
            <span class="form-check-label">Standard ($<?= number_format(shipping_fee_for('standard'), 2) ?>)</span>
          </label>
          <label class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="delivery_method" value="express" <?= $chosen === 'express' ? 'checked' : '' ?>>
            <span class="form-check-label">Express ($<?= number_format(shipping_fee_for('express'), 2) ?>)</span>
          </label>
        </div>

        <div class="mb-3">
          <label class="form-label d-block">Payment method</label>
          <?php $pm = $_POST['payment_method'] ?? 'COD'; ?>
          <label class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="payment_method" value="COD" <?= $pm === 'COD' ? 'checked' : '' ?>>
            <span class="form-check-label">Cash on Delivery (COD)</span>
          </label>
          <label class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="payment_method" value="KBZ Pay" <?= $pm === 'KBZ Pay' ? 'checked' : '' ?>>
            <span class="form-check-label">KBZ Pay</span>
          </label>
          <label class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="payment_method" value="PayPal" <?= $pm === 'PayPal' ? 'checked' : '' ?>>
            <span class="form-check-label">PayPal</span>
          </label>
          <label class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="payment_method" value="WavePay" <?= $pm === 'WavePay' ? 'checked' : '' ?>>
            <span class="form-check-label">Wave Pay</span>
          </label>
        </div>

        <div class="d-flex gap-2">
          <a class="btn btn-outline-secondary rounded-pill" href="cart.php">Back to Cart</a>
          <button class="btn btn-primary rounded-pill px-4">Place Order</button>
        </div>
      </div>
    </form>
  </div>

  <div class="col-lg-5">
    <div class="card border-0 shadow-sm rounded-4">
      <div class="card-body p-4">
        <h2 class="h6 fw-bold mb-3">Order Summary</h2>

        <ul class="list-unstyled mb-3">
          <?php foreach ($lines as $ln): ?>
            <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
              <div class="me-3">
                <div class="fw-semibold"><?= h($ln['title']) ?></div>
                <div class="text-muted small"><?= h($ln['author_name']) ?> · x<?= (int)$ln['qty'] ?></div>
              </div>
              <div class="fw-semibold">$<?= number_format($ln['line_total'], 2) ?></div>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="d-flex justify-content-between">
          <span class="text-muted">Items Subtotal</span>
          <span>$<?= number_format(array_reduce($lines, fn($c, $x) => $c + $x['line_total'], 0.0), 2) ?></span>
        </div>

        <?php if ($applied_discount): ?>
          <div class="d-flex justify-content-between text-success">
            <span>Discount<?= !empty($applied_discount['discount_code']) ? ' (' . h($applied_discount['discount_code']) . ')' : '' ?></span>
            <span>- $<?= number_format($discount_value, 2) ?></span>
          </div>
          <?php if (!empty($applied_discount['free_shipping'])): ?>
            <div class="small text-muted">Free shipping applied</div>
          <?php endif; ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between">
          <span class="text-muted">Shipping (<?= h(ucfirst($delivery_choice)) ?>)</span>
          <span>$<?= number_format($ship_fee, 2) ?></span>
        </div>

        <hr>
        <div class="d-flex justify-content-between fs-5 fw-bold">
          <span>Total</span>
          <span>$<?= number_format($preview_total, 2) ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>