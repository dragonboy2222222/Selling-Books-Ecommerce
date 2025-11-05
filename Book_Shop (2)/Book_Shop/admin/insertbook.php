<?php
// insertBook.php
require_once "dbconnect.php";
if (!isset($_SESSION)) {
  session_start();
}


$isInAdmin = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false);
$DASH_URL  = $isInAdmin ? 'dashboard.php' : 'admin/dashboard.php';



/* Load authors and categories */
$authors = $categories = [];
try {
  $stmt = $conn->query("SELECT author_id, first_name, last_name FROM authors ORDER BY last_name, first_name");
  $authors = $stmt->fetchAll();
  $stmt = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
  $categories = $stmt->fetchAll();
} catch (PDOException $e) {
  echo $e->getMessage();
}

if (isset($_POST['insertBtn'])) {
  $title        = trim($_POST['title'] ?? '');
  $author_id    = (int)($_POST['author_id'] ?? 0);
  $isbn         = trim($_POST['isbn'] ?? '');
  $price        = $_POST['price'] ?? '';
  $stock_qty    = (int)($_POST['stock_quantity'] ?? 0);
  $description  = trim($_POST['description'] ?? '');
  $category_id  = (int)($_POST['category_id'] ?? 0);

  // IMAGE HANDLING (URL or FILE)
  $image_url    = null; // what we will store in DB (URL or relative path)
  $image_url_in = trim($_POST['image_url'] ?? ''); // from the URL field

  // If user uploaded a file, prefer that
  if (!empty($_FILES['image_file']['name']) && is_uploaded_file($_FILES['image_file']['tmp_name'])) {
    $uploadDir = __DIR__ . '/uploads/books';
    if (!is_dir($uploadDir)) {
      if (!mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
        die("Cannot create upload directory: $uploadDir");
      }
    }
    $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
    $safeName = 'book_' . time() . '_' . bin2hex(random_bytes(4)) . ($ext ? "." . strtolower($ext) : '');
    $destFs  = $uploadDir . '/' . $safeName;     // filesystem path
    $destRel = 'uploads/books/' . $safeName;     // relative path for DB & <img src>

    if (move_uploaded_file($_FILES['image_file']['tmp_name'], $destFs)) {
      $image_url = $destRel; // store relative path
    } else {
      $message = "File upload failed.";
    }
  } elseif ($image_url_in !== '') {
    $image_url = $image_url_in; // accept as-is (URL or relative path)
  }

  if ($title === '' || $author_id <= 0 || $price === '' || $stock_qty < 0) {
    $message = "Please fill all required fields correctly.";
  } else {
    try {
      // Insert into books (includes image_url)
      $sql = "INSERT INTO books (title, author_id, isbn, price, stock_quantity, description, image_url, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        $title,
        $author_id,
        ($isbn !== '' ? $isbn : null),
        $price,
        $stock_qty,
        ($description !== '' ? $description : null),
        $image_url // can be null, URL, or relative path
      ]);
      $bookId = $conn->lastInsertId();

      if ($category_id > 0) {
        $stmt2 = $conn->prepare("INSERT INTO book_categories (book_id, category_id) VALUES (?, ?)");
        $stmt2->execute([$bookId, $category_id]);
      }

      $_SESSION['message'] = "New book with id $bookId has been inserted successfully!";
      header("Location: viewproduct.php");
      exit;
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Insert Book • BookNest</title>
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
      max-width: 980px;
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
      background: rgba(200, 184, 184, 0.06) !important;
      border: 1px solid var(--border) !important;
      color: var(--ink) !important;
      border-radius: 12px !important;
      padding: .7rem .9rem !important;
    }

    .form-control::placeholder {
      color: #90939eff;
      opacity: .6
    }

    .form-control:focus,
    .form-select:focus {
      border-color: rgba(208, 210, 225, 0.65) !important;
      box-shadow: 0 0 0 .2rem rgba(92, 108, 255, .15) !important;
    }

    .hint {
      color: var(--muted);
      font-size: .85rem;
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

    .error {
      border-radius: 12px;
      background: rgba(255, 77, 109, .15);
      border: 1px solid rgba(255, 77, 109, .35);
      color: #ffd7de;
    }

    .preview {
      width: 100%;
      max-width: 160px;
      height: 220px;
      object-fit: cover;
      border-radius: 12px;
      border: 1px solid var(--border);
    }

    /* Dark-theme form controls — high contrast */
    .form-control,
    .form-select {
      color: #e8eaf2 !important;
      /* light text */
      background-color: #171a33 !important;
      /* dark bg */
      border: 1px solid rgba(255, 255, 255, .18) !important;
      border-radius: 12px !important;
    }

    .form-control::placeholder {
      color: #bfc6e6;
      opacity: .75;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: rgba(92, 108, 255, .65) !important;
      box-shadow: 0 0 0 .2rem rgba(92, 108, 255, .20) !important;
    }

    /* Make dropdown list itself dark (supported in Chrome/Edge/Firefox) */
    .form-select option {
      background-color: #0c0f28;
      color: #e8eaf2;
    }

    /* Selected/hover states (browser support varies but helps where available) */
    .form-select option:checked {
      background-color: #2b1e52;
      color: #ffffff;
    }

    .form-select option:hover {
      background-color: #1b1f3d;
      color: #ffffff;
    }

    /* Disabled control appearance */
    .form-select:disabled,
    .form-control:disabled {
      background-color: #202549 !important;
      color: #8e96b2 !important;
    }
  </style>
</head>

<body>
  <div class="container-fluid p-0">
    <!-- Keep your existing navbar -->
    <div class="row"><?php require_once "navbarcopy.php"; ?></div>

    <div class="page">
      <div class="glass-card">
        <h1 class="title">Insert Book</h1>

        <?php if (!empty($message)): ?>
          <div class="alert error py-2 px-3 mb-3">
            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($message) ?>
          </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="form" enctype="multipart/form-data">
          <div class="row g-4">
            <!-- LEFT -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" name="title" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Author</label>
                <select name="author_id" class="form-select" required>
                  <option value="">Choose Author</option>
                  <?php foreach ($authors as $a): ?>
                    <option value="<?= (int)$a['author_id'] ?>"><?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Category (optional)</label>
                <select name="category_id" class="form-select">
                  <option value="">Choose category</option>
                  <?php foreach ($categories as $c): ?>
                    <option value="<?= (int)$c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="6" placeholder="Short description…"></textarea>
              </div>
            </div>

            <!-- RIGHT -->
            <div class="col-12 col-lg-6">
              <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" name="price" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Quantity (stock)</label>
                <input type="number" class="form-control" name="stock_quantity" min="0" required>
              </div>

              <div class="mb-3">
                <label class="form-label">ISBN (optional)</label>
                <input type="text" class="form-control" name="isbn" placeholder="e.g., 978-...">
              </div>

              <div class="mb-2">
                <label class="form-label">Upload Image (optional)</label>
                <input type="file" class="form-control" name="image_file" accept="image/*" id="image_file">
              </div>

              <div class="mt-2">
                <img id="preview" class="preview d-none" alt="">
              </div>

              <div class="d-flex gap-2 mt-3">
                <button type="submit" name="insertBtn" class="btn btn-grad">
                  <i class="bi bi-plus-circle me-1"></i> Insert Book
                </button>

                <a href="<?= htmlspecialchars($DASH_URL) ?>" class="btn btn-grad">
                  <i class="bi bi-x-circle me-1"></i> Cancel
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
    // Simple client-side preview for uploaded image
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