<?php
// login.php — Customer-only login with "Remember me" cookie
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';

$errors = [];
$email = $_COOKIE['remember_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember']);

    try {
        $sql = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.password_hash
            FROM users u
            INNER JOIN roles r ON r.role_id = u.role_id
            WHERE u.email = ? AND LOWER(r.role_name) = 'customer'
            LIMIT 1";
        $q = $conn->prepare($sql);
        $q->execute([strtolower($email)]);
        $u = $q->fetch(PDO::FETCH_ASSOC);

        if (!$u || !password_verify($password, $u['password_hash'])) {
            $errors[] = "Invalid email or password.";
        } else {
            $_SESSION['loginSuccess'] = true;
            $_SESSION['user_id']     = (int)$u['user_id'];
            $_SESSION['user_name']   = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
            $_SESSION['user_email']  = $u['email'];

            if ($remember) {
                $lifetime = 60 * 60 * 24 * 30; // 30 days
                setcookie(session_name(), session_id(), [
                    'expires'  => time() + $lifetime,
                    'path'     => '/',
                    'secure'   => !empty($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
                setcookie('remember_email', $u['email'], time() + $lifetime, '/', '', !empty($_SERVER['HTTPS']), true);
            } else {
                setcookie('remember_email', '', time() - 3600, '/');
            }

            header("Location: index.php");
            exit;
        }
    } catch (Throwable $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Log In • BookNest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <style>
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
        max-width: 420px;
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

    .auth-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
        font-size: 14px;
    }

    .auth-foot a {
        text-decoration: none;
        color: var(--primary);
    }

    .auth-foot a:hover {
        text-decoration: underline;
    }

    @media (max-width:420px) {
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
                Log In
            </h1>
            <div class="auth-sub">Welcome back to BookNest</div>

            <?php if ($errors): ?>
            <div class="alert alert-danger mt-3">
                <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul>
            </div>
            <?php endif; ?>

            <form method="post" class="mt-3" autocomplete="on">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="you@example.com"
                        value="<?= h($email) ?>" required />
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="pw-wrap">
                        <input id="pw" type="password" class="form-control" name="password" placeholder="Your password"
                            required />
                        <button class="pw-toggle" type="button" aria-label="Show password"
                            onclick="togglePw('pw', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1"
                            <?= isset($_POST['remember']) || isset($_COOKIE['remember_email']) ? 'checked' : '' ?> />
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <a class="small" href="forgot_password.php">Forgot password?</a>
                </div>

                <button class="btn btn-primary w-100" type="submit">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </button>

                <div class="auth-foot">
                    <span class="text-muted">Don't have any account!</span>
                    <a href="signup.php">Create an account</a>
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
    </script>
</body>

</html>