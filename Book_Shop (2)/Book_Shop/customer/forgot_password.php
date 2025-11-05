<?php
// forgot_password.php — Request a password reset link (BookNest auth theme)
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';

$errors = [];
$sent   = false;
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));

    // Always behave the same whether email exists or not (don’t leak)
    try {
        // Look up only customers
        $q = $conn->prepare("
      SELECT u.user_id, u.email
      FROM users u
      INNER JOIN roles r ON r.role_id = u.role_id
      WHERE u.email = ? AND LOWER(r.role_name) = 'customer'
      LIMIT 1
    ");
        $q->execute([$email]);
        $user = $q->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Create table if missing (id, user_id, token_hash, expires_at, created_at, used_at)
            $conn->exec("
        CREATE TABLE IF NOT EXISTS password_resets (
          id INT AUTO_INCREMENT PRIMARY KEY,
          user_id INT NOT NULL,
          token_hash VARCHAR(64) NOT NULL,
          expires_at DATETIME NOT NULL,
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          used_at DATETIME NULL,
          INDEX (user_id),
          INDEX (token_hash)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
      ");

            // Generate token & hash (store hash, send raw token)
            $rawToken  = bin2hex(random_bytes(32));             // 64-hex
            $tokenHash = hash('sha256', $rawToken);
            $expires   = date('Y-m-d H:i:s', time() + 3600);    // 1 hour

            $ins = $conn->prepare("
        INSERT INTO password_resets (user_id, token_hash, expires_at, created_at)
        VALUES (?, ?, ?, NOW())
      ");
            $ins->execute([(int)$user['user_id'], $tokenHash, $expires]);

            // Build reset link (to be handled by your reset_password.php)
            $scheme    = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
            $baseUrl   = rtrim($scheme . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']), '/');
            $resetLink = $baseUrl . '/reset_password.php?token=' . $rawToken;

            // TODO: Send $resetLink via email to $user['email'] with your mailer.
            // For local dev convenience, we’ll surface it only on localhost:
            if (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false) {
                $_SESSION['dev_reset_link'] = $resetLink;
            }
        }

        // Always act like we sent it (even if no user)
        $sent = true;
    } catch (Throwable $e) {
        // Fail closed but remain vague to the user
        $errors[] = "Something went wrong. Please try again.";
        // Optionally log $e->getMessage() to your server logs.
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password • BookNest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ==== BookNest Auth Card Theme (same as login/signup) ==== */
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
            max-width: 460px;
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

        .form-control {
            height: 46px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 .22rem var(--ring);
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

        .dev-box {
            background: #fff9db;
            border: 1px solid #ffe08a;
            color: #7a5d00;
            border-radius: 12px;
            padding: 12px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="card card-auth">
        <div class="card-body">
            <h1 class="auth-title">
                <span class="fs-5">🔐</span> Forgot Password
            </h1>
            <div class="auth-sub">Enter your account email and we’ll send a password reset link.</div>

            <?php if ($errors): ?>
                <div class="alert alert-danger mt-3 mb-2">
                    <?php foreach ($errors as $e): ?>
                        <div><?= h($e) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($sent): ?>
                <div class="alert alert-success mt-3">
                    If your email is registered, you’ll receive a reset link shortly.
                </div>

                <?php if (!empty($_SESSION['dev_reset_link'])): ?>
                    <div class="dev-box mt-3">
                        <strong>Dev note (localhost only):</strong><br>
                        Use this link to test your reset flow:<br>
                        <a href="<?= h($_SESSION['dev_reset_link']) ?>"><?= h($_SESSION['dev_reset_link']) ?></a>
                    </div>
                    <?php unset($_SESSION['dev_reset_link']); ?>
                <?php endif; ?>

                <div class="auth-foot mt-3">
                    <a href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Back to login</a>
                    <a class="btn btn-primary" href="login.php">OK</a>
                </div>
            <?php else: ?>
                <form method="post" class="mt-3" autocomplete="on">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="you@example.com" value="<?= h($email) ?>" required>
                    </div>

                    <button class="btn btn-primary w-100" type="submit">
                        <i class="bi bi-envelope-at me-1"></i> Send reset link
                    </button>

                    <div class="auth-foot">
                        <a href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Back to login</a>
                        <a href="signup.php">Create account</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>