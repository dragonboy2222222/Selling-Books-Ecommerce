<?php
$pageTitle = "BookNest — Contact";
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';
require_once __DIR__ . '/partials/header.php';

$sent = false;
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $subject = trim($_POST['subject'] ?? '');
  $message = trim($_POST['message'] ?? '');
  if ($name === '' || $email === '' || $message === '') {
    $err = 'Please fill required fields.';
  } else {
    $conn->exec("CREATE TABLE IF NOT EXISTS contact_messages (
      message_id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      email VARCHAR(120) NOT NULL,
      subject VARCHAR(150) DEFAULT NULL,
      message TEXT NOT NULL,
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $st = $conn->prepare("INSERT INTO contact_messages(name,email,subject,message) VALUES(?,?,?,?)");
    $st->execute([$name, $email, $subject, $message]);
    $sent = true;
  }
}
?>
<h1 class="h4 fw-bold mb-3">Contact Us</h1>

<?php if ($sent): ?>
  <div class="alert alert-success">Thanks! Your message has been sent.</div>
<?php elseif ($err): ?>
  <div class="alert alert-danger"><?= h($err) ?></div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-lg-6">
    <form method="post" class="card p-3 border-0 shadow-sm rounded-4">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Name *</label>
          <input class="form-control" name="name" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email *</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="col-12">
          <label class="form-label">Subject</label>
          <input class="form-control" name="subject">
        </div>
        <div class="col-12">
          <label class="form-label">Message *</label>
          <textarea class="form-control" rows="6" name="message" required></textarea>
        </div>
        <div class="col-12">
          <button class="btn btn-primary rounded-pill px-4">Send</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-lg-6">
    <div class="card p-4 border-0 shadow-sm rounded-4 h-100">
      <h5 class="fw-bold">BookNest</h5>
      <p class="text-muted mb-2">Minimal, fast, and reader-friendly.</p>
      <div class="small">Email: hello@example.com</div>
      <div class="small">Phone: +95 9 000 000 000</div>
      <hr>
      <p class="mb-0 small text-muted">We usually reply within 24-48 hours.</p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>