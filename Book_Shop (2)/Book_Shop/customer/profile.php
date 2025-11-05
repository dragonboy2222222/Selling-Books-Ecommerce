<?php
if (!isset($_SESSION)) session_start();

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';
require_once __DIR__ . '/partials/header.php';

// --- Guard: require login ---
if (empty($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$userId = (int)$_SESSION['user_id'];
$errors = [];
$flash  = "";

// Fetch current user
$q = $conn->prepare("
  SELECT user_id, first_name, last_name, email, gender, phone, city, age, profile_image_url
  FROM users
  WHERE user_id = ?
  LIMIT 1
");
$q->execute([$userId]);
$user = $q->fetch(PDO::FETCH_ASSOC);
if (!$user) {
  $errors[] = "User not found.";
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
  $first   = trim($_POST['first_name'] ?? '');
  $last    = trim($_POST['last_name'] ?? '');
  $gender  = trim($_POST['gender'] ?? '');
  $phone   = trim($_POST['phone'] ?? '');
  $city    = trim($_POST['city'] ?? '');
  $age     = (int)($_POST['age'] ?? 0);

  // Basic validation
  if ($first === '') $errors[] = "First name is required.";
  if ($last === '')  $errors[] = "Last name is required.";
  if ($age < 0 || $age > 120) $errors[] = "Please enter a valid age (0-120).";

  // File upload (optional)
  $avatarPath = $user['profile_image_url']; // keep current unless changed
  if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $f = $_FILES['avatar'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($f['type'], $allowed, true)) {
      $errors[] = "Avatar must be JPG, PNG, or WebP.";
    } elseif ($f['size'] > 2 * 1024 * 1024) { // 2MB
      $errors[] = "Avatar must be at most 2MB.";
    } else {
      // Ensure folder exists
      $dir = __DIR__ . '/../uploads/avatars';
      if (!is_dir($dir)) @mkdir($dir, 0777, true);

      $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
      $newName = 'u_' . $userId . '_' . time() . '.' . strtolower($ext);
      $destAbs = $dir . '/' . $newName;
      $destRel = 'uploads/avatars/' . $newName;

      if (move_uploaded_file($f['tmp_name'], $destAbs)) {
        $avatarPath = $destRel;
      } else {
        $errors[] = "Failed to save the avatar.";
      }
    }
  }

  // Password change (optional)
  $curPw   = $_POST['current_password'] ?? '';
  $newPw   = $_POST['new_password'] ?? '';
  $newPw2  = $_POST['new_password2'] ?? '';
  $changePw = ($curPw !== '' || $newPw !== '' || $newPw2 !== '');

  if ($changePw) {
    if ($curPw === '' || $newPw === '' || $newPw2 === '') {
      $errors[] = "To change your password, fill in all three fields.";
    } elseif ($newPw !== $newPw2) {
      $errors[] = "New password and confirmation do not match.";
    } else {
      // Check current password
      $pwq = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ? LIMIT 1");
      $pwq->execute([$userId]);
      $row = $pwq->fetch(PDO::FETCH_ASSOC);
      if (!$row || !password_verify($curPw, $row['password_hash'])) {
        $errors[] = "Current password is incorrect.";
      }
    }
  }

  if (!$errors) {
    // Update profile
    $u = $conn->prepare("
      UPDATE users
      SET first_name=?, last_name=?, gender=?, phone=?, city=?, age=?, profile_image_url=?
      WHERE user_id=?
      LIMIT 1
    ");
    $u->execute([$first, $last, $gender, $phone, $city, $age, $avatarPath, $userId]);

    if ($changePw) {
      $newHash = password_hash($newPw, PASSWORD_DEFAULT);
      $up = $conn->prepare("UPDATE users SET password_hash=? WHERE user_id=? LIMIT 1");
      $up->execute([$newHash, $userId]);
    }

    // Refresh local copy for rendering
    $q->execute([$userId]);
    $user = $q->fetch(PDO::FETCH_ASSOC);

    // update session display name if you show it in header
    $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));

    $flash = "Profile updated successfully.";
  }
}
?>

<style>
  /* Light / white theme tweaks */
  .profile-wrap {
    max-width: 980px;
    margin: 0 auto;
  }

  .avatar-lg {
    width: 72px;
    height: 72px;
    border-radius: 999px;
    object-fit: cover;
    background: #f1f3f5;
    display: inline-block;
  }
</style>

<div class="container-xxl py-4 profile-wrap">
  <?php if ($flash): ?>
    <div class="alert alert-success"><?= h($flash) ?></div>
  <?php endif; ?>
  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
          <li><?= h($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
      <div class="d-flex align-items-center gap-3 mb-3">
        <img src="assets\img\download.png"
          class="avatar-lg"
          alt="avatar">
        <div>
          <div class="fs-5 fw-semibold mb-0"><?= h(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?></div>
          <div class="text-muted small"><?= h($user['email'] ?? '') ?></div>
        </div>
      </div>

      <form method="post" enctype="multipart/form-data" class="mt-3">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">First name</label>
            <input type="text" name="first_name" class="form-control"
              value="<?= h($user['first_name'] ?? '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Last name</label>
            <input type="text" name="last_name" class="form-control"
              value="<?= h($user['last_name'] ?? '') ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Gender</label>
            <input type="text" name="gender" class="form-control"
              value="<?= h($user['gender'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control"
              value="<?= h($user['phone'] ?? '') ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control"
              value="<?= h($user['city'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Age</label>
            <input type="number" name="age" class="form-control" min="0" max="120"
              value="<?= h((string)($user['age'] ?? '')) ?>">
          </div>

          <div class="col-12">
            <label class="form-label">Upload avatar</label>
            <input type="file" name="avatar" class="form-control">
          </div>
          <!-- 
          <div class="col-12">
            <hr class="my-3">
            <div class="fw-semibold mb-2">Change Password</div>
          </div> -->

          <div class="col-12">
            <label class="form-label">Current password</label>
            <input type="password" name="current_password" class="form-control" autocomplete="current-password">
          </div>
          <div class="col-md-6">
            <label class="form-label">New password</label>
            <input type="password" name="new_password" class="form-control" autocomplete="new-password">
          </div>
          <div class="col-md-6">
            <label class="form-label">Confirm new password</label>
            <input type="password" name="new_password2" class="form-control" autocomplete="new-password">
          </div>

          <div class="col-12 d-flex gap-2 mt-2">
            <button class="btn btn-primary">Save Profile</button>
            <button type="reset" class="btn btn-light">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>