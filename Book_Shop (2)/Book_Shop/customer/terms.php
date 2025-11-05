<?php
// terms.php — BookNest Terms & Conditions (tailored to your site)
// Uses only features visible on http://localhost:3000/customer/index.php
if (!isset($_SESSION)) { session_start(); }
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';

$pageTitle = "Terms & Conditions • BookNest";
require_once __DIR__ . '/partials/header.php';
?>
<div class="container-xxl py-5">
  <h1 class="fw-bold mb-3">Terms &amp; Conditions</h1>
  <p class="text-muted mb-4">
    These Terms govern your use of BookNest and the purchase of products on our website.
    By using this site you agree to these Terms. If you do not agree, please do not use the site.
  </p>

  <h3 class="h5 fw-semibold mt-4">1. About BookNest</h3>
  <p>
    BookNest is an online bookstore. You can browse books, add them to cart, create an account, place
    orders, and write reviews for books you purchased.
  </p>

  <h3 class="h5 fw-semibold mt-4">2. Accounts</h3>
  <ul>
    <li>You’re responsible for the accuracy of your account information and for keeping your password secure.</li>
    <li>You may edit your profile details from the Profile page after signing in.</li>
    <li>We may suspend or remove accounts that misuse the site or violate these Terms.</li>
  </ul>

  <h3 class="h5 fw-semibold mt-4">3. Orders &amp; Status</h3>
  <ul>
    <li>After checkout, orders are created in our system. You can view them in <em>My Orders</em>.</li>
    <li>Order status can be <strong>pending</strong>, <strong>paid</strong>, <strong>delivered</strong>, or <strong>canceled</strong>. Status is updated by an administrator based on payment and fulfillment progress.</li>
    <li>We reserve the right to cancel an order (for example, if stock is unavailable or details are incorrect). You’ll be notified if that happens.</li>
  </ul>

  <h3 class="h5 fw-semibold mt-4">4. Pricing, Discounts &amp; Book-Specific Promotions</h3>
  <ul>
    <li>Prices shown at checkout are the prices that apply to your order.</li>
    <li>Discounts may be site-wide (discount codes) or book-specific (book discounts). If a discount applies, it is shown in the order total before you confirm the purchase.</li>
    <li>We may change prices or end discounts at any time before you place an order.</li>
  </ul>

  <h3 class="h5 fw-semibold mt-4">5. Payments</h3>
  <ul>
    <li>Payment status is reflected as <strong>PAID</strong> or <strong>UNPAID</strong> in your order. Once a payment is confirmed, the order status may be updated to <strong>paid</strong> or progress towards <strong>delivered</strong>.</li>
    <li>If a payment fails or is reversed, the order may be canceled.</li>
  </ul>

  <h3 class="h5 fw-semibold mt-4">6. Shipping</h3>
  <ul>
    <li>Available shipping methods are shown at checkout. Delivery estimates depend on the selected method and destination.</li>
    <li>Please ensure your shipping address and contact details are correct to avoid delays.</li>
  </ul>

  <h3 class="h5 fw-semibold mt-4">7. Returns &amp; Refunds</h3>
  <p>
    If a return or refund is available for your order, it will follow the policy communicated on the
    product or during checkout. Contact us from the <a href="contact.php">Contact</a> page with your order number for help.
  </p>

  <h3 class="h5 fw-semibold mt-4">8. Reviews</h3>
  <ul>
    <li>You can review books you purchased by using <em>Review items</em> from <em>My Orders</em>.</li>
    <li>Reviews must be respectful, honest, and relevant to the book. We may remove reviews that are abusive, spammy, or violate the law.</li>
  </ul>

  <h3 class="h5 fw-semibold mt-4">9. Acceptable Use</h3>
  <ul>
    <li>Do not attempt to disrupt the site, misuse features, or access other users’ data.</li>
    <li>Do not upload content that infringes intellectual property or violates applicable laws.</li>
  </ul>

  <h3 class="h5 fw-semibold mt-4">10. Intellectual Property</h3>
  <p>
    All site content (logos, design, and product data) is owned by or licensed to BookNest. You may not copy or distribute site content without permission.
  </p>

  <h3 class="h5 fw-semibold mt-4">11. Limitation of Liability</h3>
  <p>
    To the extent permitted by law, BookNest is not liable for indirect or consequential damages arising from your use of the site. Nothing in these Terms limits your statutory consumer rights.
  </p>

  <h3 class="h5 fw-semibold mt-4">12. Changes to These Terms</h3>
  <p>
    We may update these Terms from time to time. The latest version will always be available on this page and takes effect upon posting.
  </p>

  <h3 class="h5 fw-semibold mt-4">13. Contact</h3>
  <p>
    Questions? Please <a href="contact.php">contact us</a> and include your order number (if applicable).
  </p>

  <hr class="my-5">
  <p class="small text-muted">Last updated: <?= date('F j, Y') ?></p>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
