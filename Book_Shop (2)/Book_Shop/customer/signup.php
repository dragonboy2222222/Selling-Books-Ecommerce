<?php
// signup.php — Customer Sign Up (BookNest auth card theme)
if (!isset($_SESSION)) {
  session_start();
}
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';

function str_bool($v)
{
  return $v ? 'true' : 'false';
}

// very light CSRF token
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$errors = [];
$ok     = false;

// preset (for sticky form)
$data = [
  'first_name' => '',
  'last_name'  => '',
  'gender'     => '',
  'email'      => '',
  'phone'      => '',
  'city'       => '',
  'age'        => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // ---------- get inputs ----------
  $csrf       = $_POST['csrf'] ?? '';
  if (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) {
    $errors[] = "Security token expired. Please reload and try again.";
  }

  $data['first_name'] = trim($_POST['first_name'] ?? '');
  $data['last_name']  = trim($_POST['last_name'] ?? '');
  $data['gender']     = strtolower(trim($_POST['gender'] ?? ''));
  $data['email']      = strtolower(trim($_POST['email'] ?? ''));
  $data['phone']      = trim($_POST['phone'] ?? '');
  $data['city']       = trim($_POST['city'] ?? '');
  $data['age']        = trim($_POST['age'] ?? '');
  $password           = $_POST['password'] ?? '';
  $password2          = $_POST['password2'] ?? '';

  // ---------- validate ----------
  if ($data['first_name'] === '') $errors[] = "First name is required.";
  if ($data['last_name']  === '') $errors[] = "Last name is required.";
  if (!in_array($data['gender'], ['male', 'female'], true)) $errors[] = "Gender must be male or female.";
  if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
  if ($password === '' || strlen($password) < 8) $errors[] = "Password must be 8+ characters.";
  if ($password !== $password2) $errors[] = "Passwords do not match.";
  if ($data['age'] !== '' && (!ctype_digit($data['age']) || (int)$data['age'] < 13 || (int)$data['age'] > 120)) {
    $errors[] = "Age must be a number between 13 and 120 (or leave blank).";
  }

  // ---------- check unique email ----------
  if (!$errors) {
    try {
      $q = $conn->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
      $q->execute([$data['email']]);
      if ($q->fetch()) {
        $errors[] = "This email is already registered.";
      }
    } catch (Throwable $e) {
      $errors[] = "Database error checking email.";
    }
  }

  // ---------- avatar upload (optional) ----------
  $avatarPath = null; // relative path to store in DB
  if (!$errors && isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['avatar'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
      $errors[] = "Upload failed (code {$file['error']}).";
    } else {
      $maxBytes = 2 * 1024 * 1024; // 2MB
      if ($file['size'] > $maxBytes) $errors[] = "Avatar must be ≤ 2MB.";

      // mime/type & extension check
      $finfo = new finfo(FILEINFO_MIME_TYPE);
      $mime  = $finfo->file($file['tmp_name']);
      $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
      if (!isset($allowed[$mime])) {
        $errors[] = "Avatar must be JPG, PNG, or GIF.";
      }

      if (!$errors) {
        $ext = $allowed[$mime];
        $dir = __DIR__ . '/uploads/avatars';
        if (!is_dir($dir)) {
          if (!mkdir($dir, 0775, true)) {
            $errors[] = "Cannot create upload directory.";
          }
        }
        if (!$errors && !is_writable($dir)) {
          $errors[] = "Upload directory is not writable.";
        }
        if (!$errors) {
          $filename = 'u_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
          $full     = $dir . '/' . $filename;
          if (!move_uploaded_file($file['tmp_name'], $full)) {
            $errors[] = "Failed to move uploaded file.";
          } else {
            $avatarPath = 'uploads/avatars/' . $filename; // store relative path for web
          }
        }
      }
    }
  }

  // ---------- insert user ----------
  if (!$errors) {
    try {
      $password_hash = password_hash($password, PASSWORD_BCRYPT);
      $ageVal = ($data['age'] === '') ? null : (int)$data['age'];
      $roleId = 1; // customer

      $sql = "INSERT INTO users
        (first_name, last_name, gender, email, phone, city, age, profile_image_url, password_hash, role_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        $data['first_name'],
        $data['last_name'],
        $data['gender'],
        $data['email'],
        $data['phone'] ?: null,
        $data['city']  ?: null,
        $ageVal,
        $avatarPath,
        $password_hash,
        $roleId
      ]);

      // Log them in or redirect them to login (we’ll redirect to login to match your flow)
      header("Location: login.php");
      exit;
    } catch (Throwable $e) {
      $errors[] = "Failed to create account. " . $e->getMessage();
    }
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Create Account • BookNest</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap, Icons, Inter -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    /* ==== BookNest Auth Card Theme (same as login) ==== */
    :root {
      --bg: #f6f7fb;
      --ink: #0f172a;
      --muted: #64748b;
      --border: rgba(2, 6, 23, .08);
      --surface: #ffffff;
      --primary: #1a73e8;
      --ring: rgba(26, 115, 232, .24);
      --radius: 14px;
      --shadow: 0 10px 24px rgba(2, 6, 23, .08);
    }

    html,
    body {
      height: 100%;
    }

    body {
      margin: 0;
      font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color: var(--ink);
      background:
        radial-gradient(900px 500px at 85% -10%, rgba(26, 115, 232, .08), transparent 60%),
        radial-gradient(700px 400px at 10% 110%, rgba(26, 115, 232, .06), transparent 60%),
        var(--bg);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px 12px;
    }

    .card-auth {
      width: 100%;
      max-width: 780px;
      /* wider than login to fit the grid */
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
    }

    .card-auth .card-body {
      padding: 26px 24px 22px;
    }

    .auth-title {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 800;
      font-size: 22px;
      letter-spacing: .2px;
      margin: 0;
    }

    .auth-sub {
      color: var(--muted);
      font-size: 14px;
      margin-top: 6px;
    }

    .form-label {
      font-weight: 600;
      margin-bottom: 6px;
    }

    .form-control,
    .form-select {
      height: 46px;
      border-radius: 12px;
      border: 1px solid var(--border);
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 .22rem var(--ring);
    }

    .pw-wrap {
      position: relative;
    }

    .pw-toggle {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      border: 0;
      background: transparent;
      color: #6b7280;
      width: 36px;
      height: 36px;
    }

    .btn-primary {
      background: var(--primary);
      border-color: var(--primary);
      border-radius: 999px;
      height: 46px;
      font-weight: 700;
    }

    .btn-primary:hover {
      filter: brightness(.95);
    }

    .avatar-preview {
      width: 42px;
      height: 42px;
      object-fit: cover;
      border-radius: 50%;
      border: 1px solid var(--border);
      display: none;
    }

    .auth-foot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      margin-top: 12px;
      font-size: 14px;
    }

    .auth-foot a {
      color: var(--primary);
      text-decoration: none;
    }

    .auth-foot a:hover {
      text-decoration: underline;
    }

    @media (max-width: 540px) {
      .card-auth .card-body {
        padding: 22px 16px 18px;
      }
    }
  </style>
</head>

<body>

  <div class="card card-auth">
    <div class="card-body">
      <h1 class="auth-title">
        Create Account
      </h1>


      <?php if ($errors): ?>
        <div class="alert alert-danger mt-3">
          <strong>Please fix the following:</strong>
          <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul>
        </div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" novalidate class="mt-3">
        <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">First name</label>
            <input class="form-control" name="first_name" value="<?= h($data['first_name']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Last name</label>
            <input class="form-control" name="last_name" value="<?= h($data['last_name']) ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Gender</label>
            <select class="form-select" name="gender" required>
              <option value="">Choose gender</option>
              <option value="male" <?= $data['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
              <option value="female" <?= $data['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
            </select>
          </div>

          <div class="col-md-8">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?= h($data['email']) ?>" placeholder="you@example.com" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input class="form-control" name="phone" value="<?= h($data['phone']) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">City</label>
            <input class="form-control" name="city" value="<?= h($data['city']) ?>">
          </div>
          <div class="col-md-2">
            <label class="form-label">Age</label>
            <input class="form-control" name="age" inputmode="numeric" value="<?= h($data['age']) ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Password</label>
            <div class="pw-wrap">
              <input id="pw1" type="password" class="form-control" name="password" required>
              <button type="button" class="pw-toggle" onclick="togglePw('pw1', this)" aria-label="Show password">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Confirm password</label>
            <div class="pw-wrap">
              <input id="pw2" type="password" class="form-control" name="password2" required>
              <button type="button" class="pw-toggle" onclick="togglePw('pw2', this)" aria-label="Show password">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">User profile image (optional)</label>
            <input class="form-control" type="file" name="avatar" accept="image/*" onchange="previewAvatar(this)">
            <div class="mt-2">
              <img id="avatarPrev" class="avatar-preview" alt="avatar preview">
            </div>
          </div>
        </div>

        <div class="auth-foot">
          <a href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Already have an account?</a>
          <button class="btn btn-primary px-4" type="submit">
            <i class="bi bi-person-plus-fill me-1"></i> Create Account
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function togglePw(id, btn) {
      const el = document.getElementById(id);
      const icon = btn.querySelector('i');
      if (el.type === 'password') {
        el.type = 'text';
        icon.className = 'bi bi-eye-slash';
        btn.setAttribute('aria-label', 'Hide password');
      } else {
        el.type = 'password';
        icon.className = 'bi bi-eye';
        btn.setAttribute('aria-label', 'Show password');
      }
    }

    function previewAvatar(input) {
      const img = document.getElementById('avatarPrev');
      if (input.files && input.files[0]) {
        img.src = URL.createObjectURL(input.files[0]);
        img.style.display = 'inline-block';
      } else {
        img.src = '';
        img.style.display = 'none';
      }
    }
  </script>
</body>

</html>