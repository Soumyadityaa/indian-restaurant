<?php
declare(strict_types=1);
session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, password FROM admins WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid credentials. Please verify your username and password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign In | Zaffran &amp; Clay</title>
    <script>
        (function() {
            const saved = localStorage.getItem('zaffran_theme_preference');
            const theme = saved ? saved : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <header class="site-header">
        <div class="container navbar">
            <a href="../index.php" class="brand">
                <div class="brand-mark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 3c-1.5 2-1.5 4 0 6"/>
                        <path d="M4 13h16a6 6 0 0 1-6 6h-4a6 6 0 0 1-6-6Z"/>
                        <line x1="2" y1="21" x2="22" y2="21"/>
                    </svg>
                </div>
                <div class="brand-text">
                    <span class="brand-title">Zaffran Admin</span>
                    <span class="brand-subtitle">Kitchen Portal</span>
                </div>
            </a>

            <div class="nav-actions">
                <a href="../menu.php" class="btn btn-outline btn-sm">Public Menu</a>
                <button type="button" class="theme-toggle" data-theme-toggle aria-label="Toggle color theme">
                    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="5"/>
                        <line x1="12" y1="1" x2="12" y2="3"/>
                        <line x1="12" y1="21" x2="12" y2="23"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                        <line x1="1" y1="12" x2="3" y2="12"/>
                        <line x1="21" y1="12" x2="23" y2="12"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <main class="auth-wrapper">
        <div class="auth-card">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div class="feature-icon" style="margin: 0 auto 0.85rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h1>Administrator Sign In</h1>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.35rem;">Manage Indian Menu &amp; Customer Inquiries</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" novalidate>
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required autofocus autocomplete="username"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">
                    <span>Sign In to Kitchen Portal</span>
                </button>
            </form>
        </div>
    </main>

    <script src="../assets/script.js"></script>
</body>
</html>