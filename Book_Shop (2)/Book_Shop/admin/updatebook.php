<?php
// updateBook.php
require_once "dbconnect.php";
if (!isset($_SESSION)) {
  session_start();
}
if (empty($_SESSION['loginSuccess'])) {
  header('Location: login.php');
  exit;
}

/* Helper for older PHP versions (PHP 8 has str_starts_with) */
if (!function_exists('starts_with')) {
  function starts_with($haystack, $needle)
  {
    return strpos((string)$haystack, (string)$needle) === 0;
  }
}

/* -------- Resolve book id from GET/POST -------- */
$bookId = 0;
if (isset($_GET['id']))       $bookId = (int)$_GET['id'];
elseif (isset($_GET['eid']))  $bookId = (int)$_GET['eid'];
elseif (isset($_POST['book_id'])) $bookId = (int)$_POST['book_id'];

if ($bookId <= 0) {
  header("Location: viewproduct.php");
  exit;
}

/* -------- Load dropdown data -------- */
$authors = $categories = [];
try {
  $stmt = $conn->query("SELECT author_id, first_name, last_name FROM authors ORDER BY last_name, first_name");
  $authors = $stmt->fetchAll();

  $stmt = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
  $categories = $stmt->fetchAll();
} catch (PDOException $e) {
  echo $e->getMessage();
}

/* -------- Load current book (with 1 category if any) -------- */
$book = null;
$prevCategoryId = null;
try {
  $sql = "SELECT 
                b.book_id, b.title, b.author_id, b.isbn, b.price, b.stock_quantity,
                b.description, b.image_url,
                c.category_id, c.category_name
            FROM books b
            LEFT JOIN book_categories bc ON bc.book_id = b.book_id
            LEFT JOIN categories c       ON c.category_id = bc.category_id
            WHERE b.book_id = ?
            LIMIT 1";
  $st = $conn->prepare($sql);
  $st->execute([$bookId]);
  $book = $st->fetch();
  if (!$book) {
    header("Location: viewproduct.php");
    exit;
  }
  $prevCategoryId = $book['category_id'] ?? null;
} catch (PDOException $e) {
  echo $e->getMessage();
}

/* -------- Update handler -------- */
if (isset($_POST['updateBtn'])) {
  $title        = trim($_POST['title'] ?? '');
  $author_id    = (int)($_POST['author_id'] ?? 0);
  $isbn         = trim($_POST['isbn'] ?? '');
  $price        = $_POST['price'] ?? '';
  $stock_qty    = (int)($_POST['stock_quantity'] ?? 0);
  $description  = trim($_POST['description'] ?? '');
  $category_id  = (int)($_POST['category_id'] ?? 0);
  $image_url_in = trim($_POST['image_url'] ?? '');

  if ($title === '' || $author_id <= 0 || $price === '' || $stock_qty < 0) {
    $error = "Please fill all required fields correctly.";
  } else {
    try {
      // current image (to keep or replace)
      $q = $conn->prepare("SELECT image_url FROM books WHERE book_id = ?");
      $q->execute([$bookId]);
      $cur = $q->fetch();
      $currentImageUrl = $cur['image_url'] ?? null;
      $finalImageUrl   = $currentImageUrl;

      // If a file uploaded, prefer it
      if (!empty($_FILES['image_file']['name']) && is_uploaded_file($_FILES['image_file']['tmp_name'])) {
        $uploadDir = __DIR__ . '/uploads/books';
        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0775, true);
        }
        $ext  = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
        $safe = 'book_' . time() . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . strtolower($ext) : '');
        $fsPath  = $uploadDir . '/' . $safe;
        $relPath = 'uploads/books/' . $safe;

        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $fsPath)) {
          // remove old local file if it was local
          if ($currentImageUrl && starts_with($currentImageUrl, 'uploads/')) {
            $oldFs = __DIR__ . '/' . $currentImageUrl;
            if (is_file($oldFs)) {
              @unlink($oldFs);
            }
          }
          $finalImageUrl = $relPath;
        } else {
          $error = "File upload failed.";
        }
      } elseif ($image_url_in !== '') {
        // use typed URL/path if provided
        $finalImageUrl = $image_url_in;
      }

      if (empty($error)) {
        $conn->beginTransaction();

        // Update books (exact columns)
        $sql = "UPDATE books
                        SET title=?, author_id=?, isbn=?, price=?, stock_quantity=?, description=?, image_url=?
                        WHERE book_id=?";
        $st = $conn->prepare($sql);
        $st->execute([
          $title,
          $author_id,
          ($isbn !== '' ? $isbn : null),
          $price,
          $stock_qty,
          ($description !== '' ? $description : null),
          $finalImageUrl,
          $bookId
        ]);

        // Update single category link
        $del = $conn->prepare("DELETE FROM book_categories WHERE book_id = ?");
        $del->execute([$bookId]);
        if ($category_id > 0) {
          $ins = $conn->prepare("INSERT INTO book_categories (book_id, category_id) VALUES (?, ?)");
          $ins->execute([$bookId, $category_id]);
        }

        $conn->commit();
        $_SESSION['message'] = "Book #$bookId updated successfully.";
        header("Location: viewproduct.php");
        exit;
      }
    } catch (Throwable $e) {
      if ($conn->inTransaction()) {
        $conn->rollBack();
      }
      $error = $e->getMessage();
    }
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Update Book • BookNest</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
      --muted: #9aa3b2;
      --border: rgba(255, 255, 255, .12);
      --hot: #ff2d8f;
      --hot2: #ff6ea9;
    }

    * {
      box-sizing: border-box
    }

    html,
    body {
      height: 100%
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
    }

    .page {
      min-height: calc(100vh - 70px);
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding: 32px 16px 48px;
    }

    .glass-card {
      width: 100%;
      max-width: 1000px;
      background: linear-gradient(180deg, rgba(255, 255, 255, .06), rgba(255, 255, 255, .03));
      border: 1px solid var(--border);
      border-radius: 20px;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 20px 50px rgba(0, 0, 0, .35);
      padding: 26px;
    }

    .title {
      font-weight: 800;
      letter-spacing: .2px;
      margin: 4px 0 18px;
      background: linear-gradient(135deg, #ffffff, #cfd6ff);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      font-size: clamp(1.2rem, 2.2vw, 1.5rem);
      text-align: center;
    }

    .form-label {
      color: var(--muted);
      font-weight: 600;
      font-size: .9rem;
    }

    .form-control,
    .form-select {
      background: rgba(255, 255, 255, .06) !important;
      border: 1px solid var(--border) !important;
      color: var(--ink) !important;
      border-radius: 12px !important;
      padding: .7rem .9rem !important;
    }

    .form-select option {
      background-color: #0c0f28;
      color: #e8eaf2;
    }

    .form-control::placeholder {
      color: #c9cfe6;
      opacity: .6
    }

    .form-control:focus,
    .form-select:focus {
      border-color: rgba(92, 108, 255, .65) !important;
      box-shadow: 0 0 0 .2rem rgba(92, 108, 255, .15) !important;
    }

    .cover {
      width: 130px;
      height: 180px;
      object-fit: cover;
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: 0 2px 10px rgba(0, 0, 0, .25);
    }

    .btn-grad {
      background: linear-gradient(135deg, var(--hot), var(--hot2));
      color: #0f1029;
      border: 0;
      border-radius: 999px;
      font-weight: 700;
      padding: .65rem 1rem;
    }

    .btn-grad:hover {
      filter: brightness(.98);
      transform: translateY(-1px);
    }
  </style>
</head>

<body>
  <div class="container-fluid p-0">
    <div class="row"><?php require_once "navbarcopy.php"; ?></div>

    <div class="page">
      <div class="glass-card">
        <h1 class="title">Update Book ID-<?= htmlspecialchars($bookId) ?></h1>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger mb-3"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- enctype is required for file uploads -->
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="book_id" value="<?= (int)$bookId ?>">

          <div class="row g-4">
            <!-- LEFT -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label">Title</label>
                <input class="form-control" name="title" value="<?= htmlspecialchars($book['title'] ?? '') ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Author</label>
                <select name="author_id" class="form-select" required>
                  <option value="">Choose Author</option>
                  <?php foreach ($authors as $a): ?>
                    <option value="<?= (int)$a['author_id'] ?>"
                      <?= ((int)($book['author_id'] ?? 0) === (int)$a['author_id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                  <option value="">Choose Category</option>
                  <?php foreach ($categories as $c): ?>
                    <option value="<?= (int)$c['category_id'] ?>"
                      <?= ($prevCategoryId && (int)$prevCategoryId === (int)$c['category_id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($c['category_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Price</label>
                <input class="form-control" type="number" step="0.01" name="price"
                  value="<?= htmlspecialchars($book['price'] ?? '') ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Quantity (stock)</label>
                <input class="form-control" type="number" min="0" name="stock_quantity"
                  value="<?= (int)($book['stock_quantity'] ?? 0) ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label">ISBN (optional)</label>
                <input class="form-control" name="isbn" value="<?= htmlspecialchars($book['isbn'] ?? '') ?>">
              </div>
            </div>

            <!-- RIGHT -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" rows="5" name="description"><?= htmlspecialchars($book['description'] ?? '') ?></textarea>
              </div>

              <div class="mb-2">
                <label class="form-label d-block">Current Cover</label>
                <?php if (!empty($book['image_url'])): ?>
                  <img src="<?= htmlspecialchars($book['image_url']) ?>" alt="cover" class="cover" id="currentCover">
                <?php else: ?>
                  <span class="badge bg-secondary">No image</span>
                <?php endif; ?>
              </div>

              <div class="mb-3">
                <label class="form-label">Upload New Image (optional)</label>
                <input class="form-control" type="file" name="image_file" accept="image/*" id="image_file">
              </div>

              <div class="mb-3">
                <img id="preview" class="cover d-none" alt="preview">
              </div>

              <!-- FIXED BUTTONS: same design -->
              <div class="d-flex flex-wrap gap-2 mt-2">
                <button class="btn btn-grad" name="updateBtn">
                  <i class="bi bi-save me-1"></i>Update
                </button>
                <a class="btn btn-grad" href="viewproduct.php">
                  <i class="bi bi-x-circle me-1"></i>Cancel
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script>
    // live preview for uploaded image
    const fileInput = document.getElementById('image_file');
    const preview = document.getElementById('preview');
    if (fileInput) {
      fileInput.addEventListener('change', function() {
        const f = this.files && this.files[0];
        if (!f) {
          preview.classList.add('d-none');
          preview.src = '';
          return;
        }
        const url = URL.createObjectURL(f);
        preview.src = url;
        preview.classList.remove('d-none');
      });
    }
  </script>
</body>

</html>