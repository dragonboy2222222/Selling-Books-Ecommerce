<?php
// viewProduct.php
if (!isset($_SESSION)) {
  session_start();
}
if (empty($_SESSION['loginSuccess'])) {
  header('Location: login.php');
  exit;
}
require_once "dbconnect.php";

/* flash messages */
$flash = '';
if (!empty($_SESSION['message'])) {
  $flash = $_SESSION['message'];
  unset($_SESSION['message']);
}
if (!empty($_SESSION['deleteSuccess'])) {
  $flash = $_SESSION['deleteSuccess'];
  unset($_SESSION['deleteSuccess']);
}

/* load categories (optional future use) */
$categories = [];
try {
  $stmt = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
  $categories = $stmt->fetchAll();
} catch (PDOException $e) {
}

/* filters */
$qParam     = trim($_GET['q'] ?? ($_GET['tsearch'] ?? '')); // supports both ?q and ?tsearch
$categoryId = (int)($_GET['category'] ?? 0);
$hasFilters = ($qParam !== '' || $categoryId > 0);

/* query base */
$sql = "SELECT 
          b.book_id,
          b.title,
          b.price,
          b.stock_quantity,
          b.description,
          b.image_url,
          CONCAT(a.first_name, ' ', a.last_name) AS author,
          (SELECT GROUP_CONCAT(c.category_name SEPARATOR ', ')
             FROM book_categories bc
             JOIN categories c ON c.category_id = bc.category_id
            WHERE bc.book_id = b.book_id) AS categories
        FROM books b
        JOIN authors a ON a.author_id = b.author_id";

$params = [];
$where  = [];

/* search: title OR author OR category name */
if ($qParam !== '') {
  $where[] = "( b.title LIKE ?
                OR CONCAT(a.first_name,' ',a.last_name) LIKE ?
                OR EXISTS (
                     SELECT 1
                       FROM book_categories bc
                       JOIN categories c ON c.category_id = bc.category_id
                      WHERE bc.book_id = b.book_id
                        AND c.category_name LIKE ?
                )
              )";
  $like = "%{$qParam}%";
  $params[] = $like;
  $params[] = $like;
  $params[] = $like;
}

/* explicit category filter (optional) */
if ($categoryId > 0) {
  $where[]  = "EXISTS (SELECT 1 FROM book_categories bc WHERE bc.book_id = b.book_id AND bc.category_id = ?)";
  $params[] = $categoryId;
}

if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY b.created_at DESC";

$books = [];
try {
  $stmt = $conn->prepare($sql);
  $stmt->execute($params);
  $books = $stmt->fetchAll();
} catch (PDOException $e) {
}

/* truncate helper */
function bn_trunc($s, $n = 160)
{
  $s = (string)$s;
  if (function_exists('mb_strlen')) return mb_strlen($s) > $n ? mb_substr($s, 0, $n) . '…' : $s;
  return strlen($s) > $n ? substr($s, 0, $n) . '…' : $s;
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>BookNest • Books</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* White text in the table */
    .table,
    .table thead,
    .table thead th,
    .table tbody td,
    .table tbody th {
      color: #fff !important;
    }

    :root {
      --bg1: #0f1029;
      --bg2: #2b1e52;
      --panel: #0c0f28;
      --panel2: #111433;
      --ink: #ffffff;
      --muted: #b9c2d9;
      --border: rgba(255, 255, 255, .12);
      --accent: #5c6cff;
      --hot: #ff2d8f;
      --hot2: #ff6ea9;
    }

    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color: var(--ink);
      background:
        radial-gradient(900px 600px at 85% 10%, rgba(108, 0, 255, .25), transparent 60%),
        radial-gradient(800px 600px at 15% 90%, rgba(255, 45, 143, .25), transparent 60%),
        linear-gradient(160deg, var(--bg1), var(--bg2));
      min-height: 100vh;
      overflow-x: hidden;
    }

    .page {
      padding: 16px;
    }

    .glass {
      background: linear-gradient(180deg, rgba(255, 255, 255, .07), rgba(255, 255, 255, .03));
      border: 1px solid var(--border);
      border-radius: 18px;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 20px 50px rgba(0, 0, 0, .35);
      overflow: visible;
    }

    .filter-card {
      padding: 18px;
      position: sticky;
      top: 12px;
    }

    .filter-card .title {
      font-weight: 800;
      margin-bottom: 10px;
    }

    .form-control,
    .form-select {
      color: #fff !important;
      background: rgba(231, 224, 224, 0.08) !important;
      border: 1px solid var(--border) !important;
      border-radius: 12px !important;
    }

    .form-select option {
      background: #0c0f28;
      color: #fff;
    }

    .form-control::placeholder {
      color: #e7e9f5;
      opacity: .8
    }

    .form-control:focus,
    .form-select:focus {
      border-color: rgba(92, 108, 255, .75) !important;
      box-shadow: 0 0 0 .2rem rgba(92, 108, 255, .25) !important;
      color: #fff !important;
      position: relative;
      z-index: 1051;
    }

    .btn-grad {
      background: linear-gradient(135deg, var(--hot), var(--hot2));
      color: #0f1029;
      border: 0;
      border-radius: 999px;
      font-weight: 700;
    }

    .data-card {
      padding: 18px;
    }

    .table thead {
      color: var(--muted);
    }

    .table>:not(caption)>*>* {
      background-color: transparent;
      border-bottom-color: var(--border);
    }

    td {
      word-break: break-word;
    }

    .cover {
      width: 90px;
      height: 90px;
      object-fit: cover;
      border-radius: 10px;
      border: 1px solid var(--border);
    }

    .btn-action {
      --bs-btn-padding-y: .25rem;
      --bs-btn-padding-x: .6rem;
      --bs-btn-font-size: .85rem;
      border-radius: 999px;
      font-weight: 600;
    }

    .btn-edit {
      color: #fff;
      border: 1px solid rgba(255, 255, 255, .35);
    }

    .btn-edit:hover {
      background: rgba(255, 255, 255, .12);
      color: #fff;
    }

    .btn-del {
      color: #fff;
      border: 1px solid rgba(255, 0, 80, .45);
    }

    .btn-del:hover {
      background: rgba(255, 0, 80, .18);
      color: #fff;
    }

    @media (max-width: 991.98px) {
      .filter-card {
        position: static;
      }
    }

    @media (max-width: 575.98px) {
      .col-desc {
        display: none;
      }
    }
  </style>
</head>

<body>
  <div class="container-fluid p-0">
    <div class="row"><?php require_once "navbarcopy.php"; ?></div>

    <div class="page container-fluid">
      <?php if ($flash): ?>
        <div class="alert alert-success glass border-0 mb-3"><?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>

      <div class="row g-3">
        <!-- FILTERS -->
        <div class="col-12 col-lg-3">
          <div class="glass filter-card">
            <div class="title">Filter Books</div>
            <form method="get" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
              <div class="mb-3">
                <label class="form-label">Keyword</label>
                <input type="text" class="form-control" name="q"
                  placeholder="Title, Author, or Category"
                  value="<?= htmlspecialchars($qParam) ?>">
              </div>
              <div class="d-grid gap-2">
                <button class="btn btn-grad" type="submit">
                  <i class="bi bi-search me-1"></i> Search
                </button>
                <?php if ($hasFilters): ?>
                  <a class="btn btn-outline-light" href="<?= htmlspecialchars(parse_url($_SERVER['PHP_SELF'], PHP_URL_PATH)) ?>">
                    <i class="bi bi-x-circle me-1"></i> Cancel / Show all
                  </a>
                <?php endif; ?>
              </div>
            </form>
          </div>
        </div>

        <!-- TABLE -->
        <div class="col-12 col-lg-9">
          <div class="glass data-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="m-0 fw-bold">Books</h5>
              <a class="btn btn-sm btn-grad" href="insertBook.php">
                <i class="bi bi-plus-circle me-1"></i>Add Book
              </a>
            </div>

            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Categories</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th class="col-desc">Description</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($books as $b): ?>
                    <tr>
                      <td>
                        <?php if (!empty($b['image_url'])): ?>
                          <img src="<?= htmlspecialchars($b['image_url']) ?>" class="cover" alt="">
                        <?php else: ?>
                          <span class="badge text-bg-secondary">No image</span>
                        <?php endif; ?>
                      </td>
                      <td class="fw-semibold"><?= htmlspecialchars($b['title']) ?></td>
                      <td><?= htmlspecialchars($b['author']) ?></td>
                      <td><?= htmlspecialchars($b['categories'] ?? '') ?></td>
                      <td><?= htmlspecialchars($b['price']) ?></td>
                      <td><?= (int)$b['stock_quantity'] ?></td>
                      <td class="col-desc" style="max-width:360px;"><?= htmlspecialchars(bn_trunc($b['description'] ?? '', 160)) ?></td>
                      <td class="text-end">
                        <a href="updateBook.php?id=<?= (int)$b['book_id'] ?>" class="btn btn-action btn-edit">
                          <i class="bi bi-pencil-square me-1"></i>Edit
                        </a>
                        <a href="deletebook.php?id=<?= (int)$b['book_id'] ?>" class="btn btn-action btn-del ms-2"
                          onclick="return confirm('Delete this book?');">
                          <i class="bi bi-trash3 me-1">Delete</i>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if (!$books): ?>
                    <tr>
                      <td colspan="8" class="text-secondary">No books found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>