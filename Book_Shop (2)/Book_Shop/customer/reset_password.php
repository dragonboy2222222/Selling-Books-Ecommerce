<?php
// reset_password.php — Set a new password using a reset token (BookNest auth theme)
if (!isset($_SESSION)) { session_start(); }
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';

$errors = [];
$done   = false;

// 1) Read token (GET on first view, POST on submit)
$rawToken = $_GET['token'] ?? $_POST['token'] ?? '';
$rawToken = trim($rawToken);

// Quick token format check: must be 64 hex chars (from bin2hex(32))
if ($rawToken === '' || strlen($rawToken) !== 64 || !ctype_xdigit($rawToken)) {
  $errors[] = "Invalid or missing reset token.";
  $rawToken = ''; // ensure empty for form state
}

// 2) Look up token row (if token format OK)
$resetRow = null;
$userRow  = null;
if (!$errors && $rawToken !== '') {
  try {
    $hash = hash('sha256', $rawToken);
    $q = $conn->prepare("
      SELECT pr.id, pr.user_id, pr.expires_at, pr.used_at, pr.created_at,
             u.email
      FROM password_resets pr
      JOIN users u ON u.user_id = pr.user_id
      WHERE pr.token_hash = ?
      ORDER BY pr.created_at DESC
      LIMIT 1
    ");
    $q->execute([$hash]);
    $resetRow = $q->fetch(PDO::FETCH_ASSOC);

    if (!$resetRow) {
      $errors[] = "This reset link is invalid.";
    } else {
      // expired or used?
      $now = new DateTimeImmutable('now');
      $exp = new DateTimeImmutable($resetRow['expires_at']);
      if (!empty($resetRow['used_at'])) {
        $errors[] = "This reset link was already used.";
      } elseif ($exp < $now) {
        $errors[] = "This reset link has expired.";
      }
    }

  } catch (Throwable $e) {
    $errors[] = "Something went wrong. Please try again.";
  }
}

// 3) Handle form post (set new password)
if (!$errors && $resetRow && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $pw1 = $_POST['password']  ?? '';
  $pw2 = $_POST['password2'] ?? '';

  if ($pw1 === '' || strlen($pw1) < 8) {
    $errors[] = "Password must be at least 8 characters.";
  }
  if ($pw1 !== $pw2) {
    $errors[] = "Passwords do not match.";
  }

  if (!$errors) {
    try {
      // Update user password
      $hashPw = password_hash($pw1, PASSWORD_BCRYPT);
      $u = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
      $u->execute([$hashPw, (int)$resetRow['user_id']]);

      // Mark this reset token as used
      $m = $conn->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ?");
      $m->execute([(int)$resetRow['id']]);

      // Optionally: invalidate all other outstanding tokens for this user
      $i = $conn->prepare("UPDATE password_resets SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL");
      $i->execute([(int)$resetRow['user_id']]);

      $done = true;

    } catch (Throwable $e) {
      $errors[] = "Failed to set new password. Please try again.";
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password • BookNest</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    /* ==== BookNest Auth Card Theme (same as login/signup/forgot) ==== */
    :root{
      --bg:      #f6f7fb;
      --ink:     #0f172a;
      --muted:   #64748b;
      --border:  rgba(2,6,23,.08);
      --surface: #ffffff;
      --primary: #1a73e8;
      --ring:    rgba(26,115,232,.24);
      --radius:  14px;
      --shadow:  0 10px 24px rgba(2,6,23,.08);
    }
    html,body{ height:100%; }
    body{
      margin:0;
      font-family:"Inter",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      color:var(--ink);
      background:
        radial-gradient(900px 500px at 85% -10%, rgba(26,115,232,.08), transparent 60%),
        radial-gradient(700px 400px at 10% 110%, rgba(26,115,232,.06), transparent 60%),
        var(--bg);
      display:flex; align-items:center; justify-content:center;
      padding: 24px 12px;
    }
    .card-auth{
      width:100%;
      max-width: 460px;
      background:var(--surface);
      border:1px solid var(--border);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
    }
    .card-auth .card-body{ padding:26px 24px 22px; }
    .auth-title{
      display:flex; align-items:center; gap:10px;
      font-weight:800; font-size:22px; letter-spacing:.2px; margin:0;
    }
    .auth-sub{ color:var(--muted); font-size:14px; margin-top:6px; }
    .form-label{ font-weight:600; margin-bottom:6px; }
    .form-control{
      height:46px; border-radius:12px;
      border:1px solid var(--border);
    }
    .form-control:focus{
      border-color:var(--primary); box-shadow:0 0 0 .22rem var(--ring);
    }
    .btn-primary{
      background:var(--primary); border-color:var(--primary);
      border-radius:999px; height:46px; font-weight:700;
    }
    .btn-primary:hover{ filter:brightness(.95); }
    .pw-wrap{ position:relative; }
    .pw-toggle{
      position:absolute; right:10px; top:50%; transform:translateY(-50%);
      border:0; background:transparent; color:#667085; width:36px; height:36px;
    }
    .pw-toggle:hover{ color:#111827; }
    .auth-foot{
      display:flex; justify-content:space-between; align-items:center; gap:12px;
      margin-top:12px; font-size:14px;
    }
    .auth-foot a{ color:var(--primary); text-decoration:none; }
    .auth-foot a:hover{ text-decoration:underline; }
  </style>
</head>
<body>

  <div class="card card-auth">
    <div class="card-body">
      <h1 class="auth-title">
        <span class="fs-5">🔒</span> Reset Password
      </h1>
      <div class="auth-sub">
        Choose a new password for your account.
      </div>

      <?php if ($errors): ?>
        <div class="alert alert-danger mt-3 mb-2">
          <?php foreach ($errors as $e): ?>
            <div><?= h($e) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($done): ?>
        <div class="alert alert-success mt-3">
          Your password has been updated. You can now log in.
        </div>
        <div class="auth-foot mt-2">
          <a href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Back to login</a>
          <a class="btn btn-primary" href="login.php">Log In</a>
        </div>
      <?php elseif ($rawToken === '' || !$resetRow || $errors): ?>
        <div class="auth-foot mt-3">
          <a href="forgot_password.php">Request a new reset link</a>
          <a class="btn btn-primary" href="login.php">Back to login</a>
        </div>
      <?php else: ?>
        <form method="post" class="mt-3" autocomplete="new-password">
          <input type="hidden" name="token" value="<?= h($rawToken) ?>">

          <div class="mb-3">
            <label class="form-label">New password</label>
            <div class="pw-wrap">
              <input id="pw1" type="password" class="form-control" name="password" placeholder="At least 8 characters" required>
              <button class="pw-toggle" type="button" aria-label="Show password" onclick="togglePw('pw1', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm new password</label>
            <div class="pw-wrap">
              <input id="pw2" type="password" class="form-control" name="password2" required>
              <button class="pw-toggle" type="button" aria-label="Show password" onclick="togglePw('pw2', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <button class="btn btn-primary w-100" type="submit">
            <i class="bi bi-check2-circle me-1"></i> Update password
          </button>

          <div class="auth-foot">
            <a href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Back to login</a>
            <a href="forgot_password.php">Need a new link?</a>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function togglePw(id, btn){
      const el = document.getElementById(id);
      const icon = btn.querySelector('i');
      if (el.type === 'password') {
        el.type = 'text';
        icon.classList.replace('bi-eye','bi-eye-slash');
        btn.setAttribute('aria-label','Hide password');
      } else {
        el.type = 'password';
        icon.classList.replace('bi-eye-slash','bi-eye');
        btn.setAttribute('aria-label','Show password');
      }
    }
  </script>
</body>
</html>
