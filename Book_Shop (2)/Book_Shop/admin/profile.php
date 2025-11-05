<?php
// profile.php — edit my profile (including avatar)
require_once "dbconnect.php";
if (!isset($_SESSION)) {
  session_start();
}
if (empty($_SESSION['loginSuccess'])) {
  header('Location: login.php');
  exit;
}

// PHP 7 fallback for str_starts_with (if needed)
if (!function_exists('str_starts_with')) {
  function str_starts_with(string $haystack, string $needle): bool
  {
    return $needle === '' || strpos($haystack, $needle) === 0;
  }
}

// Load current user  (added age in SELECT)
try {
  $st = $conn->prepare("
    SELECT user_id, first_name, last_name, email,
           gender, phone, city, age, profile_image_url, created_at
    FROM users
    WHERE email = ?
    LIMIT 1
  ");
  $st->execute([$_SESSION['email'] ?? '']);
  $me = $st->fetch();
  if (!$me) {
    die('Profile not found.');
  }
} catch (Throwable $e) {
  die($e->getMessage());
}

$success = $error = '';

if (isset($_POST['save_profile'])) {
  $first  = trim($_POST['first_name'] ?? '');
  $last   = trim($_POST['last_name']  ?? '');
  $gender = $_POST['gender'] ?? null;           // 'male' | 'female' | null
  $phone  = trim($_POST['phone'] ?? '');
  $city   = trim($_POST['city']  ?? '');
  $age    = ($_POST['age'] !== '' ? (int)$_POST['age'] : null);   // <-- added

  // start with current avatar
  $newAvatar = $me['profile_image_url'];

  // If file uploaded, prefer it
  if (!empty($_FILES['avatar']['name']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) {
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
    $finfo   = finfo_open(FILEINFO_MIME_TYPE);
    $mime    = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed, true)) {
      $error = "Only JPG, PNG, WEBP, GIF or SVG are allowed.";
    } else {
      $dir = __DIR__ . '/uploads/avatars';
      if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
      }

      $ext  = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
      $safe = 'u_' . $me['user_id'] . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . strtolower($ext) : '');
      $fs   = $dir . '/' . $safe;
      $rel  = 'uploads/avatars/' . $safe;

      if (move_uploaded_file($_FILES['avatar']['tmp_name'], $fs)) {
        // remove old local file if it was local
        if (!empty($me['profile_image_url']) && str_starts_with($me['profile_image_url'], 'uploads/')) {
          $old = __DIR__ . '/' . $me['profile_image_url'];
          if (is_file($old)) {
            @unlink($old);
          }
        }
        $newAvatar = $rel;
      } else {
        $error = "Upload failed.";
      }
    }
  }

  if (!$error) {
    try {
      // added age to UPDATE and params
      $st = $conn->prepare("
        UPDATE users
        SET first_name = ?, last_name = ?, gender = ?, phone = ?, city = ?, age = ?, profile_image_url = ?
        WHERE user_id = ?
      ");
      $st->execute([
        ($first !== '' ? $first : null),
        ($last  !== '' ? $last  : null),
        ($gender !== '' ? $gender : null),
        ($phone !== '' ? $phone : null),
        ($city  !== '' ? $city  : null),
        $age,
        ($newAvatar !== '' ? $newAvatar : null),
        (int)$me['user_id']
      ]);
      $success = "Profile updated.";

      // refresh array for display without re-query
      $me['first_name'] = $first ?: null;
      $me['last_name']  = $last ?: null;
      $me['gender']     = ($gender !== '') ? $gender : null;
      $me['phone']      = $phone ?: null;
      $me['city']       = $city ?: null;
      $me['age']        = $age;
      $me['profile_image_url'] = $newAvatar ?: null;
    } catch (Throwable $e) {
      $error = $e->getMessage();
    }
  }
}

// Default avatar for display
$avatarDefault = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false)
  ? '../assets/avatar-default.png'
  : 'assets/avatar-default.png';
$avatar = !empty($me['profile_image_url']) ? $me['profile_image_url'] : $avatarDefault;
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg1: #0f1029;
      --bg2: #2b1e52;
      --panel: #10132f;
      --panel2: #13183c;
      --ink: #eef1f7;
      --muted: #b9c1d1;
      --border: rgba(255, 255, 255, .14);
      --accent: #5c6cff;
      --hot: #ff2d8f;
      --hot2: #ff6ea9;
      --field-h: 52px;
      /* unified field height (desktop) */
    }

    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, Arial;
      color: var(--ink);
      background:
        radial-gradient(900px 600px at 85% 10%, rgba(108, 0, 255, .18), transparent 60%),
        radial-gradient(800px 600px at 15% 90%, rgba(255, 45, 143, .18), transparent 60%),
        linear-gradient(160deg, var(--bg1), var(--bg2));
      overflow-x: hidden;
    }

    .card {
      background: linear-gradient(180deg, var(--panel), var(--panel2));
      border: 1px solid var(--border);
      border-radius: 18px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, .25);
      overflow: visible;
      /* prevents select dropdown clipping */
    }

    .card h5 {
      color: #e8ebf4;
      font-weight: 800
    }

    .subtle {
      color: var(--muted);
    }

    .form-label {
      color: #dfe3ef;
      font-weight: 600
    }

    .form-control,
    .form-select {
      color: #fff;
      background: rgba(255, 255, 255, .08);
      border: 1px solid var(--border);
      border-radius: 12px;
      height: var(--field-h) !important;
      padding: .8rem 1rem !important;
    }

    .form-control::placeholder {
      color: var(--muted)
    }

    .form-control:focus,
    .form-select:focus {
      color: #fff;
      background: rgba(255, 255, 255, .1);
      border-color: var(--hot2);
      box-shadow: 0 0 0 .2rem rgba(255, 110, 169, .25);
      outline: 0;
      position: relative;
      z-index: 1051;
      /* keep focused control above card edges */
    }

    .form-select {
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      padding-right: 2.5rem !important;
      background-position: right 1rem center;
      background-size: 16px 12px;
    }

    .form-select option {
      background: #121733;
      color: #fff;
    }

    input[type="file"].form-control {
      padding: .55rem .9rem !important;
      color: #dfe3ef
    }

    .user-avatar {
      width: 96px;
      height: 96px;
      border-radius: 50%;
      object-fit: cover;
      object-position: center;
      background: #59A9DC;
      border: 2px solid #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, .18);
    }

    .btn-grad {
      background: linear-gradient(135deg, var(--hot), var(--hot2));
      color: #0f1029;
      border: 0;
    }

    .btn-outline-light {
      border-color: rgba(255, 255, 255, .35);
      color: #fff
    }

    .btn-outline-light:hover {
      background: rgba(255, 255, 255, .1)
    }

    /* ---------- Responsive polish ---------- */
    .profile-header {
      flex-wrap: wrap;
      gap: 14px;
    }

    @media (max-width: 992px) {
      :root {
        --field-h: 48px;
      }

      .card {
        border-radius: 16px;
      }

      .user-avatar {
        width: 80px;
        height: 80px;
      }

      .card .p-4 {
        padding: 1.1rem !important;
      }
    }

    @media (max-width: 768px) {
      :root {
        --field-h: 46px;
      }

      .user-avatar {
        width: 72px;
        height: 72px;
      }

      .card h5 {
        font-size: 1.05rem;
      }

      .container {
        padding-left: 10px;
        padding-right: 10px;
      }
    }

    @media (max-width: 480px) {
      :root {
        --field-h: 44px;
      }

      .user-avatar {
        width: 60px;
        height: 60px;
      }

      .card {
        border-radius: 14px;
      }

      .btn {
        width: 100%;
      }

      /* stack buttons full width */
      .btn+.btn {
        margin-top: .5rem;
      }

      /* spacing when stacked */
    }
  </style>
</head>

<body>

  <div class="container-fluid p-0">
    <!-- Keep your existing navbar -->
    <?php require_once "navbarcopy.php"; ?>
    <div class="row justify-content-center" style="margin-top:40px; margin-bottom:20px">
      <div class="col-12 col-lg-9 col-xl-8">
        <div class="card p-4">
          <div class="d-flex align-items-center profile-header mb-3">
            <img src="uploads\avatars\u_3_e5e020de.png" style="width:96px; height:96px; border-radius:50%; object-fit:cover; object-position:center; background:#59A9DC; border:2px solid #fff; box-shadow:0 2px 6px rgba(0,0,0,.18);" alt="avatar">
            <div>
              <h5 class="mb-0"><?= htmlspecialchars(trim(($me['first_name'] ?? '') . ' ' . ($me['last_name'] ?? '')) ?: 'My Profile') ?></h5>
              <small class="subtle"><?= htmlspecialchars($me['email']) ?></small>
            </div>
          </div>

          <?php if ($success): ?><div class="alert alert-success py-2"><?= htmlspecialchars($success) ?></div><?php endif; ?>
          <?php if ($error):   ?><div class="alert alert-danger  py-2"><?= htmlspecialchars($error)   ?></div><?php endif; ?>

          <form method="post" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
              <label class="form-label">First name</label>
              <input class="form-control" name="first_name" value="<?= htmlspecialchars($me['first_name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Last name</label>
              <input class="form-control" name="last_name" value="<?= htmlspecialchars($me['last_name'] ?? '') ?>">
            </div>

            <div class="col-md-4">
              <label class="form-label">Gender</label>
              <select class="form-select" name="gender">
                <option value="male" <?= ($me['gender'] === 'male'   ? 'selected' : '') ?>>male</option>
                <option value="female" <?= ($me['gender'] === 'female' ? 'selected' : '') ?>>female</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Phone</label>
              <input class="form-control" name="phone" value="<?= htmlspecialchars($me['phone'] ?? '') ?>" placeholder="+95-9-...">
            </div>
            <div class="col-md-4">
              <label class="form-label">City</label>
              <input class="form-control" name="city" value="<?= htmlspecialchars($me['city'] ?? '') ?>">
            </div>

            <div class="col-md-4">
              <label class="form-label">Age</label>
              <input class="form-control" type="number" min="0" max="120" name="age"
                value="<?= htmlspecialchars($me['age'] ?? '') ?>">
            </div>

            <div class="col-md-6">
              <label class="form-label">Upload avatar (optional)</label>
              <input class="form-control" type="file" name="avatar" accept="image/*">
            </div>

            <div class="col-12 d-flex flex-wrap gap-2">
              <button class="btn btn-grad px-4" name="save_profile">Save Profile</button>
              <a href="dashboard.php" class="btn btn-outline-light px-4">Back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

</body>

</html>