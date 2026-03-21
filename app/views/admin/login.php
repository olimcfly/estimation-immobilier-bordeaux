<?php
use App\Controllers\AuthController;
$csrfToken = AuthController::generateCsrfToken();
$step = $step ?? 'email';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title><?= htmlspecialchars($page_title ?? 'Connexion Admin', ENT_QUOTES, 'UTF-8') ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <style>
    :root {
      --bg: #faf9f7;
      --surface: #ffffff;
      --text: #1a1410;
      --muted: #6b6459;
      --primary: #8B1538;
      --primary-dark: #6b0f2d;
      --border: #e8dfd7;
      --danger: #e24b4a;
      --success: #22c55e;
      --primary-rgb: 139, 21, 56;
      --neutral-rgb: 0, 0, 0;
      --success-rgb: 34, 197, 94;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #faf9f7 0%, #f3ece4 50%, #faf9f7 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .login-container {
      width: 100%;
      max-width: 440px;
    }

    .login-header {
      text-align: center;
      margin-bottom: 2.5rem;
    }

    .login-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 64px;
      height: 64px;
      background: linear-gradient(135deg, var(--primary), #C41E3A);
      border-radius: 16px;
      margin-bottom: 1rem;
      box-shadow: 0 8px 24px rgba(var(--primary-rgb), 0.25);
    }

    .login-icon i {
      font-size: 1.8rem;
      color: #fff;
    }

    .login-header h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      color: var(--text);
      margin: 0 0 0.5rem;
    }

    .login-header p {
      color: var(--muted);
      font-size: 0.95rem;
      margin: 0;
    }

    .alert {
      padding: 1rem 1.25rem;
      border-radius: 10px;
      margin-bottom: 1.5rem;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .alert-error {
      background: rgba(226, 75, 74, 0.08);
      border: 1px solid var(--danger);
      color: var(--danger);
    }

    .alert-success {
      background: rgba(34, 197, 94, 0.08);
      border: 1px solid var(--success);
      color: #15803d;
    }

    .login-form {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 4px 20px rgba(var(--neutral-rgb), 0.06);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      font-weight: 600;
      font-size: 0.9rem;
      color: var(--text);
      margin-bottom: 0.5rem;
    }

    .form-group label i {
      color: var(--primary);
      margin-right: 0.4rem;
    }

    .form-group input {
      width: 100%;
      padding: 0.9rem 1rem;
      border: 1px solid var(--border);
      border-radius: 10px;
      font-size: 1rem;
      font-family: inherit;
      transition: all 0.2s ease;
      background: var(--bg);
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.08);
    }

    .code-input {
      text-align: center;
      font-size: 1.8rem !important;
      letter-spacing: 0.6rem;
      font-weight: 700;
      padding: 1rem !important;
    }

    .btn-submit {
      width: 100%;
      padding: 1rem;
      background: linear-gradient(135deg, var(--primary), #C41E3A);
      color: #fff;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      font-size: 1rem;
      font-family: inherit;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.2);
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(var(--primary-rgb), 0.3);
    }

    .login-footer {
      text-align: center;
      margin-top: 2rem;
      color: var(--muted);
      font-size: 0.85rem;
    }

    .login-footer i {
      margin-right: 0.3rem;
    }

    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      color: var(--muted);
      text-decoration: none;
      font-size: 0.85rem;
      margin-top: 1.5rem;
      transition: color 0.2s;
    }

    .back-link:hover {
      color: var(--primary);
    }

    .email-display {
      background: rgba(var(--primary-rgb), 0.06);
      border-radius: 8px;
      padding: 0.6rem 1rem;
      font-size: 0.9rem;
      color: var(--text);
      font-weight: 500;
      margin-bottom: 1.5rem;
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="login-container">

    <div class="login-header">
      <div class="login-icon">
        <i class="fas fa-<?= $step === 'code' ? 'envelope-open-text' : 'lock' ?>"></i>
      </div>
      <h1>Espace Administrateur</h1>
      <?php if ($step === 'email'): ?>
        <p>Entrez votre adresse email pour recevoir un code de connexion</p>
      <?php else: ?>
        <p>Saisissez le code reçu par email</p>
      <?php endif; ?>
    </div>

    <?php if (!empty($error_message)): ?>
    <div class="alert alert-error">
      <i class="fas fa-exclamation-circle"></i>
      <span><?= e($error_message) ?></span>
    </div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
    <div class="alert alert-success">
      <i class="fas fa-check-circle"></i>
      <span><?= e($success_message) ?></span>
    </div>
    <?php endif; ?>

    <?php if ($step === 'email'): ?>

      <form method="POST" action="/admin/login" class="login-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <input type="hidden" name="action" value="send_code">

        <div class="form-group">
          <label for="email">
            <i class="fas fa-envelope"></i>Adresse email
          </label>
          <input
            type="email"
            id="email"
            name="email"
            required
            autocomplete="email"
            autofocus
          >
        </div>

        <button type="submit" class="btn-submit">
          <i class="fas fa-paper-plane" style="margin-right: 0.5rem;"></i>Recevoir mon code
        </button>
      </form>

    <?php else: ?>

      <div class="email-display">
        <i class="fas fa-envelope" style="margin-right: 0.4rem; color: var(--primary);"></i>
        <?= e($login_email ?? '') ?>
      </div>

      <div id="countdown-bar" style="background: rgba(var(--success-rgb), 0.08); border: 1px solid var(--success); border-radius: 10px; padding: 0.75rem 1rem; margin-bottom: 1.5rem; text-align: center; font-size: 0.85rem; color: #15803d;">
        <i class="fas fa-clock" style="margin-right: 0.4rem;"></i>
        Code valable pendant <strong id="countdown-timer">10:00</strong>
      </div>

      <form method="POST" action="/admin/login" class="login-form">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <input type="hidden" name="action" value="verify_code">
        <input type="hidden" name="email" value="<?= e($login_email ?? '') ?>">

        <div class="form-group">
          <label for="code">
            <i class="fas fa-key"></i>Code de connexion
          </label>
          <input
            type="text"
            id="code"
            name="code"
            class="code-input"
            maxlength="6"
            pattern="[0-9]{6}"
            inputmode="numeric"
            autocomplete="one-time-code"
            required
            autofocus
          >
        </div>

        <button type="submit" class="btn-submit" id="btn-verify">
          <i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i>Se connecter
        </button>
      </form>

      <div style="text-align: center; margin-top: 1.5rem;">
        <form method="POST" action="/admin/login" style="display: inline;">
          <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
          <input type="hidden" name="action" value="send_code">
          <input type="hidden" name="email" value="<?= e($login_email ?? '') ?>">
          <?php if (!empty($error_message)): ?>
          <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #f97316, #ea580c); margin-bottom: 0.5rem;">
            <i class="fas fa-redo" style="margin-right: 0.5rem;"></i>Renvoyer le code
          </button>
          <?php else: ?>
          <button type="submit" class="back-link" style="background: none; border: none; cursor: pointer; font-family: inherit; font-size: 0.85rem; color: var(--muted);">
            <i class="fas fa-redo"></i> Renvoyer le code
          </button>
          <?php endif; ?>
        </form>
      </div>

      <div style="text-align: center;">
        <a href="/admin/login" class="back-link">
          <i class="fas fa-arrow-left"></i>Utiliser une autre adresse
        </a>
      </div>

      <script>
      (function() {
        var totalSeconds = 10 * 60;
        var timerEl = document.getElementById('countdown-timer');
        var barEl = document.getElementById('countdown-bar');
        var btnEl = document.getElementById('btn-verify');

        function updateTimer() {
          if (totalSeconds <= 0) {
            barEl.style.background = 'rgba(226, 75, 74, 0.08)';
            barEl.style.borderColor = '#e24b4a';
            barEl.style.color = '#e24b4a';
            timerEl.textContent = 'expire';
            barEl.querySelector('i').className = 'fas fa-exclamation-triangle';
            barEl.innerHTML = '<i class="fas fa-exclamation-triangle" style="margin-right: 0.4rem;"></i> Code expire. Veuillez renvoyer un nouveau code.';
            btnEl.disabled = true;
            btnEl.style.opacity = '0.5';
            btnEl.style.cursor = 'not-allowed';
            return;
          }
          totalSeconds--;
          var m = Math.floor(totalSeconds / 60);
          var s = totalSeconds % 60;
          timerEl.textContent = m + ':' + (s < 10 ? '0' : '') + s;

          if (totalSeconds <= 60) {
            barEl.style.background = 'rgba(249, 115, 22, 0.08)';
            barEl.style.borderColor = '#f97316';
            barEl.style.color = '#f97316';
          }
        }

        setInterval(updateTimer, 1000);
      })();
      </script>

    <?php endif; ?>

    <p class="login-footer">
      <i class="fas fa-shield-alt"></i>Connexion sécurisée SSL
    </p>

  </div>

</body>
</html>
