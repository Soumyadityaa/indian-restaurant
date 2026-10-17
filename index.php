<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';

$contactSuccess = '';
$contactError = '';

// Handle Contact Us Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $fullName = trim((string)($_POST['full_name'] ?? ''));
    $email    = trim((string)($_POST['email'] ?? ''));
    $phone    = trim((string)($_POST['phone'] ?? ''));
    $subject  = trim((string)($_POST['subject'] ?? ''));
    $message  = trim((string)($_POST['message'] ?? ''));

    if ($fullName === '' || $email === '' || $phone === '' || $subject === '' || $message === '') {
        $contactError = 'Please complete all required fields before sending your inquiry.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contactError = 'Please provide a valid email address.';
    } else {
        $insertStmt = $pdo->prepare(
            'INSERT INTO contact_messages (full_name, email, phone, subject, message)
             VALUES (:full_name, :email, :phone, :subject, :message)'
        );
        $insertStmt->execute([
            ':full_name' => mb_substr($fullName, 0, 100),
            ':email'     => mb_substr($email, 0, 120),
            ':phone'     => mb_substr($phone, 0, 30),
            ':subject'   => mb_substr($subject, 0, 120),
            ':message'   => mb_substr($message, 0, 2000),
        ]);
        $contactSuccess = 'Dhanyawad! Your inquiry has been received. Our hospitality team will reach out shortly.';
        $_POST = [];
    }
}

// Fetch 3 Signature Dishes for Homepage Preview
$featuredStmt = $pdo->query('SELECT id, name, category, diet_type, description, price, image_path FROM menu_items ORDER BY id ASC LIMIT 3');
$featuredDishes = $featuredStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zaffran &amp; Clay | Authentic Indian Tandoor &amp; Royal Kitchen</title>
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

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="container hero-grid">
                <div>
                    <span class="eyebrow">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L15 8L21 9L16.5 14L18 20L12 17L6 20L7.5 14L3 9L9 8L12 2Z"/>
                        </svg>
                        Hand-Roasted Masala &amp; Earthen Tandoor
                    </span>
                    <h1>Where Royal Awadhi Heritage Meets Smoky Tandoor Craft.</h1>
                    <p>Savor slow-simmered handi biryanis, charcoal-kissed kebabs, and stone-ground regional curries crafted fresh with pure Kashmiri saffron and farm-fresh produce.</p>

                    <div class="hero-actions">
                        <a href="menu.php" class="btn btn-primary">
                            <span>Explore Full Indian Menu</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                        <a href="#contact" class="btn btn-outline">Book a Table / Catering</a>
                    </div>

                    <div class="delivery-strip">
                        <span class="delivery-strip-label">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="5.5" cy="17.5" r="2.5"/>
                                <circle cx="18.5" cy="17.5" r="2.5"/>
                                <path d="M15 6h4l3 5v6.5h-2"/>
                                <path d="M3 17.5H1v-11h14v11H8"/>
                            </svg>
                            Order Hot Delivery Via:
                        </span>
                        <a href="https://www.swiggy.com" target="_blank" rel="noopener noreferrer" class="btn btn-swiggy">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2C7.58 2 4 5.58 4 10c0 5.25 8 12 8 12s8-6.75 8-12c0-4.42-3.58-8-8-8z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>Order on Swiggy</span>
                        </a>
                        <a href="https://www.zomato.com" target="_blank" rel="noopener noreferrer" class="btn btn-zomato">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                                <line x1="6" y1="1" x2="6" y2="4"/>
                                <line x1="10" y1="1" x2="10" y2="4"/>
                                <line x1="14" y1="1" x2="14" y2="4"/>
                            </svg>
                            <span>Order on Zomato</span>
                        </a>
                    </div>
                </div>

                <aside class="hero-card">
                    <h3>Dastarkhwan Timings &amp; Hospitality</h3>
                    <ul class="info-list">
                        <li class="info-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <div>
                                <strong>Lunch &amp; Dinner Service</strong>
                                <span>Daily: 12:00 PM &ndash; 11:30 PM<br>Live Tandoor Counter from 6:00 PM</span>
                            </div>
                        </li>
                        <li class="info-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <div>
                                <strong>Flagship Dining Room</strong>
                                <span>City Centre Commercial Hub, Durgapur, West Bengal 713216</span>
                            </div>
                        </li>
                        <li class="info-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            <div>
                                <strong>Direct Table Desk &amp; Bulk Orders</strong>
                                <span>+91 98300 11223 &bull; Party &amp; Event Catering</span>
                            </div>
                        </li>
                    </ul>
                </aside>
            </div>
        </section>

        <!-- Signature Dishes Preview -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <span class="eyebrow">Chef's Royal Signatures</span>
                    <h2>From Our Earthen Clay Oven &amp; Handi</h2>
                    <div class="ornament-divider">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15 9 22 9 16 14 18 21 12 17 6 21 8 14 2 9 9 9 12 2"/>
                        </svg>
                    </div>
                    <p>A taste of our most celebrated North Indian, Mughlai, and Awadhi delicacies.</p>
                </div>

                <div class="menu-grid">
                    <?php foreach ($featuredDishes as $dish): ?>
                        <?php
                            $imgPath = $dish['image_path'];
                            $hasImg = !empty($imgPath) && file_exists(__DIR__ . '/' . $imgPath);
                            $isVeg = ($dish['diet_type'] === 'Veg');
                        ?>
                        <article class="menu-card">
                            <div class="menu-card-media">
                                <div class="badge-overlay">
                                    <span class="category-badge"><?php echo htmlspecialchars($dish['category'], ENT_QUOTES, 'UTF-8'); ?></span>
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

                                <?php if ($hasImg): ?>
                                    <img src="<?php echo htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="<?php echo htmlspecialchars($dish['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="menu-placeholder">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                            <path d="M12 3c-1.5 2-1.5 4 0 6"/>
                                            <path d="M4 13h16a6 6 0 0 1-6 6h-4a6 6 0 0 1-6-6Z"/>
                                            <line x1="2" y1="21" x2="22" y2="21"/>
                                        </svg>
                                        <span>Royal Handi Presentation</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="menu-card-body">
                                <div class="menu-card-header">
                                    <h3 class="menu-card-title"><?php echo htmlspecialchars($dish['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <span class="menu-card-price">&#8377;<?php echo number_format((float)$dish['price'], 2); ?></span>
                                </div>
                                <p class="menu-card-desc"><?php echo htmlspecialchars($dish['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <div class="menu-card-footer">
                                    <span>Freshly Prepared</span>
                                    <div class="mini-order-links">
                                        <a href="https://www.swiggy.com" target="_blank" rel="noopener noreferrer" class="mini-link mini-swiggy">Swiggy</a>
                                        <a href="https://www.zomato.com" target="_blank" rel="noopener noreferrer" class="mini-link mini-zomato">Zomato</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div style="text-align: center; margin-top: 2.5rem;">
                    <a href="menu.php" class="btn btn-primary">View Complete Menu &amp; Filter by Category</a>
                </div>
            </div>
        </section>

        <!-- Indian Culinary Pillars -->
        <section class="section section-alt">
            <div class="container">
                <div class="section-header">
                    <span class="eyebrow">Our Culinary Craft</span>
                    <h2>Authentic Indian Foodie Vibes</h2>
                    <p>Rooted in centuries-old spice blending traditions and slow earthen cooking.</p>
                </div>

                <div class="features-grid">
                    <article class="feature-card">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 2c1 3 4 5 4 9a4 4 0 1 1-8 0c0-4 3-6 4-9z"/>
                                <path d="M5 14a7 7 0 0 0 14 0"/>
                            </svg>
                        </div>
                        <h3>Live Clay Tandoor</h3>
                        <p>Our skewered kebabs, tikkas, and artisan naans are roasted at 450°C inside a seasoned clay tandoor fired with natural hardwood charcoal.</p>
                    </article>

                    <article class="feature-card">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 3v18"/>
                                <path d="M3 12h18"/>
                            </svg>
                        </div>
                        <h3>In-House Chakki Masalas</h3>
                        <p>We sun-dry and stone-grind whole Malabar black pepper, green cardamom, mace, and Kashmiri chillies daily for unmatched aroma.</p>
                    </article>

                    <article class="feature-card">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 11h16a1 1 0 0 1 1 1v.5a8.5 8.5 0 0 1-17 0V12a1 1 0 0 1 1-1Z"/>
                                <path d="M7 7h10"/>
                                <path d="M9 4h6"/>
                            </svg>
                        </div>
                        <h3>Sealed Dum Pukht</h3>
                        <p>Our biryanis are sealed with hand-rolled atta dough so the basmati grains absorb every drop of saffron, mint, and spice infusion.</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Contact Us, Address Map Iframe & Social Connect -->
        <section id="contact" class="section">
            <div class="container">
                <div class="section-header">
                    <span class="eyebrow">Atithi Devo Bhava</span>
                    <h2>Contact Us, Reservations &amp; Location</h2>
                    <p>Send a table reservation request, catering inquiry, or feedback directly to our management desk.</p>
                </div>

                <div class="contact-grid">
                    <!-- Contact Form -->
                    <div class="panel-card">
                        <h2>Send an Inquiry or Book a Table</h2>

                        <?php if ($contactError !== ''): ?>
                            <div class="alert alert-danger" role="alert">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                <span><?php echo htmlspecialchars($contactError, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($contactSuccess !== ''): ?>
                            <div class="alert alert-success" role="status">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                <span><?php echo htmlspecialchars($contactSuccess, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="index.php#contact">
                            <input type="hidden" name="contact_submit" value="1">

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="full_name" class="form-label">Your Full Name</label>
                                    <input type="text" id="full_name" name="full_name" class="form-control" required maxlength="100"
                                           placeholder="e.g., Rohan Sen"
                                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="phone" class="form-label">Phone / WhatsApp Number</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" required maxlength="30"
                                           placeholder="+91 98300 XXXXX"
                                           value="<?php echo htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control" required maxlength="120"
                                           placeholder="you@example.com"
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="subject" class="form-label">Inquiry Type</label>
                                    <select id="subject" name="subject" class="form-control" required>
                                        <option value="Table Reservation">Table Reservation</option>
                                        <option value="Party / Bulk Biryani Catering">Party / Bulk Biryani Catering</option>
                                        <option value="Feedback &amp; Appreciation">Feedback &amp; Appreciation</option>
                                        <option value="General Inquiry">General Inquiry</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="message" class="form-label">Your Message / Guest Count &amp; Preferred Time</label>
                                <textarea id="message" name="message" class="form-control" required placeholder="Share your preferred dining date, number of guests, or catering requirements..."><?php echo htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                                <span>Submit Inquiry to Restaurant Desk</span>
                            </button>
                        </form>
                    </div>

                    <!-- Address Details, Social Handles & Embedded Map Iframe -->
                    <div>
                        <div class="panel-card">
                            <h2>Visit Our Dining Hall</h2>
                            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                                Located in the heart of City Centre, Durgapur. Ample parking and family dining booths available.
                            </p>

                            <div style="display: grid; gap: 0.75rem; font-size: 0.92rem; margin-bottom: 1.25rem;">
                                <div><strong>Address:</strong> Plot 14, City Centre Commercial Enclave, Durgapur, West Bengal 713216</div>
                                <div><strong>Reservations:</strong> +91 98300 11223 / +91 343 254 8899</div>
                                <div><strong>Email:</strong> namaste@zaffranandclay.in</div>
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                                <span style="font-weight: 600; font-size: 0.88rem;">Follow Our Foodie Journey:</span>
                                <div class="social-links">
                                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="Instagram" title="Instagram">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                                        </svg>
                                    </a>
                                    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="Facebook" title="Facebook">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                                        </svg>
                                    </a>
                                    <a href="https://x.com" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="X (Twitter)" title="X (Twitter)">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M4 4l11.733 16h4.267l-11.733 -16z"/>
                                            <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"/>
                                        </svg>
                                    </a>
                                    <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="YouTube" title="YouTube">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/>
                                            <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="map-frame-wrapper">
                            <iframe
                                title="Zaffran and Clay Location Map"
                                src="https://maps.google.com/maps?q=City+Centre,Durgapur,West+Bengal&t=&z=14&ie=UTF8&iwloc=&output=embed"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="brand" style="margin-bottom: 0.85rem;">
                        <div class="brand-mark">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 3c-1.5 2-1.5 4 0 6"/>
                                <path d="M4 13h16a6 6 0 0 1-6 6h-4a6 6 0 0 1-6-6Z"/>
                                <line x1="2" y1="21" x2="22" y2="21"/>
                            </svg>
                        </div>
                        <div class="brand-text">
                            <span class="brand-title">Zaffran &amp; Clay</span>
                            <span class="brand-subtitle">Royal Indian Kitchen</span>
                        </div>
                    </div>
                    <p style="max-width: 340px; margin-bottom: 1rem;">
                        Celebrating India's rich culinary tapestry with authentic tandoor roasts, Awadhi biryanis, and warm hospitality.
                    </p>
                </div>

                <div>
                    <h4 style="margin-bottom: 0.85rem;">Order Online</h4>
                    <p style="margin-bottom: 0.85rem;">Get piping hot biryanis and kebabs delivered to your doorstep:</p>
                    <div style="display: flex; gap: 0.65rem; flex-wrap: wrap;">
                        <a href="https://www.swiggy.com" target="_blank" rel="noopener noreferrer" class="btn btn-swiggy">Swiggy</a>
                        <a href="https://www.zomato.com" target="_blank" rel="noopener noreferrer" class="btn btn-zomato">Zomato</a>
                    </div>
                </div>

                <div>
                    <h4 style="margin-bottom: 0.85rem;">Connect &amp; Navigate</h4>
                    <ul style="list-style: none; display: grid; gap: 0.45rem; margin-bottom: 1rem;">
                        <li><a href="menu.php">Explore Seasonal Menu</a></li>
                        <li><a href="#contact">Book a Table / Inquiry</a></li>
                        <li><a href="admin/login.php">Staff &amp; Admin Portal</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; <?php echo date('Y'); ?> Zaffran &amp; Clay Indian Kitchen. All rights reserved.</span>
                <span>Crafted with Authentic Indian Flavors &bull; FSSAI Compliant</span>
            </div>
        </div>
    </footer>

    <script src="assets/script.js"></script>
</body>
</html>