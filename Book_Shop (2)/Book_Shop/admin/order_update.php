<?php
// admin/order_update.php — small JSON endpoint to update orders.{order_status|payment_status}
if (!isset($_SESSION)) session_start();
header('Content-Type: application/json');

if (empty($_SESSION['loginSuccess'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

// include db (supports /admin or root)
$dbc = __DIR__ . '/dbconnect.php';
if (!file_exists($dbc)) $dbc = __DIR__ . '/../dbconnect.php';
require_once $dbc; // $conn (PDO)

$raw = file_get_contents('php://input');
$req = json_decode($raw, true);

$id    = isset($req['id']) ? (int)$req['id'] : 0;
$field = trim($req['field'] ?? '');
$value = trim($req['value'] ?? '');

if ($id <= 0 || !in_array($field, ['order_status', 'payment_status'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Bad request']);
    exit;
}

// Normalize / validate allowed values
if ($field === 'order_status') {
    $v = strtolower($value);
    $allowed = ['pending', 'paid', 'delivered', 'canceled', 'cancelled'];
    if (!in_array($v, $allowed, true)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid order_status']);
        exit;
    }
    if ($v === 'cancelled') $v = 'canceled';
} else {
    // payment_status
    $v = strtoupper($value);
    $allowed = ['UNPAID', 'PAID', 'REFUNDED', 'FAILED'];
    if (!in_array($v, $allowed, true)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid payment_status']);
        exit;
    }
}

try {
    $sql = "UPDATE orders SET `$field` = ? WHERE order_id = ?";
    $st  = $conn->prepare($sql);
    $st->execute([$v, $id]);

    echo json_encode(['ok' => true, 'id' => $id, 'field' => $field, 'value' => $v]);
} catch (Throwable $e) {
    error_log("[order_update] " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'DB error']);
}
