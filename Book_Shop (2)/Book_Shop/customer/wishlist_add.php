<?php
if (!isset($_SESSION)) { session_start(); }
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (empty($_SESSION['user_id'])) {
  if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['ok'=>false,'login'=>true,'url'=>'login.php?return='.urlencode($_SERVER['HTTP_REFERER']??'index.php')]);
    exit;
  }
  header('Location: login.php?return='.urlencode($_SERVER['HTTP_REFERER']??'index.php'));
  exit;
}

$userId = (int)$_SESSION['user_id'];
$bookId = (int)($_POST['book_id'] ?? 0);

try {
  if ($bookId <= 0) throw new RuntimeException('Invalid book.');

  // avoid duplicates
  $st = $conn->prepare("INSERT IGNORE INTO wishlists (user_id, book_id, created_at) VALUES (?,?,NOW())");
  $st->execute([$userId, $bookId]);

  if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['ok'=>true,'inWishlist'=>true]);
    exit;
  }

  // non-AJAX fallback = go back
  header('Location: '.$_SERVER['HTTP_REFERER']);
} catch (Throwable $e) {
  if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['ok'=>false,'msg'=>$e->getMessage()]);
    exit;
  }
  header('Location: '.$_SERVER['HTTP_REFERER']);
}
