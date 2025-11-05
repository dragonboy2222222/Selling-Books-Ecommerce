<?php
// faq.php — BookNest Frequently Asked Questions
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/dbconnect.php';

$pageTitle = "FAQ • BookNest";
require_once __DIR__ . '/partials/header.php';

/* You can tweak these Q&As anytime */
$faqs = [
    [
        'q' => 'How do I place an order?',
        'a' => 'Browse books on the Shop page, click "Add to cart", then proceed to checkout. You can review your cart before confirming your order.'
    ],
    [
        'q' => 'Which payment methods are accepted?',
        'a' => 'We currently accept common card payments and wallet methods enabled in your region. All transactions are processed over secure, encrypted connections.'
    ],
    [
        'q' => 'What is my order status?',
        'a' => 'After checkout, your order starts as <strong>Pending</strong>. An admin may update it to <strong>Paid</strong>, <strong>Delivered</strong>, or <strong>Canceled</strong>. You can always see the current status on the <em>My Orders</em> page.'
    ],
    [
        'q' => 'When will my books arrive?',
        'a' => 'Delivery times vary by destination and shipping method. Estimates are shown at checkout. You will receive updates by email when your order progresses.'
    ],
    [
        'q' => 'Can I change or cancel an order?',
        'a' => 'If your order is still pending, contact us as soon as possible from the Contact page. Once shipped or delivered, changes are no longer possible.'
    ],
    [
        'q' => 'What is your returns & refunds policy?',
        'a' => 'You can request a return within 14 days of delivery if items are unused and in original condition. Refunds are issued according to our Terms & Conditions.'
    ],
    [
        'q' => 'How do discounts and coupon codes work?',
        'a' => 'Enter a valid code at checkout. Some codes apply automatically or only to specific titles. If a book discount exists, it will be reflected in the order total.'
    ],
    [
        'q' => 'How do I write a review?',
        'a' => 'Go to <em>My Orders</em> and click <strong>Review items</strong> next to the relevant order, then choose the book you want to review.'
    ],
    [
        'q' => 'How do I update my account details?',
        'a' => 'Open the Profile page after logging in. You can edit your name, email, password, and shipping details there.'
    ],
    [
        'q' => 'Is my data safe?',
        'a' => 'Yes. We apply modern security practices and never sell personal data. See our <a href="privacy.php">Privacy Policy</a> for details.'
    ],
];
?>

<div class="container-xxl py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="fw-bold m-0">Frequently Asked Questions</h1>
        <a href="contact.php" class="btn btn-outline-primary rounded-pill">Still need help? Contact us</a>
    </div>

    <!-- Quick search (client-side) -->
    <div class="input-group mb-4">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input id="faqSearch" type="search" class="form-control" placeholder="Search questions…">
    </div>

    <div class="accordion" id="faqAccordion">
        <?php foreach ($faqs as $i => $item):
            $qid  = 'faqQ' . $i;
            $cid  = 'faqC' . $i;
        ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="<?= h($qid) ?>">
                    <button class="accordion-button <?= $i ? 'collapsed' : '' ?>" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#<?= h($cid) ?>"
                        aria-expanded="<?= $i ? 'false' : 'true' ?>"
                        aria-controls="<?= h($cid) ?>">
                        <?= h($item['q']) ?>
                    </button>
                </h2>
                <div id="<?= h($cid) ?>" class="accordion-collapse collapse <?= $i ? '' : 'show' ?>"
                    aria-labelledby="<?= h($qid) ?>" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <?= $item['a'] ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <hr class="my-5">

    <div class="row g-4">
        <div class="col-md-4">
            <div class="p-3 border rounded-4 h-100">
                <h3 class="h6 fw-bold">Orders & Payments</h3>
                <p class="text-muted mb-2">Questions about checkout, payments, and order status.</p>
                <a href="#<?= h('faqQ0') ?>" class="link-primary">Placing an order</a><br>
                <a href="#<?= h('faqQ1') ?>" class="link-primary">Payment methods</a><br>
                <a href="#<?= h('faqQ2') ?>" class="link-primary">Order status</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 border rounded-4 h-100">
                <h3 class="h6 fw-bold">Shipping & Returns</h3>
                <p class="text-muted mb-2">Delivery windows, returns and refunds.</p>
                <a href="#<?= h('faqQ3') ?>" class="link-primary">Delivery times</a><br>
                <a href="#<?= h('faqQ4') ?>" class="link-primary">Changing/canceling</a><br>
                <a href="#<?= h('faqQ5') ?>" class="link-primary">Returns & refunds</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 border rounded-4 h-100">
                <h3 class="h6 fw-bold">Account & Reviews</h3>
                <p class="text-muted mb-2">Managing your profile and writing reviews.</p>
                <a href="#<?= h('faqQ7') ?>" class="link-primary">Write a review</a><br>
                <a href="#<?= h('faqQ8') ?>" class="link-primary">Update account</a><br>
                <a href="#<?= h('faqQ9') ?>" class="link-primary">Privacy & security</a>
            </div>
        </div>
    </div>
</div>

<!-- SEO: FAQPage structured data -->
<script type="application/ld+json">
    <?= json_encode([
        "@context" => "https://schema.org",
        "@type"    => "FAQPage",
        "mainEntity" => array_map(function ($f) {
            return [
                "@type" => "Question",
                "name"  => strip_tags($f['q']),
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text"  => $f['a'],
                ],
            ];
        }, $faqs)
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<!-- Tiny client-side filter -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const box = document.getElementById('faqSearch');
        const items = [...document.querySelectorAll('#faqAccordion .accordion-item')];

        box?.addEventListener('input', () => {
            const q = (box.value || '').toLowerCase().trim();
            items.forEach(el => {
                const txt = el.innerText.toLowerCase();
                el.style.display = q && !txt.includes(q) ? 'none' : '';
            });
        });
    });
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>