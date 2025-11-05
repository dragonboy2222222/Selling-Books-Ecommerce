<?php
// admin/crud.php — CRUD for: users, books, discounts, book_discounts, orders (statuses), reviews
if (!isset($_SESSION)) session_start();
if (empty($_SESSION['loginSuccess'])) {
  header('Location: login.php');
  exit;
}

// include dbconnect (works from /admin or root)
$dbc = __DIR__ . '/dbconnect.php';
if (!file_exists($dbc)) $dbc = __DIR__ . '/../dbconnect.php';
require_once $dbc; // must define $conn (PDO)

/* ---------- Helpers ---------- */
function esc($s)
{
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
function qa(PDO $db, string $sql, array $p = [], string $label = '')
{
  try {
    $st = $db->prepare($sql);
    $st->execute($p);
    return $st->fetchAll(PDO::FETCH_ASSOC);
  } catch (Throwable $e) {
    error_log("[CRUD][$label] " . $e->getMessage());
    return [];
  }
}
function qx(PDO $db, string $sql, array $p = [], string $label = '')
{
  try {
    $st = $db->prepare($sql);
    return $st->execute($p);
  } catch (Throwable $e) {
    error_log("[CRUD][$label] " . $e->getMessage());
    return false;
  }
}
function qv(PDO $db, string $sql, array $p = [], $d = 0)
{
  try {
    $st = $db->prepare($sql);
    $st->execute($p);
    return $st->fetchColumn();
  } catch (Throwable $e) {
    error_log("[CRUD] " . $e->getMessage());
    return $d;
  }
}
/* normalize only datetime-local; DATE inputs come through clean */
function normalize_datetime(?string $s): ?string
{
  if ($s === null || $s === '') return null;
  $s = str_replace('T', ' ', $s);
  if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $s)) $s .= ':00';
  return $s;
}
/* safe list loader */
function load_list(PDO $db, array $cfg, string $limitClause)
{
  $rows = [];
  if (!empty($cfg['list_sql'])) $rows = qa($db, $cfg['list_sql'] . $limitClause, [], $cfg['table'] . '.list_sql');
  if ($rows === []) {
    $pk = $cfg['pk'];
    $rows = qa($db, "SELECT * FROM `{$cfg['table']}` ORDER BY `$pk` DESC" . $limitClause, [], $cfg['table'] . ".fallback");
  }
  return $rows;
}

/* ---------- Entities config ---------- */
$entities = [
  /* BOOKS */
  'books' => [
    'pk' => 'book_id',
    'table' => 'books',
    'title' => 'Books',
    'allow_new' => true,
    'fields' => [
      ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => true],
      ['name' => 'author_id', 'label' => 'Author ID', 'type' => 'number', 'required' => true],
      ['name' => 'price', 'label' => 'Price', 'type' => 'number', 'step' => '0.01', 'required' => true],
      ['name' => 'stock_quantity', 'label' => 'Stock', 'type' => 'number', 'required' => true],
      ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
      ['name' => 'image_url', 'label' => 'Image URL', 'type' => 'text'],
    ],
    'list_sql' => "
      SELECT b.book_id, b.title,
             CONCAT(a.first_name,' ',a.last_name) AS author,
             b.price, b.stock_quantity, b.created_at
      FROM books b
      LEFT JOIN authors a ON a.author_id=b.author_id
      ORDER BY COALESCE(b.updated_at, b.created_at, b.book_id) DESC",
    'columns' => [
      ['key' => 'book_id', 'label' => 'ID'],
      ['key' => 'title', 'label' => 'Title'],
      ['key' => 'author', 'label' => 'Author'],
      ['key' => 'price', 'label' => 'Price'],
      ['key' => 'stock_quantity', 'label' => 'Stock'],
      ['key' => 'created_at', 'label' => 'Created'],
    ],
  ],

  /* DISCOUNTS — DATE fields */
  'discounts' => [
    'pk' => 'discount_id',
    'table' => 'discounts',
    'title' => 'Discounts',
    'allow_new' => true,
    'fields' => [
      ['name' => 'discount_code', 'label' => 'Code', 'type' => 'text'],
      ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
      ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
      ['name' => 'discount_type', 'label' => 'Type (percentage|fixed_amount)', 'type' => 'text', 'required' => true],
      ['name' => 'value', 'label' => 'Value', 'type' => 'number', 'step' => '0.01', 'required' => true],
      ['name' => 'start_date', 'label' => 'Start date', 'type' => 'date'],
      ['name' => 'end_date', 'label' => 'End date', 'type' => 'date'],
      ['name' => 'is_active', 'label' => 'Active (0/1)', 'type' => 'number'],
    ],
    'list_sql' => "
      SELECT discount_id, discount_code, name, description, discount_type,
             value, is_active, start_date, end_date
      FROM discounts
      ORDER BY COALESCE(updated_at, created_at, discount_id) DESC",
    'columns' => [
      ['key' => 'discount_id', 'label' => 'ID'],
      ['key' => 'discount_code', 'label' => 'Code'],
      ['key' => 'name', 'label' => 'Name'],
      ['key' => 'description', 'label' => 'Description'],
      ['key' => 'discount_type', 'label' => 'Type'],
      ['key' => 'value', 'label' => 'Value'],
      ['key' => 'is_active', 'label' => 'Active'],
      ['key' => 'start_date', 'label' => 'Start'],
      ['key' => 'end_date', 'label' => 'End'],
    ],
  ],

  /* BOOK DISCOUNTS */
  'book_discounts' => [
    'pk' => 'book_discount_id',
    'table' => 'book_discounts',
    'title' => 'Book Discounts',
    'allow_new' => true,
    'fields' => [
      ['name' => 'book_id', 'label' => 'Book ID', 'type' => 'number', 'required' => true],
      ['name' => 'discount_type', 'label' => 'Type (percentage|fixed_amount)', 'type' => 'text', 'required' => true],
      ['name' => 'value', 'label' => 'Value', 'type' => 'number', 'step' => '0.01', 'required' => true],
      ['name' => 'start_date', 'label' => 'Start date', 'type' => 'date'],
      ['name' => 'end_date', 'label' => 'End date', 'type' => 'date'],
      ['name' => 'is_active', 'label' => 'Active (0/1)', 'type' => 'number'],
    ],
    'list_sql' => "
      SELECT bd.book_discount_id, bd.book_id,
             bd.discount_type, bd.value, bd.start_date, bd.end_date, bd.is_active
      FROM book_discounts bd
      LEFT JOIN books b ON b.book_id = bd.book_id
      ORDER BY COALESCE(bd.updated_at, bd.created_at, bd.book_discount_id) DESC",
    'columns' => [
      ['key' => 'book_discount_id', 'label' => 'Discount ID'],
      ['key' => 'book_id', 'label' => 'Book ID'],
      ['key' => 'discount_type', 'label' => 'Type'],
      ['key' => 'value', 'label' => 'Value'],
      ['key' => 'start_date', 'label' => 'Start'],
      ['key' => 'end_date', 'label' => 'End'],
      ['key' => 'is_active', 'label' => 'Active'],
    ],
  ],

  /* ORDERS — manage statuses only */
  'orders' => [
    'pk' => 'order_id',
    'table' => 'orders',
    'title' => 'Orders',
    'allow_new' => false,
    'fields' => [
      ['name' => 'order_status', 'label' => 'Order status (pending|paid|delivered|canceled)', 'type' => 'text'],
      ['name' => 'payment_status', 'label' => 'Payment status (UNPAID|PAID|REFUNDED…)', 'type' => 'text'],
    ],
    'list_sql' => "
      SELECT o.order_id, COALESCE(u.email, CONCAT('user#',o.user_id)) AS email,
             o.full_name, o.total,
             COALESCE(o.order_status,'pending') AS order_status,
             o.payment_status, o.order_date
      FROM orders o
      LEFT JOIN users u ON u.user_id=o.user_id
      ORDER BY o.order_date DESC",
    'columns' => [
      ['key' => 'order_id', 'label' => 'ID'],
      ['key' => 'email', 'label' => 'User'],
      ['key' => 'full_name', 'label' => 'Name'],
      ['key' => 'total', 'label' => 'Total'],
      ['key' => 'order_status', 'label' => 'Order Status'],
      ['key' => 'payment_status', 'label' => 'Payment'],
      ['key' => 'order_date', 'label' => 'Date'],
    ],
  ],

  /* REVIEWS — edit only */
  'reviews' => [
    'pk' => 'review_id',
    'table' => 'reviews',
    'title' => 'Reviews',
    'allow_new' => false,
    'fields' => [
      ['name' => 'rating', 'label' => 'Rating (1–5)', 'type' => 'number'],
      ['name' => 'comment', 'label' => 'Comment', 'type' => 'textarea'],
    ],
    'list_sql' => "
      SELECT r.review_id, u.email, b.title, r.rating, r.comment, r.created_at
      FROM reviews r
      JOIN users u ON u.user_id=r.user_id
      JOIN books b ON b.book_id=r.book_id
      ORDER BY r.created_at DESC",
    'columns' => [
      ['key' => 'review_id', 'label' => 'ID'],
      ['key' => 'email', 'label' => 'User'],
      ['key' => 'title', 'label' => 'Book'],
      ['key' => 'rating', 'label' => 'Rating'],
      ['key' => 'comment', 'label' => 'Comment'],
      ['key' => 'created_at', 'label' => 'Date'],
    ],
  ],
];

/* --- Router (aliases) --- */
$rawEntity = strtolower(trim($_GET['entity'] ?? 'discounts'));
$entityAliases = [
  'user' => 'users',
  'book' => 'books',
  'order' => 'orders',
  'review' => 'reviews',
  'discount' => 'discounts',
  'bookdiscount' => 'book_discounts',
  'bookdiscounts' => 'book_discounts',
  'book-discount' => 'book_discounts',
  'book-discounts' => 'book_discounts',
  'book_discount' => 'book_discounts',
];
$entity = $entityAliases[$rawEntity] ?? $rawEntity;

if (!isset($entities[$entity])) {
  http_response_code(404);
  echo "Unknown entity. Try one of: " . implode(', ', array_keys($entities));
  exit;
}

$cfg = $entities[$entity];
$pk = $cfg['pk'];
$title = $cfg['title'];
$table = $cfg['table'];
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* ---------- show-all ---------- */
$showAll = (($_GET['show'] ?? '') === 'all');
$limitClause = $showAll ? '' : ' LIMIT 10';

/* ---------- DELETE ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_delete']) && $id) {
  try {
    $st = $conn->prepare("DELETE FROM `$table` WHERE `$pk`=?");
    $st->execute([$id]);
    header("Location: crud.php?entity=$entity");
    exit;
  } catch (Throwable $e) {
    error_log("[CRUD][$table.delete] " . $e->getMessage());
    echo '<div style="padding:16px;color:#fff;background:#8b1d1d;">Delete failed. See PHP error log.</div>';
  }
}

/* ---------- INSERT / UPDATE ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['_delete'])) {
  $cols = [];
  $vals = [];
  $pars = [];
  foreach ($cfg['fields'] as $f) {
    $name = $f['name'];
    $type = $f['type'] ?? 'text';
    $cols[] = "`$name`";
    $raw = $_POST[$name] ?? null;
    if ($raw === '') $raw = null;
    if ($type === 'datetime-local') $raw = normalize_datetime($raw);
    if ($type === 'number' && $raw !== null) {
      $isInt = preg_match('/_id$|^(is_|age$|stock_quantity$)/', $name);
      $raw = $isInt ? (int)$raw : (float)$raw;
    }
    if (in_array($entity, ['discounts', 'book_discounts'], true) && $name === 'is_active' && $raw === null) $raw = 1;
    $vals[] = '?';
    $pars[] = $raw;
  }

  if ($id) {
    $sets = [];
    foreach ($cfg['fields'] as $f) {
      $sets[] = "`{$f['name']}`=?";
    }
    $pars[] = $id;
    if (!qx($conn, "UPDATE `$table` SET " . implode(',', $sets) . " WHERE `$pk`=?", $pars, $table . '.update')) {
      die('<div style="padding:16px;color:#fff;background:#8b1d1d;">Update failed. See PHP error log.</div>');
    }
    header("Location: crud.php?entity=$entity");
    exit;
  } else {
    if (!empty($cfg['allow_new'])) {
      if (!qx($conn, "INSERT INTO `$table` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")", $pars, $table . '.insert')) {
        die('<div style="padding:16px;color:#fff;background:#8b1d1d;">Insert failed. See PHP error log.</div>');
      }
      header("Location: crud.php?entity=$entity");
      exit;
    } else {
      echo '<div style="padding:16px;color:#fff;background:#8b1d1d;">Insert disabled for this entity.</div>';
    }
  }
}

/* ---------- Load rows ---------- */
$listRows = load_list($conn, $cfg, $limitClause);
$editing = null;
if ($action === 'edit' && $id) {
  $rows = qa($conn, "SELECT * FROM `$table` WHERE `$pk`=?", [$id], $table . '.load_one');
  $editing = $rows ? $rows[0] : null;
}
$totalCount = (int)qv($conn, "SELECT COUNT(*) FROM `$table`");
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Manage <?= esc($title) ?> — Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg1: #0f1029;
      --bg2: #2b1e52;
      --panel: #0c0f28;
      --panel2: #111433;
      --ink: #e8eaf2;
      --border: rgba(255, 255, 255, .1);
      --hot: #ff2d8f;
      --hot2: #ff6ea9;
    }

    body {
      margin: 0;
      font-family: "Inter", system-ui;
      color: var(--ink);
      background:
        radial-gradient(900px 600px at 85% 10%, rgba(108, 0, 255, .25), transparent 60%),
        radial-gradient(800px 600px at 15% 90%, rgba(255, 45, 143, .25), transparent 60%),
        linear-gradient(160deg, var(--bg1), var(--bg2));
      min-height: 100vh
    }

    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      background: rgba(0, 0, 0, .12);
      backdrop-filter: blur(6px)
    }

    .pill {
      border-radius: 999px;
      background: rgba(255, 255, 255, .06);
      border: 1px solid var(--border);
      color: var(--ink)
    }

    .section-card {
      border: 1px solid var(--border);
      background: rgba(255, 255, 255, .04);
      border-radius: 16px
    }

    .data-table {
      --bs-table-bg: transparent;
      --bs-table-color: #e9edf7;
      --bs-table-hover-bg: rgba(255, 255, 255, .06);
      --bs-table-hover-color: #fff;
      color: var(--bs-table-color);
      background: transparent;
      margin-bottom: 0
    }

    .data-table thead th {
      background: rgba(255, 255, 255, .06);
      color: #fff;
      border-bottom: 1px solid var(--border);
      white-space: nowrap
    }

    .btn-grad {
      background: linear-gradient(135deg, var(--hot), var(--hot2));
      color: #0f1029;
      border: 0
    }
  </style>
</head>

<body>
  <div class="topbar">
    <div class="d-flex align-items-center gap-2">
      <a href="dashboard.php" class="text-decoration-none text-light fw-bold">BookNest Admin</a>
      <span class="text-secondary">Manage <?= esc($title) ?></span>
    </div>
    <div><a href="dashboard.php" class="btn btn-sm btn-outline-light pill">Back to Dashboard</a></div>
  </div>

  <main class="p-4">
    <div class="section-card p-3">
      <div class="d-flex align-items-center justify-content-between">
        <h5 class="m-0"><?= esc($title) ?></h5>
        <div class="d-flex gap-2">
          <?php if ($showAll): ?>
            <a class="btn btn-sm btn-outline-light pill" href="crud.php?entity=<?= esc($entity) ?>">
              <i class="bi bi-chevron-left me-1"></i>Back (latest 10)
            </a>
          <?php elseif ($totalCount > 10): ?>
            <a class="btn btn-sm btn-grad pill" href="crud.php?entity=<?= esc($entity) ?>&show=all">
              <i class="bi bi-list-ul me-1"></i>Show all
            </a>
          <?php endif; ?>
          <?php if (!empty($cfg['allow_new'])): ?>
            <a class="btn btn-sm btn-primary pill" href="crud.php?entity=<?= esc($entity) ?>&action=new">
              <i class="bi bi-plus-circle me-1"></i>Add New
            </a>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($action === 'new' || $action === 'edit'): ?>
        <form class="mt-3" method="post">
          <div class="row g-3">
            <?php foreach ($cfg['fields'] as $f):
              $name = $f['name'];
              $label = $f['label'];
              $type = $f['type'];
              $val = $editing[$name] ?? '';
              $required = !empty($f['required']) ? 'required' : '';
              $step = !empty($f['step']) ? ' step="' . $f['step'] . '"' : '';
            ?>
              <div class="col-12 col-md-6">
                <label class="form-label"><?= esc($label) ?></label>
                <?php if ($entity === 'discounts' && $name === 'discount_type'): ?>
                  <select name="discount_type" class="form-select pill" required>
                    <?php foreach (['percentage', 'fixed_amount'] as $opt): ?>
                      <option value="<?= $opt ?>" <?= (($val ?: 'percentage') === $opt ? 'selected' : '') ?>><?= ucfirst(str_replace('_', ' ', $opt)) ?></option>
                    <?php endforeach; ?>
                  </select>
                <?php elseif ($type === 'textarea'): ?>
                  <textarea name="<?= esc($name) ?>" class="form-control pill" rows="3" <?= $required ?>><?= esc($val) ?></textarea>
                <?php else: ?>
                  <input type="<?= esc($type) ?>" name="<?= esc($name) ?>" value="<?= esc($val) ?>" class="form-control pill" <?= $required . $step ?>>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="mt-3 d-flex gap-2">
            <button class="btn btn-grad pill" type="submit"><?= $editing ? 'Update' : 'Create' ?></button>
            <a href="crud.php?entity=<?= esc($entity) ?>" class="btn btn-outline-light pill">Cancel</a>
            <?php if ($editing): ?>
              <button class="btn btn-danger pill ms-auto" name="_delete" value="1" onclick="return confirm('Delete this record?')">
                <i class="bi bi-trash3 me-1"></i>Delete
              </button>
            <?php endif; ?>
          </div>
        </form>
      <?php else: ?>
        <div class="table-responsive mt-3">
          <table class="table table-hover table-sm align-middle data-table">
            <thead>
              <tr>
                <?php foreach ($cfg['columns'] as $c): ?>
                  <th><?= esc($c['label']) ?></th>
                <?php endforeach; ?>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($listRows as $r): ?>
                <tr>
                  <?php foreach ($cfg['columns'] as $c): $key = $c['key']; ?>
                    <td><?= esc($r[$key] ?? '') ?></td>
                  <?php endforeach; ?>
                  <td class="text-end">
                    <a class="btn btn-sm btn-primary rounded-pill" href="crud.php?entity=<?= esc($entity) ?>&action=edit&id=<?= (int)$r[$pk] ?>">
                      <i class="bi bi-pencil-square me-1"></i>Edit
                    </a>
                    <form class="d-inline" method="post" action="crud.php?entity=<?= esc($entity) ?>&action=edit&id=<?= (int)$r[$pk] ?>" onsubmit="return confirm('Delete this record?')">
                      <button class="btn btn-sm btn-danger rounded-pill" name="_delete" value="1">
                        <i class="bi bi-trash3 me-1"></i>Delete
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$listRows): ?>
                <tr>
                  <td colspan="<?= count($cfg['columns']) + 1 ?>" class="text-secondary">No records.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>