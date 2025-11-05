<?php
// customer/includes/discounts.php

/* ---------- Per-book discounts ---------- */
function active_book_discount(PDO $conn, int $bookId): ?array
{
  $sql = "SELECT discount_type, value
          FROM book_discounts
          WHERE book_id=? AND is_active=1
            AND (start_date IS NULL OR start_date<=CURDATE())
            AND (end_date   IS NULL OR end_date  >=CURDATE())
          ORDER BY book_discount_id DESC
          LIMIT 1";
  $st = $conn->prepare($sql);
  $st->execute([$bookId]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  return $row ?: null;
}

function price_after_discount(float $base, ?array $disc): float
{
  if (!$disc) return round($base, 2);
  $type = strtolower((string)($disc['discount_type'] ?? ''));
  $val  = (float)($disc['value'] ?? 0);
  if ($type === 'percentage')   return round(max(0, $base * (1 - $val / 100)), 2);
  if ($type === 'fixed_amount') return round(max(0, $base - $val), 2);
  return round($base, 2);
}

function discount_badge(?array $disc): string
{
  if (!$disc) return '';
  $type = strtolower((string)($disc['discount_type'] ?? ''));
  $val  = (float)($disc['value'] ?? 0);
  return $type === 'percentage' ? ('-' . (int)round($val) . '%') : ('-' . number_format($val, 0));
}

/* ---------- Order-level discounts ---------- */
function is_first_paid_order(PDO $conn, int $userId): bool
{
  $st = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id=?");
  $st->execute([$userId]);
  return ((int)$st->fetchColumn()) === 0;
}

function find_active_discount_by_code(PDO $conn, string $code, float $subtotal): ?array
{
  if ($code === '') return null;
  $sql = "SELECT *
          FROM discounts
          WHERE is_active=1
            AND discount_code=?
            AND (start_date IS NULL OR start_date<=CURDATE())
            AND (end_date   IS NULL OR end_date  >=CURDATE())
            AND (min_subtotal IS NULL OR ? >= min_subtotal)
          LIMIT 1";
  $st = $conn->prepare($sql);
  $st->execute([$code, $subtotal]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  return $row ?: null;
}

function get_auto_first_purchase_discount(PDO $conn): ?array
{
  $sql = "SELECT *
          FROM discounts
          WHERE is_active=1
            AND applies_first_purchase=1
            AND applies_automatically=1
            AND (start_date IS NULL OR start_date<=CURDATE())
            AND (end_date   IS NULL OR end_date  >=CURDATE())
          ORDER BY discount_id DESC
          LIMIT 1";
  return $conn->query($sql)->fetch(PDO::FETCH_ASSOC) ?: null;
}

function apply_order_level_discount(float $subtotal, float $shipping, ?array $disc): array
{
  if (!$disc) return [$subtotal, $shipping, 0.0];

  $off = 0.0;
  $type = strtolower((string)($disc['discount_type'] ?? ''));
  $val  = (float)($disc['value'] ?? 0);

  if ($type === 'percentage')   $off = round($subtotal * ($val / 100), 2);
  if ($type === 'fixed_amount') $off = $val;

  $off = min($off, $subtotal);

  if (!empty($disc['free_shipping'])) $shipping = 0.0;

  return [max(0.0, $subtotal - $off), $shipping, $off];
}
