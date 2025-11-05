<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once "dbconnect.php";

if (isset($_POST['login'])) {
  $email    = $_POST['email']    ?? '';
  $password = $_POST['password'] ?? '';

  if ($email === '' || $password === '') {
    $errorMessage = "Email and password are required.";
  } else {
    // Join roles and only allow admins to log in
    $sql = "SELECT u.user_id, u.email, u.password_hash, r.role_name
                FROM users u
                JOIN roles r ON r.role_id = u.role_id
                WHERE u.email = ? AND r.role_name = 'admin'
                LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
      $_SESSION['loginSuccess'] = true;
      $_SESSION['email'] = $admin['email'];
      header('Location: dashboard.php');
      exit;
    } else {
      $errorMessage = "Invalid email or password.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Login • BookNest</title>
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
    }

    /* center area under the (optional) navbar */
    .login-area {
      min-height: calc(100vh - 70px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 16px;
    }

    .glass-card {
      width: 100%;
      max-width: 460px;
      background: linear-gradient(180deg, rgba(255, 255, 255, .06), rgba(255, 255, 255, .03));
      border: 1px solid var(--border);
      border-radius: 20px;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 20px 50px rgba(0, 0, 0, .35);
      padding: 28px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 10px;
    }

    .brand img {
      height: 40px;
      width: auto;
      border-radius: 8px
    }

    .brand span {
      font-weight: 800;
      letter-spacing: .3px;
      font-size: 1.05rem;
    }

    .title {
      font-weight: 800;
      letter-spacing: .2px;
      margin: 4px 0 18px;
      background: linear-gradient(135deg, #ffffff, #cfd6ff);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      font-size: clamp(1.3rem, 2.5vw, 1.6rem);
    }

    .form-label {
      color: var(--muted);
      font-weight: 600;
      font-size: .9rem;
    }

    .form-control {
      background: rgba(255, 255, 255, .06) !important;
      border: 1px solid var(--border) !important;
      color: var(--ink) !important;
      border-radius: 12px !important;
      padding: .7rem .9rem !important;
    }

    .form-control::placeholder {
      color: #c9cfe6;
      opacity: .6
    }

    .form-control:focus {
      border-color: rgba(92, 108, 255, .65) !important;
      box-shadow: 0 0 0 .2rem rgba(92, 108, 255, .15) !important;
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

    .helper {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-top: 8px;
    }

    .muted {
      color: var(--muted);
      font-size: .9rem;
    }

    .error {
      border-radius: 12px;
      background: rgba(255, 77, 109, .15);
      border: 1px solid rgba(255, 77, 109, .35);
      color: #ffd7de;
    }

    .top-space {
      height: 70px
    }

    /* reserve space if your navbar sits above */
    @media (max-width: 576px) {
      .glass-card {
        padding: 22px;
      }
    }
  </style>
</head>

<body>
  <div class="container-fluid">

    <div class="login-area">
      <div class="glass-card">
        <h1 class="title" style="margin-left:150px">Log In</h1>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" novalidate>
          <?php if (isset($errorMessage)): ?>
            <div class="alert error py-2 px-3 mb-3">
              <i class="bi bi-exclamation-triangle me-2"></i>
              <?php echo htmlspecialchars($errorMessage); ?>
            </div>
          <?php endif; ?>

          <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input id="email" type="email" class="form-control" name="email" placeholder="admin@example.com" required>
          </div>

          <div class="mb-2">
            <label class="form-label" for="password">Password</label>
            <input id="password" type="password" class="form-control" name="password" placeholder="Your password" required>
          </div>

          <div class="d-grid mt-3">
            <button type="submit" class="btn btn-grad" name="login">
              <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>