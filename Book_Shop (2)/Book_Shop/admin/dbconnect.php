<?php
// dbconnect.php  — provides $conn (PDO) for book_shop
$hostname = "127.0.0.1";
$username = "root";
$password = "";
$dbname   = "book_shop";

$dsn = "mysql:host=$hostname;dbname=$dbname;charset=utf8mb4";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // keep simple, no stack traces to users
    exit("Database connection failed.");
}
