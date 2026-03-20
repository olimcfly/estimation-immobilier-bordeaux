<?php
use App\Controllers\AuthController;
$csrfToken = AuthController::generateCsrfToken();
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
      --primary-rgb: 139, 21, 56;
      --neutral-rgb: 0, 0, 0;
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

    .error-message {
      background: rgba(226, 75, 74, 0.08);
      border: 1px solid var(--danger);
      color: var(--danger);
      padding: 1rem 1.25rem;
      border-radius: 10px;
      margin-bottom: 1.5rem;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
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

    .password-wrapper {
      position: relative;
    }

    .password-wrapper input {
      padding-right: 3rem;
    }

    .password-toggle {
      position: absolute;
      right: 0.8rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: var(--muted);
      padding: 0.4rem;
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
  </style>
</head>
<body>

  <div class="login-container">

    <div class="login-header">
      <div class="login-icon">
        <i class="fas fa-lock"></i>
      </div>
      <h1>Espace Administrateur</h1>
      <p>Connectez-vous pour accéder au tableau de bord</p>
    </div>

    <?php if (!empty($error_message)): ?>
    <div class="error-message">
      <i class="fas fa-exclamation-circle"></i>
      <span><?= e($error_message) ?></span>
    </div>
    <?php endif; ?>

    <form method="POST" action="/admin/login" class="login-form">
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

      <div class="form-group">
        <label for="email">
          <i class="fas fa-envelope"></i>Adresse email
        </label>
        <input
          type="email"
          id="email"
          name="email"
          value="<?= e((string) ($old_email ?? '')) ?>"
          placeholder="contact@estimation-immobilier-bordeaux.fr"
          required
          autocomplete="email"
        >
      </div>

      <div class="form-group">
        <label for="password">
          <i class="fas fa-key"></i>Mot de passe
        </label>
        <div class="password-wrapper">
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Votre mot de passe"
            required
            autocomplete="current-password"
          >
          <button type="button" onclick="togglePassword()" class="password-toggle" aria-label="Afficher le mot de passe">
            <i id="toggle-icon" class="fas fa-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-submit">
        <i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i>Se connecter
      </button>
    </form>

    <p class="login-footer">
      <i class="fas fa-shield-alt"></i>Connexion sécurisée SSL
    </p>

  </div>

  <script>
  function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('toggle-icon');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }
  </script>

</body>
</html>
