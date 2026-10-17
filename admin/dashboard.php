<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$feedback = '';

// Handle Contact Inquiry Actions (Toggle Read/Unread or Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inquiry_action'])) {
    $msgId = filter_input(INPUT_POST, 'message_id', FILTER_VALIDATE_INT);
    $action = (string)$_POST['inquiry_action'];

    if ($msgId) {
        if ($action === 'toggle_status') {
            $newStatus = ($_POST['current_status'] ?? 'Unread') === 'Unread' ? 'Read' : 'Unread';
            $stmt = $pdo->prepare('UPDATE contact_messages SET status = :status WHERE id = :id');
            $stmt->execute([':status' => $newStatus, ':id' => $msgId]);
            $feedback = "Inquiry #{$msgId} marked as {$newStatus}.";
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM contact_messages WHERE id = :id');
            $stmt->execute([':id' => $msgId]);
            $feedback = "Customer inquiry #{$msgId} deleted.";
        }
    }
}

// Fetch Menu & Inquiry Statistics
$menuStats = $pdo->query(
    "SELECT
        COUNT(*) AS total_items,
        SUM(CASE WHEN diet_type = 'Veg' THEN 1 ELSE 0 END) AS veg_items,
        SUM(CASE WHEN diet_type = 'Non-Veg' THEN 1 ELSE 0 END) AS nonveg_items
     FROM menu_items"
)->fetch();

$msgStats = $pdo->query(
    "SELECT
        COUNT(*) AS total_messages,
        SUM(CASE WHEN status = 'Unread' THEN 1 ELSE 0 END) AS unread_messages
     FROM contact_messages"
)->fetch();

// Fetch All Contact Inquiries
$inquiriesStmt = $pdo->query('SELECT id, full_name, email, phone, subject, message, status, created_at FROM contact_messages ORDER BY status ASC, id DESC');
$inquiries = $inquiriesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard &amp; Inquiries | Zaffran &amp; Clay</title>
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
            <a href="dashboard.php" class="brand">
                <div class="brand-mark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 3c-1.5 2-1.5 4 0 6"/>
                        <path d="M4 13h16a6 6 0 0 1-6 6h-4a6 6 0 0 1-6-6Z"/>
                        <line x1="2" y1="21" x2="22" y2="21"/>
                    </svg>
                </div>
                <div class="brand-text">
                    <span class="brand-title">Zaffran Admin</span>
                    <span class="brand-subtitle">Management Desk</span>
                </div>
            </a>

            <ul id="primaryNav" class="nav-links">
                <li><a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">Dashboard &amp; Inquiries</a></li>
                <li><a href="manage_menu.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'manage_menu.php' ? 'active' : ''; ?>">Manage Menu</a></li>
                <li><a href="../menu.php" class="nav-link" target="_blank">Live Menu</a></li>
                <li><a href="logout.php" class="nav-link" style="color: var(--danger-color);">Sign Out</a></li>
            </ul>

            <div class="nav-actions">
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

                <button type="button" class="mobile-menu-toggle" data-nav-toggle aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle admin navigation">
                    <svg class="icon-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                    <svg class="icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <main class="section">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
                <div>
                    <span class="eyebrow">Operations &amp; Guest Relations</span>
                    <h1>Namaste, <?php echo htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8'); ?></h1>
                </div>
                <a href="manage_menu.php" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>Add / Edit Indian Dishes</span>
                </a>
            </div>

            <?php if ($feedback !== ''): ?>
                <div class="alert alert-success" role="status">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span><?php echo htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>

            <!-- Overview Metrics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="feature-icon" style="margin-bottom: 0;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 13h16a6 6 0 0 1-6 6h-4a6 6 0 0 1-6-6Z"/>
                            <line x1="2" y1="21" x2="22" y2="21"/>
                        </svg>
                    </div>
                    <div>
                        <div class="stat-value"><?php echo (int)$menuStats['total_items']; ?></div>
                        <div class="stat-label">Total Dishes (<?php echo (int)$menuStats['veg_items']; ?> Veg / <?php echo (int)$menuStats['nonveg_items']; ?> Non-Veg)</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="feature-icon" style="margin-bottom: 0;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="stat-value"><?php echo (int)$msgStats['unread_messages']; ?></div>
                        <div class="stat-label">Unread Guest Inquiries</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="feature-icon" style="margin-bottom: 0;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div>
                        <div class="stat-value"><?php echo (int)$msgStats['total_messages']; ?></div>
                        <div class="stat-label">Total Contact Submissions</div>
                    </div>
                </div>
            </div>

            <!-- Customer Contact Us & Table Reservations Management Table -->
            <section class="panel-card">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                    <h2 style="margin: 0; padding: 0; border: 0;">Customer Contact &amp; Table Inquiries (<?php echo count($inquiries); ?>)</h2>
                    <a href="../index.php#contact" target="_blank" class="btn btn-outline btn-sm">View Public Contact Form</a>
                </div>

                <?php if (empty($inquiries)): ?>
                    <p style="color: var(--text-secondary);">No customer inquiries have been submitted yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Guest Details</th>
                                    <th>Subject &amp; Message</th>
                                    <th>Received On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inquiries as $inq): ?>
                                    <?php $isUnread = ($inq['status'] === 'Unread'); ?>
                                    <tr>
                                        <td>
                                            <span class="status-badge <?php echo $isUnread ? 'status-unread' : 'status-read'; ?>">
                                                <?php echo htmlspecialchars($inq['status'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </td>
                                        <td style="min-width: 190px;">
                                            <strong style="display: block; color: var(--text-primary);">
                                                <?php echo htmlspecialchars($inq['full_name'], ENT_QUOTES, 'UTF-8'); ?>
                                            </strong>
                                            <span style="display: block; font-size: 0.82rem; color: var(--accent-primary); font-weight: 600;">
                                                <?php echo htmlspecialchars($inq['phone'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                            <span style="display: block; font-size: 0.8rem; color: var(--text-secondary);">
                                                <?php echo htmlspecialchars($inq['email'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </td>
                                        <td style="max-width: 420px;">
                                            <strong style="display: block; margin-bottom: 0.2rem;">
                                                <?php echo htmlspecialchars($inq['subject'], ENT_QUOTES, 'UTF-8'); ?>
                                            </strong>
                                            <p style="color: var(--text-secondary); font-size: 0.86rem; white-space: pre-line;">
                                                <?php echo htmlspecialchars($inq['message'], ENT_QUOTES, 'UTF-8'); ?>
                                            </p>
                                        </td>
                                        <td style="white-space: nowrap; font-size: 0.82rem; color: var(--text-muted);">
                                            <?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($inq['created_at'])), ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <form method="POST" action="dashboard.php" style="display: inline;">
                                                    <input type="hidden" name="inquiry_action" value="toggle_status">
                                                    <input type="hidden" name="message_id" value="<?php echo (int)$inq['id']; ?>">
                                                    <input type="hidden" name="current_status" value="<?php echo htmlspecialchars($inq['status'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <button type="submit" class="btn btn-outline btn-sm">
                                                        <?php echo $isUnread ? 'Mark Read' : 'Mark Unread'; ?>
                                                    </button>
                                                </form>

                                                <form method="POST" action="dashboard.php" data-confirm-delete data-item-name="inquiry from <?php echo htmlspecialchars($inq['full_name'], ENT_QUOTES, 'UTF-8'); ?>" style="display: inline;">
                                                    <input type="hidden" name="inquiry_action" value="delete">
                                                    <input type="hidden" name="message_id" value="<?php echo (int)$inq['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <script src="../assets/script.js"></script>
</body>
</html>