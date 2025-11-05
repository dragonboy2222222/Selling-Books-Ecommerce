<?php
// chatbot.php — rule-based bot for BookNest
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/includes/dbconnect.php';

/* ---------- Small DB helpers ---------- */
function table_exists(PDO $db, string $t): bool
{
    try {
        $db->query("SELECT 1 FROM `$t` LIMIT 1");
        return true;
    } catch (Throwable $e) {
        return false;
    }
}
function col_exists(PDO $db, string $t, string $c): bool
{
    try {
        $db->query("SELECT `$c` FROM `$t` LIMIT 1");
        return true;
    } catch (Throwable $e) {
        return false;
    }
}
function qa(PDO $db, string $sql, array $p = []): array
{
    try {
        $st = $db->prepare($sql);
        $st->execute($p);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}

/* ---------- Request ---------- */
header('Content-Type: application/json');

// Support both JSON and form POST
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$msg = '';
if (is_array($data) && isset($data['message'])) {
    $msg = $data['message'];
} elseif (!empty($_POST['message'])) {
    $msg = $_POST['message'];
}
$msg = trim($msg);

function reply(string $msg)
{
    echo json_encode(['reply' => $msg]);
    exit;
}

/* Normalize */
$low = mb_strtolower($msg, 'UTF-8');
$norm = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $low);
$norm = preg_replace('/\s+/u', ' ', $norm);

function has(string $hay, array $needles): bool
{
    foreach ($needles as $n) {
        if ($n !== '' && strpos($hay, $n) !== false) return true;
    }
    return false;
}

/* ---------- 1) Rude/off-topic ---------- */
$bad = ['fuck', 'bitch', 'shit', 'asshole', 'suck', 'motherf', 'dick', 'pussy', 'cunt', 'sex', 'porn', 'xxx', 'nude', '18+', 'horny'];
if (has($norm, $bad)) {
    reply("Please keep it respectful 🙏. I can answer questions about BookNest — book counts, categories, promotions/discounts, shipping, how to place an order, reviews, order history, or payment methods.");
}

/* ---------- 2) Intents ---------- */
// Books count
if (has($norm, ['how many books', 'book count', 'total books', 'books do you have', 'how many book'])) {
    if (table_exists($conn, 'books')) {
        $row = $conn->query("SELECT COUNT(*) AS c FROM books")->fetch(PDO::FETCH_ASSOC);
        $c = (int)($row['c'] ?? 0);
        reply("We currently have {$c} books available on BookNest. 📚");
    }
    reply("Our catalog is active, but I can’t fetch the count right now. Please try again later.");
}

// Categories
if (has($norm, ['categories', 'genres', 'types of books', 'what categories'])) {
    if (table_exists($conn, 'categories')) {
        $rows = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC")
            ->fetchAll(PDO::FETCH_COLUMN);
        if ($rows) reply("Available categories: " . implode(", ", $rows) . ".");
    }
    reply("Categories are available on the Shop page (top of the site).");
}

// Promotions
if (has($norm, ['promo', 'promotion', 'promotions', 'discount', 'discounts', 'offer', 'offers', 'sale', 'sales'])) {
    if (table_exists($conn, 'discounts')) {
        $hasActive = col_exists($conn, 'discounts', 'active');
        $hasStart  = col_exists($conn, 'discounts', 'start_date');
        $hasEnd    = col_exists($conn, 'discounts', 'end_date');

        if ($hasActive || $hasStart || $hasEnd) {
            $sql = "SELECT name FROM discounts WHERE 1=1";
            $conds = [];
            if ($hasActive) $conds[] = "active = 1";
            if ($hasStart)  $conds[] = "(start_date IS NULL OR start_date <= NOW())";
            if ($hasEnd)    $conds[] = "(end_date IS NULL OR end_date >= NOW())";
            if ($conds) $sql .= " AND " . implode(' AND ', $conds);
            $sql .= " ORDER BY name LIMIT 8";

            $rows = qa($conn, $sql);
            $names = array_values(array_filter(array_map(fn($r) => $r['name'] ?? '', $rows)));
            if ($names) reply("Current promotions: " . implode(", ", $names) . ". 🏷️");
        }
    }
    reply("Any available discounts will appear on product pages or during checkout. 🏷️");
}

// Place an order
if (has($norm, ['place order', 'place an order', 'buy', 'purchase', 'checkout', 'how to order', 'order book'])) {
    reply("How to place an order:\n1️⃣ Browse books and Add to cart 🛒\n2️⃣ Open your cart →Checkout\n3️⃣ Enter shipping & payment details\n4️⃣ Confirm the order — that’s it!");
}

// Shipping
if (has($norm, ['free shipping', 'delivery fee', 'shipping cost', 'shipping info', 'shipping'])) {
    reply("Shipping costs depend on your chosen delivery option. If free shipping is available, it will be shown during checkout. 🚚");
}

// My orders
if (has($norm, ['my orders', 'order history', 'track order', 'where is my order'])) {
    reply("Go to My Orders (top-right menu) after logging in to view your order history and details.");
}

// Reviews
if (has($norm, ['review', 'write review', 'write a review', 'feedback', 'rate book', 'how do i write a review'])) {
    reply("After purchasing a book, open My Orders → Review items for that order and leave your review. ⭐");
}

// Contact support
if (has($norm, ['contact', 'support', 'help', 'customer service', 'contact us'])) {
    reply("You can reach us from the Contact Us page (see the footer). We’ll be happy to help! ✉️");
}

// Payment
if (has($norm, ['payment', 'pay method', 'pay methods', 'methods', 'visa', 'paypal', 'kbz', 'wave', 'how can i pay'])) {
    reply("We currently accept Cash on Delivery, KBZ Pay, Wave Pay, PayPal, and Visa. 💳");
}

/* ---------- Fallback ---------- */
reply("I can help with:\n• Book counts\n• Categories\n• Promotions/discounts\n•
 How to place orders\n• Shipping info\n• Order history\n• Reviews\n• Support contact\n• 
 Payment methods\n\nTry asking: “How many books do you have?” or “Any promotions now?”");
