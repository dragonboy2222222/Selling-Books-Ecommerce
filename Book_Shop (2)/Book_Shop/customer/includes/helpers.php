<?php
// /includes/helpers.php
if (!isset($_SESSION)) {
  session_start();
}

/* ---------------------------
   Basic helpers
---------------------------- */
function h($v)
{
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function url(string $path, array $params = []): string
{
  $q = $params ? ('?' . http_build_query($params)) : '';
  return $path . $q;
}

/** Returns project root (folder that contains /includes) */
function project_root_dir(): string
{
  $root = dirname(__DIR__);
  if (!file_exists($root . '/includes/dbconnect.php')) {
    $root = __DIR__ . '/..';
  }
  return $root;
}

/* ---------------------------
   DB Introspection (used in many pages)
---------------------------- */
function table_exists(PDO $conn, string $table): bool
{
  $st = $conn->prepare("
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
  ");
  $st->execute([$table]);
  return (bool)$st->fetchColumn();
}

function col_exists(PDO $conn, string $table, string $col): bool
{
  $st = $conn->prepare("
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?
  ");
  $st->execute([$table, $col]);
  return (bool)$st->fetchColumn();
}

/* ---------------------------
   Cart helpers (DB cart when logged-in, session cart otherwise)
---------------------------- */
function cart_using_db($conn = null): bool
{
  return !empty($_SESSION['user_id']) && $conn instanceof PDO;
}

/** Total quantity in cart */
function cart_count($conn = null): int
{
  if (cart_using_db($conn)) {
    $st = $conn->prepare("SELECT COALESCE(SUM(quantity),0) FROM cart_items WHERE user_id=?");
    $st->execute([$_SESSION['user_id']]);
    return (int)$st->fetchColumn();
  }
  $sum = 0;
  foreach (($_SESSION['cart'] ?? []) as $q) $sum += (int)$q;
  return $sum;
}

/** Add a book to cart */
function cart_add($conn = null, int $bookId, int $qty = 1): void
{
  $qty = max(1, $qty);
  if (cart_using_db($conn)) {
    // Requires UNIQUE KEY (user_id, book_id) on cart_items
    $st = $conn->prepare("
      INSERT INTO cart_items (user_id, book_id, quantity, added_at)
      VALUES (?,?,?, NOW())
      ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ");
    $st->execute([$_SESSION['user_id'], $bookId, $qty]);
  } else {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) $_SESSION['cart'] = [];
    $_SESSION['cart'][$bookId] = ($_SESSION['cart'][$bookId] ?? 0) + $qty;
  }
}

/** Update quantity for a cart line (0 removes) */
function cart_update($conn = null, int $bookId, int $qty): void
{
  $qty = max(0, $qty);
  if (cart_using_db($conn)) {
    if ($qty === 0) {
      cart_remove($conn, $bookId);
    } else {
      $st = $conn->prepare("UPDATE cart_items SET quantity=? WHERE user_id=? AND book_id=?");
      $st->execute([$qty, $_SESSION['user_id'], $bookId]);
    }
  } else {
    if ($qty === 0) unset($_SESSION['cart'][$bookId]);
    else $_SESSION['cart'][$bookId] = $qty;
  }
}

/** Remove a book from cart */
function cart_remove($conn = null, int $bookId): void
{
  if (cart_using_db($conn)) {
    $st = $conn->prepare("DELETE FROM cart_items WHERE user_id=? AND book_id=?");
    $st->execute([$_SESSION['user_id'], $bookId]);
  } else {
    unset($_SESSION['cart'][$bookId]);
  }
}

/**
 * Load cart items for display.
 * Returns [array $items, float $subtotal]
 * Each $item: book_id, title, price, image_url, qty, total
 */
function cart_fetch_items($conn = null): array
{
  $items = [];
  $subtotal = 0.0;

  if (cart_using_db($conn)) {
    $st = $conn->prepare("
      SELECT b.book_id, b.title, b.price, b.image_url, ci.quantity AS qty
      FROM cart_items ci
      JOIN books b ON b.book_id = ci.book_id
      WHERE ci.user_id = ?
      ORDER BY b.title
    ");
    $st->execute([$_SESSION['user_id']]);
    $items = $st->fetchAll(PDO::FETCH_ASSOC);
    foreach ($items as &$r) {
      $r['qty']   = (int)$r['qty'];
      $r['total'] = (float)$r['price'] * $r['qty'];
      $subtotal  += $r['total'];
    }
    unset($r);
  } else {
    // Session cart requires $conn to get book data
    $ids = array_keys($_SESSION['cart'] ?? []);
    if ($ids) {
      if (!($conn instanceof PDO)) return [[], 0.0];
      $in = implode(',', array_fill(0, count($ids), '?'));
      $st = $conn->prepare("SELECT book_id, title, price, image_url FROM books WHERE book_id IN ($in)");
      $st->execute($ids);
      while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
        $qty = (int)($_SESSION['cart'][$r['book_id']] ?? 0);
        $r['qty']   = $qty;
        $r['total'] = (float)$r['price'] * $qty;
        $subtotal  += $r['total'];
        $items[]    = $r;
      }
    }
  }

  return [$items, $subtotal];
}

/**
 * After login: merge any guest session cart into DB for this user.
 * Call once right after you set $_SESSION['user_id'].
 */
function cart_merge_session_into_db(PDO $conn): void
{
  if (empty($_SESSION['cart']) || empty($_SESSION['user_id'])) return;

  foreach ($_SESSION['cart'] as $bookId => $qty) {
    $st = $conn->prepare("
      INSERT INTO cart_items (user_id, book_id, quantity, added_at)
      VALUES (?,?,?, NOW())
      ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ");
    $st->execute([$_SESSION['user_id'], (int)$bookId, max(1, (int)$qty)]);
  }
  // Clear guest cart once merged
  $_SESSION['cart'] = [];
}

/* ---------------------------
   Reviews gating (choose your demo mode)
   'none'      = allow review immediately after order placed
   'paid'      = allow after payment (default)
   'delivered' = require delivery/complete
---------------------------- */
// Reviews gate: 'none' | 'paid' | 'delivered'
if (!defined('REVIEWS_GATE')) {
  define('REVIEWS_GATE', 'none'); // change to 'none' for instant demo reviews
}

function user_can_review(PDO $conn, int $userId, int $bookId): bool
{
  $gate = defined('REVIEWS_GATE') ? REVIEWS_GATE : 'paid';

  // Always require a purchase record
  $baseSql = "SELECT 1
              FROM order_items oi
              JOIN orders o ON o.order_id = oi.order_id";
  $where   = " WHERE o.user_id = ? AND oi.book_id = ? ";
  $params  = [$userId, $bookId];

  if ($gate === 'none') {
    $st = $conn->prepare($baseSql . $where . " LIMIT 1");
    $st->execute($params);
    return (bool)$st->fetchColumn();
  }

  // Detect what the DB actually has
  $hasPayStatus = function_exists('col_exists') ? col_exists($conn, 'orders', 'payment_status') : false;
  $hasStatus    = function_exists('col_exists') ? col_exists($conn, 'orders', 'status') : false;
  $hasShipTbl   = function_exists('table_exists') ? table_exists($conn, 'shipments') : false;
  $hasShipDeliv = $hasShipTbl && col_exists($conn, 'shipments', 'delivered_at');

  if ($gate === 'paid') {
    $conds = [];
    if ($hasPayStatus) $conds[] = "o.payment_status = 'PAID'";
    if ($hasStatus)    $conds[] = "LOWER(o.status) IN ('paid','completed','delivered','shipped')";

    // If neither column exists, fall back to “purchased is enough”
    $sql = $baseSql . $where . ($conds ? " AND (" . implode(" OR ", $conds) . ")" : "") . " LIMIT 1";
    $st  = $conn->prepare($sql);
    $st->execute($params);
    return (bool)$st->fetchColumn();
  }

  if ($gate === 'delivered') {
    $conds = [];
    $join  = '';
    if ($hasShipDeliv) {
      $join  = " LEFT JOIN shipments s ON s.order_id = o.order_id ";
      $conds[] = "s.delivered_at IS NOT NULL";
    }
    if ($hasStatus) $conds[] = "LOWER(o.status) IN ('delivered','completed')";

    // If nothing to check, fall back to purchased (still avoids hard error)
    $sql = "SELECT 1
            FROM order_items oi
            JOIN orders o ON o.order_id = oi.order_id
            $join " . $where . ($conds ? " AND (" . implode(" OR ", $conds) . ")" : "") . " LIMIT 1";
    $st = $conn->prepare($sql);
    $st->execute($params);
    return (bool)$st->fetchColumn();
  }

  return false;
}

/** Gate for showing "Review items" on orders list (safe even if columns missing) */
function order_allows_reviews(array $o): bool
{
  $gate   = defined('REVIEWS_GATE') ? REVIEWS_GATE : 'paid';
  $status = strtolower((string)($o['status'] ?? ''));
  $pay    = strtoupper((string)($o['payment_status'] ?? ''));

  if ($gate === 'none') return true;
  if ($gate === 'paid') return ($pay === 'PAID') || in_array($status, ['paid', 'completed', 'delivered', 'shipped'], true);
  if ($gate === 'delivered') return in_array($status, ['delivered', 'completed'], true) || !empty($o['delivered_at']);
  return false;
}

// ... your existing helpers.php content ...

// Ensure col_exists exists only once (you already have it; keep yours).
if (!function_exists('col_exists')) {
  function col_exists(PDO $conn, string $table, string $column): bool
  {
    $q = $conn->prepare("SELECT 1 FROM information_schema.COLUMNS
                         WHERE TABLE_SCHEMA = DATABASE()
                           AND TABLE_NAME   = ?
                           AND COLUMN_NAME  = ?
                         LIMIT 1");
    $q->execute([$table, $column]);
    return (bool)$q->fetchColumn();
  }
}

/**
 * Return current stock for a book using your schema.
 * Prefers `stock_quantity`, then falls back to `quantity`, then `stock`.
 */
if (!function_exists('book_stock')) {
  function book_stock(PDO $conn, int $bookId): int
  {
    static $col = null; // detect once per request

    if ($col === null) {
      $col = 'stock_quantity';
      if (!col_exists($conn, 'books', $col)) {
        foreach (['quantity', 'stock'] as $alt) {
          if (col_exists($conn, 'books', $alt)) {
            $col = $alt;
            break;
          }
        }
      }
    }

    $st = $conn->prepare("SELECT COALESCE($col,0) FROM books WHERE book_id=?");
    $st->execute([$bookId]);
    return (int)($st->fetchColumn() ?? 0);
  }
}

/** Clamp a requested qty to [1..stock]. If stock=0, returns 0. */
if (!function_exists('clamp_qty_to_stock')) {
  function clamp_qty_to_stock(PDO $conn, int $bookId, int $requested): int
  {
    $stock = book_stock($conn, $bookId);
    if ($stock <= 0) return 0;
    return max(1, min($requested, $stock));
  }
}
