<?php
// privacy.php — BookNest Privacy Policy
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';
$pageTitle = "Privacy Policy • BookNest";
require_once __DIR__ . '/partials/header.php';
?>

<div class="container-xxl py-5">
    <h1 class="fw-bold mb-4">Privacy Policy</h1>
    <p class="text-muted">At BookNest, your privacy is important to us. This policy explains how we collect, use, and protect your personal information when you use our website and services.</p>

    <hr class="my-4">

    <h3 class="h5 fw-semibold mt-4">1. Information We Collect</h3>
    <ul>
        <li><strong>Account Information:</strong> Name, email, password, and contact details you provide during registration.</li>
        <li><strong>Order Details:</strong> Shipping address, phone number, and payment confirmation.</li>
        <li><strong>Usage Data:</strong> Pages visited, products viewed, and actions performed on the site (via cookies and analytics tools).</li>
    </ul>

    <h3 class="h5 fw-semibold mt-4">2. How We Use Your Information</h3>
    <ul>
        <li>To process and deliver your book orders.</li>
        <li>To communicate updates about your account and purchases.</li>
        <li>To improve our website, products, and services.</li>
        <li>For customer support and security purposes.</li>
    </ul>

    <h3 class="h5 fw-semibold mt-4">3. Cookies & Tracking</h3>
    <p>BookNest uses cookies to enhance your browsing experience, remember your cart, and analyze site usage. You may disable cookies in your browser, but some features may not work properly.</p>

    <h3 class="h5 fw-semibold mt-4">4. Data Sharing</h3>
    <p>We do not sell or rent your personal information. Data may only be shared with trusted service providers (payment processors, shipping companies) strictly for fulfilling your order.</p>

    <h3 class="h5 fw-semibold mt-4">5. Data Security</h3>
    <p>We use secure servers and encryption to protect your personal data. However, no method of transmission over the Internet is 100% secure.</p>

    <h3 class="h5 fw-semibold mt-4">6. Your Rights</h3>
    <p>You have the right to access, update, or delete your personal information. Contact us if you wish to exercise these rights.</p>

    <h3 class="h5 fw-semibold mt-4">7. Third-Party Links</h3>
    <p>Our site may include links to third-party websites. BookNest is not responsible for their privacy practices.</p>

    <h3 class="h5 fw-semibold mt-4">8. Changes to Policy</h3>
    <p>We may update this Privacy Policy from time to time. Updates will be posted on this page with the revised date.</p>

    <h3 class="h5 fw-semibold mt-4">9. Contact Us</h3>
    <p>If you have questions regarding this Privacy Policy, please <a href="contact.php">contact us</a>.</p>

    <hr class="my-5">

    <p class="small text-muted">Last updated: <?= date('F j, Y') ?></p>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>