<?php
// /includes/dbconnect.php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'book_shop';
$DB_USER = 'root';
$DB_PASS = ''; // <-- set your password if any

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $conn = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (Throwable $e) {
  http_response_code(500);
  echo "<pre style='padding:24px;font:14px/1.4 monospace;'>Database connection failed.\n\n".h($e->getMessage())."</pre>";
  exit;
}
