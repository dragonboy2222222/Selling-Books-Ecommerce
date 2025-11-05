<?php
// user_role_update.php
if (!isset($_SESSION)) {
  session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/dbconnect.php';

// Always throw exceptions so we can return useful JSON
if ($conn instanceof PDO) {
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

function jerr($msg, $code = 400)
{
  http_response_code($code);
  echo json_encode(['ok' => false, 'message' => $msg], JSON_UNESCAPED_SLASHES);
  exit;
}
function jok($data = [])
{
  echo json_encode(['ok' => true] + $data, JSON_UNESCAPED_SLASHES);
  exit;
}

// 1) Auth check: must be logged in AND be admin
if (empty($_SESSION['loginSuccess']) || empty($_SESSION['email'])) {
  jerr('Not authenticated', 401);
}
try {
  $stmt = $conn->prepare(
    "SELECT r.role_name
       FROM users u
       JOIN roles r ON r.role_id = u.role_id
      WHERE u.email = ?
      LIMIT 1"
  );
  $stmt->execute([$_SESSION['email']]);
  $me = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$me || strtolower($me['role_name']) !== 'admin') {
    jerr('Only admin can change roles', 403);
  }
} catch (Throwable $e) {
  jerr('Auth check failed');
}

// 2) Parse JSON body
$raw = file_get_contents('php://input');
$body = json_decode($raw, true);
if (!is_array($body)) jerr('Invalid JSON');

$user_id   = isset($body['user_id']) ? (int)$body['user_id'] : 0;
$role_name = isset($body['role_name']) ? trim($body['role_name']) : '';
$role_id   = isset($body['role_id']) ? (int)$body['role_id'] : 0;

if ($user_id <= 0) jerr('Missing user_id');

// 3) Resolve role_id
try {
  if ($role_id > 0) {
    // validate role_id exists
    $check = $conn->prepare("SELECT role_id FROM roles WHERE role_id = ? LIMIT 1");
    $check->execute([$role_id]);
    if (!$check->fetchColumn()) jerr('role_id not found');
  } else {
    if ($role_name === '') jerr('Provide role_name or role_id');
    $find = $conn->prepare("SELECT role_id FROM roles WHERE LOWER(role_name) = LOWER(?) LIMIT 1");
    $find->execute([$role_name]);
    $rid = $find->fetchColumn();
    if (!$rid) jerr('role_name not found');
    $role_id = (int)$rid;
  }

  // 4) Update the user’s role
  $upd = $conn->prepare("UPDATE users SET role_id = ? WHERE user_id = ? LIMIT 1");
  $upd->execute([$role_id, $user_id]);

  // 5) Return success
  jok();
} catch (Throwable $e) {
  // You can log $e->getMessage() for debugging
  jerr('Database error', 500);
}
