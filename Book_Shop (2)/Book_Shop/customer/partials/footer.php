<?php
// partials/footer.php

// Make sure we can read categories even if footer is included standalone
if (!isset($conn)) {
  $db1 = __DIR__ . '/../includes/dbconnect.php';
  $db2 = __DIR__ . '/includes/dbconnect.php';
  if (file_exists($db1)) require_once $db1;
  elseif (file_exists($db2)) require_once $db2;
}

// Fetch categories (robust to different column names)
$categories = [];
if (isset($conn) && $conn instanceof PDO) {
  try {
    // Try common schemas
    $tries = [
      "SELECT category_id AS id, name AS label FROM categories ORDER BY name",
      "SELECT category_id AS id, category_name AS label FROM categories ORDER BY category_name",
      "SELECT id AS id, name AS label FROM categories ORDER BY name",
    ];
    foreach ($tries as $sql) {
      try {
        $st = $conn->query($sql);
        $rows = $st ? $st->fetchAll(PDO::FETCH_ASSOC) : [];
        if ($rows) {
          $categories = $rows;
          break;
        }
      } catch (Throwable $e) { /* try next */
      }
    }
  } catch (Throwable $e) { /* ignore */
  }
}
?>

</div> <!-- closes .container-xxl from header -->
</main> <!-- closes .bn-main from header -->

<footer class="bn-footer border-top mt-5">
  <div class="container-fluid py-5 px-4">
    <div class="row g-4 align-items-start">

      <!-- Brand -->
      <div class="col-12 col-lg-3">
        <div class="fs-4 fw-bold mb-2 d-flex align-items-center gap-2">
          <span><img src="assets\img\logo.png" style="width: 100px; height:100px;"></span> BookNest
        </div>
        <p class="text-muted mb-3">
          Minimal, fast, and reader-friendly. Powered by PHP &amp; MySQL.
        </p>
      </div>

      <!-- Support -->
      <div class="col-6 col-md-4 col-lg-2">
        <div class="fw-semibold mb-2">Support</div>
        <ul class="list-unstyled m-0 small">
          <li class="mb-2"><a class="text-decoration-none text-muted" href="about.php">About Us</a></li>
          <li class="mb-2"><a class="text-decoration-none text-muted" href="terms.php">Terms &amp; Conditions</a></li>
          <li class="mb-2"><a class="text-decoration-none text-muted" href="contact.php">Contact Us</a></li>
          <li class="mb-2"><a class="text-decoration-none text-muted" href="privacy.php">Privacy Policy</a></li>
          <li class="mb-2"><a class="text-decoration-none text-muted" href="faq.php">FAQ</a></li>
        </ul>
      </div>

      <!-- My Account -->
      <div class="col-6 col-md-4 col-lg-2">
        <div class="fw-semibold mb-2">My Account</div>
        <ul class="list-unstyled m-0 small">
          <li class="mb-2"><a class="text-decoration-none text-muted" href="login.php">Login</a></li>
          <li class="mb-2"><a class="text-decoration-none text-muted" href="signup.php">Sign up</a></li>
          <li class="mb-2"><a class="text-decoration-none text-muted" href="profile.php">Profile</a></li>
          <li class="mb-2"><a class="text-decoration-none text-muted" href="orders.php">Orders</a></li>
          <li class="mb-2"><a class="text-decoration-none text-muted" href="wishlist.php">Wishlists</a></li>
          <li class="mb-2"><a class="text-decoration-none text-muted" href="logout.php">Logout</a></li>
        </ul>
      </div>

      <!-- Categories (auto from DB) -->
      <div class="col-12 col-md-4 col-lg-3">
        <div class="fw-semibold mb-2">Categories</div>
        <?php if ($categories): ?>
          <div class="row g-1">
            <?php foreach ($categories as $c): ?>
              <div class="col-6">
                <a class="btn btn-sm btn-outline-secondary rounded-pill w-100 text-truncate"
                  href="product.php?category=<?= htmlspecialchars($c['id']) ?>">
                  <?= htmlspecialchars($c['label']) ?>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-muted small">No categories yet.</div>
        <?php endif; ?>
      </div>

      <!-- Explore -->
      <div class="col-12 col-lg-2">
        <div class="fw-semibold mb-2">Explore</div>
        <div class="d-flex flex-column gap-2">
          <a class="btn btn-sm btn-outline-secondary rounded-pill text-start" href="product.php">Shop</a>

        </div>
      </div>

      <!-- Newsletter -->
      <div class="col-12 col-lg-2">
        <div class="fw-semibold mb-2">Newsletter</div>
        <form class="d-flex gap-2" action="newsletter_subscribe.php" method="post" novalidate>
          <input class="form-control form-control-sm" type="email" name="email" placeholder="Your email" required>
          <button class="btn btn-primary btn-sm" type="submit">Subscribe</button>
        </form>
      </div>

    </div>


  </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="assets/js/main.js"></script>

<!-- Search clear -->
<script>
  (function() {
    const clearBtn = document.getElementById('bnSearchClear');
    const input = document.getElementById('bnSearchInput');
    if (clearBtn && input) {
      clearBtn.addEventListener('click', () => {
        input.value = '';
        input.focus();
      });
    }
  })();
</script>

<!-- Wishlist add/remove AJAX -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-wish-form').forEach(form => {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const btn = form.querySelector('.js-wish-btn');
        const icon = btn.querySelector('i');
        const fd = new FormData(form);

        btn.disabled = true;

        try {
          const res = await fetch(form.action, {
            method: 'POST',
            body: fd,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          });
          const data = await res.json();

          if (data.login) {
            window.location.href = data.url || 'login.php';
            return;
          }
          if (!data.ok) {
            console.warn(data.msg || 'Wishlist action failed');
            return;
          }

          const inWish = !!data.inWishlist;
          form.action = inWish ? 'wishlist_remove.php' : 'wishlist_add.php';

          btn.classList.toggle('btn-danger', inWish);
          btn.classList.toggle('btn-outline-secondary', !inWish);
          if (icon) icon.className = 'bi ' + (inWish ? 'bi-heart-fill' : 'bi-heart');
          btn.title = inWish ? 'Remove from Wishlist' : 'Add to Wishlist';
          btn.dataset.inwish = inWish ? '1' : '0';
        } catch (err) {
          console.error(err);
        } finally {
          btn.disabled = false;
        }
      });
    });
  });
</script>


<!-- ===== BookNest Chat Widget (with ready-made questions) ===== -->
<style>
  :root {
    --bn-primary: #1a73e8;
    --bn-primary-2: #1662c4;
    --bn-surface: #fff;
    --bn-ink: #0f172a;
    --bn-muted: #64748b;
    --bn-border: rgba(2, 6, 23, .08);
  }

  .bn-chat-btn {
    position: fixed;
    right: 18px;
    bottom: 18px;
    z-index: 1040;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: var(--bn-primary);
    color: #fff;
    border: none;
    box-shadow: 0 12px 28px rgba(26, 115, 232, .28);
    cursor: pointer;
    display: grid;
    place-items: center;
    font-size: 22px;
  }

  .bn-chat-btn:hover {
    background: var(--bn-primary-2);
    transform: translateY(-1px);
  }

  .bn-chat-panel {
    position: fixed;
    right: 18px;
    bottom: 84px;
    z-index: 1040;
    width: min(360px, 92vw);
    max-height: 68vh;
    display: none;
    flex-direction: column;
    background: var(--bn-surface);
    color: var(--bn-ink);
    border: 1px solid var(--bn-border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 18px 40px rgba(2, 6, 23, .18);
  }

  .bn-chat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 10px 12px;
    border-bottom: 1px solid var(--bn-border);
    background: linear-gradient(180deg, #f9fbff, #fff);
  }

  .bn-chat-header .title {
    font-weight: 800;
    letter-spacing: .2px;
  }

  .bn-chat-body {
    padding: 12px;
    overflow: auto;
    flex: 1;
    background: #fafbfe;
  }

  .bn-msg {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
  }

  .bn-msg .bubble {
    padding: 10px 12px;
    border-radius: 12px;
    max-width: 85%;
    border: 1px solid var(--bn-border);
    background: #fff;
    white-space: pre-wrap;
  }

  .bn-msg.me {
    justify-content: flex-end;
  }

  .bn-msg.me .bubble {
    background: #e9f1ff;
    border-color: #d7e6ff;
  }

  .bn-quick {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 6px 0 10px;
  }

  .bn-chip {
    border: 1px solid var(--bn-border);
    background: #fff;
    color: var(--bn-ink);
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 13px;
    cursor: pointer;
  }

  .bn-chip:hover {
    background: #f2f6ff;
  }

  .bn-chat-input {
    display: flex;
    gap: 8px;
    padding: 10px;
    border-top: 1px solid var(--bn-border);
    background: #fff;
  }

  .bn-chat-input input {
    flex: 1;
    border-radius: 12px;
    border: 1px solid var(--bn-border);
    height: 42px;
    padding: 0 12px;
  }

  .bn-chat-input button {
    border: 0;
    border-radius: 12px;
    padding: 0 14px;
    height: 42px;
    font-weight: 700;
    background: var(--bn-primary);
    color: #fff;
  }

  .bn-chat-input button:hover {
    background: var(--bn-primary-2);
  }
</style>

<button id="bnChatBtn" class="bn-chat-btn" aria-label="Chat">💬</button>

<div id="bnChatPanel" class="bn-chat-panel" role="dialog" aria-label="BookNest Chat">
  <div class="bn-chat-header">
    <div class="title">BookNest Assistant</div>
    <button id="bnChatClose" class="btn btn-sm btn-outline-secondary">Close</button>
  </div>

  <div id="bnChatBody" class="bn-chat-body">
    <div class="bn-msg">
      <div class="bubble">
        Hi! I can help with BookNest questions like:
        • How many books are available?
        • What categories do you have?
        • Any promotions now?
        • How to place an order?
      </div>
    </div>

    <!-- Quick question chips -->
    <div id="bnQuick" class="bn-quick"></div>
  </div>

  <form id="bnChatForm" class="bn-chat-input">
    <input id="bnChatInput" type="text" placeholder="Type your message…" autocomplete="off" />
    <button type="submit">Send</button>
  </form>
</div>

<script>
  (function() {
    const btn = document.getElementById('bnChatBtn');
    const panel = document.getElementById('bnChatPanel');
    const close = document.getElementById('bnChatClose');
    const body = document.getElementById('bnChatBody');
    const form = document.getElementById('bnChatForm');
    const input = document.getElementById('bnChatInput');
    const quick = document.getElementById('bnQuick');

    const ready = [
      "How many books do you have?",
      "What categories are available?",
      "Any promotions/discounts now?",
      "How do I place an order?",
      "Do you have free shipping?",
      "Where can I see my orders?",
      "How do I write a review?",
      "How do I contact support?",
      "What payment methods do you accept?"
    ];


    function renderChips() {
      quick.innerHTML = '';
      ready.forEach(q => {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'bn-chip';
        b.textContent = q;
        b.addEventListener('click', () => ask(q));
        quick.appendChild(b);
      });
    }

    function toggle(open) {
      panel.style.display = open ? 'flex' : 'none';
      if (open) {
        setTimeout(() => input.focus(), 100);
      }
    }
    btn.addEventListener('click', () => toggle(panel.style.display !== 'flex'));
    close.addEventListener('click', () => toggle(false));

    function addMsg(text, me = false) {
      const wrap = document.createElement('div');
      wrap.className = 'bn-msg' + (me ? ' me' : '');
      wrap.innerHTML = `<div class="bubble"></div>`;
      wrap.querySelector('.bubble').textContent = text;
      body.appendChild(wrap);
      body.scrollTop = body.scrollHeight;
    }

    async function ask(text) {
      addMsg(text, true);
      addMsg('…'); // typing
      try {
        const res = await fetch('chatbot.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            message: text
          })
        });
        const data = await res.json();
        const last = body.querySelector('.bn-msg:last-child .bubble');
        last.textContent = (data && data.reply) ? data.reply : 'Sorry, I could not get a reply.';
      } catch (e) {
        const last = body.querySelector('.bn-msg:last-child .bubble');
        last.textContent = 'Network error. Please try again.';
      }
    }

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const text = input.value.trim();
      if (!text) return;
      input.value = '';
      ask(text);
    });

    renderChips();
  })();
</script>
<!-- ===== /BookNest Chat Widget ===== -->



</body>

</html>