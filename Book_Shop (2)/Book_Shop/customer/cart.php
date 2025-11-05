<?php
// customer/cart.php
if (!isset($_SESSION)) session_start();
$pageTitle = "BookNest — Cart";

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';
require_once __DIR__ . '/includes/discounts.php';

$notices = [];

/* ------------------------ POST actions (stock-safe) ------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $bookId = (int)($_POST['book_id'] ?? 0);
  $qty    = max(0, (int)($_POST['qty'] ?? 1));

  if ($action === 'add' && $bookId > 0) {
    $clamped = clamp_qty_to_stock($conn, $bookId, $qty);
    if ($clamped <= 0) {
      $notices[] = ['type' => 'danger', 'msg' => 'Sorry, this book is currently out of stock.'];
    } else {
      if ($clamped < $qty) {
        $notices[] = ['type' => 'warning', 'msg' => "Limited stock. Added $clamped copy(ies)."];
      }
      cart_add($conn, $bookId, $clamped);
    }
  } elseif ($action === 'update' && $bookId > 0) {
    $clamped = clamp_qty_to_stock($conn, $bookId, $qty);
    if ($clamped <= 0) {
      cart_remove($conn, $bookId);
      $notices[] = ['type' => 'danger', 'msg' => 'That title is now out of stock and was removed from your cart.'];
    } else {
      if ($clamped < $qty) {
        $notices[] = ['type' => 'warning', 'msg' => "Quantity reduced to $clamped due to stock limits."];
      }
      cart_update($conn, $bookId, $clamped);
    }
  } elseif ($action === 'remove' && $bookId > 0) {
    cart_remove($conn, $bookId);
  } elseif ($action === 'apply_coupon') {
    $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
    $_SESSION['coupon_code'] = $code ?: null;
  } elseif ($action === 'remove_coupon') {
    unset($_SESSION['coupon_code']);
  }

  header('Location: cart.php');
  exit;
}

/* --- Merge guest session cart into DB after login --- */
if (!empty($_SESSION['user_id']) && !empty($_SESSION['cart'])) {
  cart_merge_session_into_db($conn);
}

/* -------------------- Load cart items -------------------- */
list($rawItems, $ignoreSubtotal) = cart_fetch_items($conn);

/**
 * Normalize lines against stock every page load:
 * - If qty > stock, auto-reduce & notify once.
 * - If stock == 0, remove line & notify once.
 */
$items = [];
$subtotal_before_order_level = 0.0;

foreach ($rawItems as $r) {
  $bookId = (int)$r['book_id'];
  $stock  = book_stock($conn, $bookId);

  // Base pricing & per-book discount
  $disc = active_book_discount($conn, $bookId);
  $base = (float)$r['price'];
  $unit = price_after_discount($base, $disc);

  $qty  = (int)($r['qty'] ?? 1);

  if ($stock <= 0) {
    // remove from cart
    cart_remove($conn, $bookId);
    $notices[] = ['type' => 'danger', 'msg' => "“" . ($r['title'] ?? 'This book') . "” is out of stock and was removed from your cart."];
    continue;
  }

  if ($qty > $stock) {
    $qty = $stock;
    cart_update($conn, $bookId, $qty);
    $notices[] = ['type' => 'warning', 'msg' => "“" . ($r['title'] ?? 'This book') . "” quantity reduced to available stock ($stock)."];
  }

  $line = round($unit * $qty, 2);

  $items[] = [
    'book_id' => $bookId,
    'title'   => $r['title'],
    'image'   => ($r['image_url'] ?? $r['image'] ?? '') ?: 'assets/img/book1.png',
    'qty'     => $qty,
    'price'   => $base, // base
    'unit'    => $unit, // after per-book discount
    'disc'    => $disc,
    'line'    => $line,
    'stock'   => $stock,
  ];

  $subtotal_before_order_level += $line;
}

/* ---------------- Order-level discount preview ---------------- */
$shipping_preview   = 0.00; // shown as “calculated at checkout”
$applied_discount   = null;
$discount_value     = 0.00;
$applied_disclaimer = '';

if (!empty($items)) {
  $couponCode = $_SESSION['coupon_code'] ?? '';
  if ($couponCode !== '') {
    $applied_discount = find_active_discount_by_code($conn, $couponCode, $subtotal_before_order_level);
  }

  if (!$applied_discount && !empty($_SESSION['user_id'])) {
    $auto = get_auto_first_purchase_discount($conn);
    if ($auto && is_first_paid_order($conn, (int)$_SESSION['user_id'])) {
      if (empty($auto['min_subtotal']) || $subtotal_before_order_level >= (float)$auto['min_subtotal']) {
        $applied_discount   = $auto;
        $applied_disclaimer = ' (auto)';
      }
    }
  }

  if ($applied_discount) {
    list($sub_after, $ship_after, $discount_value) =
      apply_order_level_discount($subtotal_before_order_level, $shipping_preview, $applied_discount);
    $subtotal_after = $sub_after;
  }
}

/* ---------------- Checkout URL decision ---------------- */
$hasItems    = !empty($items);
$checkoutUrl = empty($_SESSION['user_id']) ? 'login.php?return=' . urlencode('checkout.php') : 'checkout.php';

/* ---------------- Render ---------------- */
require_once __DIR__ . '/partials/header.php';
?>
<h1 class="h4 fw-bold mb-3">Your Cart</h1>

<?php foreach ($notices as $n): ?>
<div class="alert alert-<?= $n['type'] ?>"><?= $n['msg'] ?></div>
<?php endforeach; ?>

<?php if (!$items): ?>
<p class="text-muted">Your cart is empty.</p>
<a class="btn btn-dark rounded-pill" href="product.php">Browse books</a>
<?php else: ?>
<div class="table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Book</th>
                <th style="width:200px">Qty</th>
                <th>Price</th>
                <th>Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $it): ?>
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <img src="<?= h($it['image']) ?>" width="56" height="56" style="object-fit:cover"
                            class="rounded" alt="">
                        <div>
                            <div><?= h($it['title']) ?></div>
                            <div class="small text-muted">In stock: <?= (int)$it['stock'] ?></div>
                        </div>
                    </div>
                </td>
                <td>
                    <form method="post" class="d-flex gap-2 align-items-center m-0">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="book_id" value="<?= (int)$it['book_id'] ?>">
                        <input type="number" class="form-control" name="qty" min="1" max="<?= (int)$it['stock'] ?>"
                            value="<?= (int)$it['qty'] ?>">
                        <button class="btn btn-outline-secondary btn-sm">Save</button>
                    </form>
                </td>
                <td>
                    <?php if ($it['disc']): ?>
                    <div>
                        <span class="text-muted text-decoration-line-through me-2">
                            $<?= number_format($it['price'], 0) ?>
                        </span>
                        <span class="fw-bold">$<?= number_format($it['unit'], 0) ?></span>
                        <span class="badge bg-danger ms-2"><?= h(discount_badge($it['disc'])) ?></span>
                    </div>
                    <?php else: ?>
                    $<?= number_format($it['price'], 0) ?>
                    <?php endif; ?>
                </td>
                <td>$<?= number_format($it['line'], 0) ?></td>
                <td>
                    <form method="post" class="m-0">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="book_id" value="<?= (int)$it['book_id'] ?>">
                        <button class="btn btn-link text-danger btn-sm">Remove</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Coupon box -->
<div class="row g-3 align-items-end">
    <div class="col-md-6">
        <form method="post" class="d-flex gap-2">
            <input type="hidden" name="action" value="apply_coupon">
            <input type="text" name="coupon_code" class="form-control" placeholder="Have a coupon? Enter code"
                value="<?= h($_SESSION['coupon_code'] ?? '') ?>">
            <button class="btn btn-outline-primary">Apply</button>
            <?php if (!empty($_SESSION['coupon_code'])): ?>
            <button class="btn btn-outline-secondary" name="action" value="remove_coupon">Remove</button>
            <?php endif; ?>
        </form>
        <?php if (!empty($_SESSION['coupon_code']) && !$applied_discount): ?>
        <div class="text-danger small mt-1">
            Coupon "<?= h($_SESSION['coupon_code']) ?>" is not valid for this cart.
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Totals preview -->
<div class="text-end mt-3">
    <table class="table table-borderless">
        <tr>
            <td class="text-end">Items Subtotal</td>
            <td class="text-end" style="width:180px;">
                <strong>$<?= number_format($subtotal_before_order_level, 0) ?></strong>
            </td>
        </tr>
        <?php if ($applied_discount): ?>
        <tr class="text-success">
            <td class="text-end">
                Discount<?= $applied_discount['discount_code'] ? ' (' . h($applied_discount['discount_code']) . ')' : '' ?><?= $applied_disclaimer ?>
                <?php if (!empty($applied_discount['free_shipping'])): ?>
                <div class="small text-muted">+ Free shipping will apply at checkout</div>
                <?php endif; ?>
            </td>
            <td class="text-end">- $<?= number_format($discount_value, 0) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="text-end">Shipping</td>
            <td class="text-end">Calculated at checkout</td>
        </tr>
        <tr class="fw-bold">
            <td class="text-end">Estimated Total</td>
            <td class="text-end">
                $<?php
            $estimate = $subtotal_before_order_level - $discount_value;
            echo number_format(max(0, $estimate), 0);
            ?>
            </td>
        </tr>
    </table>

    <a href="<?= h($checkoutUrl) ?>" class="btn btn-primary rounded-pill mt-2<?= $hasItems ? '' : ' disabled' ?>"
        role="button" aria-disabled="<?= $hasItems ? 'false' : 'true' ?>">
        Proceed to Checkout
    </a>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/partials/footer.php'; ?>