<?php
// toggle_theme.php — flip between 'light' and 'dark'
$curr = $_COOKIE['theme'] ?? 'light';
$next = ($curr === 'dark') ? 'light' : 'dark';
setcookie('theme', $next, time() + 60*60*24*365, '/', '', !empty($_SERVER['HTTPS']), false);
$back = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: {$back}");
exit;
