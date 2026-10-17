<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';

$stmt = $pdo->query('SELECT id, name, category, diet_type, description, price, image_path FROM menu_items ORDER BY id DESC');
$menuItems = $stmt->fetchAll();

$categories = [
    'Starters & Tandoor',
    'Main Course',
    'Biryani & Rice',
    'Artisan Breads',
    'Desserts & Beverages',
];
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Royal Indian Menu | Zaffran &amp; Clay</title>
    <script>
        (function() {
            const saved = localStorage.getItem('zaffran_theme_preference');
            const theme = saved ? saved : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="site-header">
        <div class="container navbar">
            <a href="index.php" class="brand">
                <div class="brand-mark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 3c-1.5 2-1.5 4 0 6"/>
                        <path d="M8 5c-1 1.5-1 3 0 4.5"/>
                        <path d="M16 5c-1 1.5-1 3 0 4.5"/>
                        <path d="M4 13h16a6 6 0 0 1-6 6h-4a6 6 0 0 1-6-6Z"/>
                        <line x1="2" y1="21" x2="22" y2="21"/>
                    </svg>
                </div>
                <div class="brand-text">
                    <span class="brand-title">Zaffran &amp; Clay</span>
                    <span class="brand-subtitle">Royal Indian Kitchen</span>
                </div>
            </a>

            <ul id="primaryNav" class="nav-links">
                <li><a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="menu.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'menu.php' ? 'active' : ''; ?>">Royal Menu</a></li>
                <li><a href="index.php#contact" class="nav-link">Contact &amp; Map</a></li>
                <li><a href="admin/login.php" class="nav-link">Admin</a></li>
            </ul>

            <div class="nav-actions">
                <button type="button" class="theme-toggle" data-theme-toggle aria-label="Toggle color theme" title="Toggle Dark/Light Theme">
                    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
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
                    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>

                <button type="button" class="mobile-menu-toggle" data-nav-toggle aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle navigation menu">
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
            <div class="section-header">
                <span class="eyebrow">Dastarkhwan-e-Khas</span>
                <h2>Explore Our Indian Culinary Menu</h2>
                <div class="ornament-divider">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15 9 22 9 16 14 18 21 12 17 6 21 8 14 2 9 9 9 12 2"/>
                    </svg>
                </div>
                <p>Filter by course category, dietary preference, or search for your favorite kebabs, curries, and biryanis.</p>

                <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.25rem;">
                    <a href="https://www.swiggy.com" target="_blank" rel="noopener noreferrer" class="btn btn-swiggy">
                        <span>Order Full Menu on Swiggy</span>
                    </a>
                    <a href="https://www.zomato.com" target="_blank" rel="noopener noreferrer" class="btn btn-zomato">
                        <span>Order Full Menu on Zomato</span>
                    </a>
                </div>
            </div>

            <!-- Live Search, Diet Toggle & Category Filter Bar -->
            <div class="menu-controls">
                <div class="search-row">
                    <div class="search-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="search" id="menuSearchInput" class="search-input" placeholder="Search dishes (e.g., Biryani, Paneer, Makhani, Naan, Saffron)..." aria-label="Search menu items">
                    </div>

                    <div class="diet-toggle-group" role="group" aria-label="Dietary Preference Filter">
                        <button type="button" class="diet-btn active" data-filter-diet="all">All Dishes (<span id="menuVisibleCount"><?php echo count($menuItems); ?></span>)</button>
                        <button type="button" class="diet-btn" data-filter-diet="Veg">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="12" cy="12" r="5" fill="#15803d"/>
                            </svg>
                            <span>Pure Veg</span>
                        </button>
                        <button type="button" class="diet-btn" data-filter-diet="Non-Veg">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#b91c1c" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <polygon points="12 7 17 16 7 16" fill="#b91c1c"/>
                            </svg>
                            <span>Non-Veg</span>
                        </button>
                    </div>
                </div>

                <div class="category-pills" role="tablist" aria-label="Menu Categories">
                    <button type="button" class="category-pill active" data-filter-category="all">All Courses</button>
                    <?php foreach ($categories as $cat): ?>
                        <button type="button" class="category-pill" data-filter-category="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (empty($menuItems)): ?>
                <div class="panel-card" style="text-align: center; padding: 3rem;">
                    <h3>Our kitchen menu is being updated.</h3>
                    <p style="color: var(--text-secondary); margin-top: 0.5rem;">Please check back shortly or log into the admin portal to publish dishes.</p>
                </div>
            <?php else: ?>
                <div class="menu-grid" id="publicMenuGrid">
                    <?php foreach ($menuItems as $item): ?>
                        <?php
                            $imagePath = $item['image_path'];
                            $hasValidImage = !empty($imagePath) && file_exists(__DIR__ . '/' . $imagePath);
                            $isVeg = ($item['diet_type'] === 'Veg');
                        ?>
                        <article class="menu-card"
                                 data-menu-item
                                 data-name="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                 data-desc="<?php echo htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?>"
                                 data-category="<?php echo htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8'); ?>"
                                 data-diet="<?php echo htmlspecialchars($item['diet_type'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="menu-card-media">
                                <div class="badge-overlay">
                                    <span class="category-badge"><?php echo htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="diet-indicator <?php echo $isVeg ? 'diet-veg' : 'diet-nonveg'; ?>">
                                        <?php if ($isVeg): ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                                <circle cx="12" cy="12" r="5" fill="currentColor"/>
                                            </svg>
                                            <span>VEG</span>
                                        <?php else: ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                                <polygon points="12 7 17 16 7 16" fill="currentColor"/>
                                            </svg>
                                            <span>NON-VEG</span>
                                        <?php endif; ?>
                                    </span>
                                </div>

                                <?php if ($hasValidImage): ?>
                                    <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="menu-placeholder">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <path d="M12 3c-1.5 2-1.5 4 0 6"/>
                                            <path d="M4 13h16a6 6 0 0 1-6 6h-4a6 6 0 0 1-6-6Z"/>
                                            <line x1="2" y1="21" x2="22" y2="21"/>
                                        </svg>
                                        <span>Authentic Handi Dish</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="menu-card-body">
                                <div class="menu-card-header">
                                    <h3 class="menu-card-title"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <span class="menu-card-price">&#8377;<?php echo number_format((float)$item['price'], 2); ?></span>
                                </div>
                                <p class="menu-card-desc"><?php echo nl2br(htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8')); ?></p>
                                <div class="menu-card-footer">
                                    <span>Prepared Fresh on Order</span>
                                    <div class="mini-order-links">
                                        <a href="https://www.swiggy.com" target="_blank" rel="noopener noreferrer" class="mini-link mini-swiggy">Swiggy</a>
                                        <a href="https://www.zomato.com" target="_blank" rel="noopener noreferrer" class="mini-link mini-zomato">Zomato</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div id="menuNoResults" class="panel-card" style="display: none; text-align: center; padding: 3rem; margin-top: 1.5rem;">
                    <h3>No dishes match your current filter.</h3>
                    <p style="color: var(--text-secondary); margin-top: 0.45rem;">Try clearing your search keyword or switching to "All Courses".</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container footer-bottom">
            <span>&copy; <?php echo date('Y'); ?> Zaffran &amp; Clay Indian Kitchen. All rights reserved.</span>
            <span><a href="index.php">Home</a> &bull; <a href="index.php#contact">Contact &amp; Map</a> &bull; <a href="admin/login.php">Staff Access</a></span>
        </div>
    </footer>

    <script src="assets/script.js"></script>
</body>
</html>