# Zaffran & Clay — Royal Indian Kitchen & Menu CMS

![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-InnoDB-4479A1?style=flat-square&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-Semantic-E34F26?style=flat-square&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-Custom_Properties-1572B6?style=flat-square&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-Vanilla_ES6%2B-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![License](https://img.shields.io/badge/License-MIT-success?style=flat-square)

**Zaffran & Clay** is a lightweight, secure, full-stack Indian restaurant website and content management system (CMS) engineered for local **XAMPP** and standard LAMP/LEMP environments. Built without heavy external frameworks, it pairs an earthy terracotta, saffron, and tandoor-charcoal design language with a hardened **PHP 8+ (PDO)** backend.

---

## Key Features

### Public Dining Portal (`index.php` & `menu.php`)
* **Authentic Indian Aesthetic:** Custom terracotta and saffron palette with bespoke vector SVG iconography and zero external icon library dependencies.
* **Adaptive Dark / Light Theme:** Instant theme switching via CSS custom properties (`data-theme`), persisted in `localStorage` with automatic OS `prefers-color-scheme` fallback.
* **Live Menu Search & Multi-Facet Filtering:** Instant client-side filtering across dish names, spice descriptions, course categories (*Starters & Tandoor*, *Main Course*, *Biryani & Rice*, *Artisan Breads*, *Desserts & Beverages*), and FSSAI-style **Pure Veg / Non-Veg** dietary indicators.
* **Mobile-First & Responsive Hamburger Drawer:** Accessible slide-down mobile navigation with backdrop overlay, `Escape` key support, swipeable category pill ribbons, and fluid CSS Grid layouts.
* **Aggregator & Location Integration:** Direct ordering badges for **Swiggy** and **Zomato**, social media links, and an embedded **Google Maps** location frame.
* **Interactive Contact & Reservation Desk:** Validated guest inquiry form supporting table reservations, bulk biryani catering requests, and customer feedback.

### Secure Administration Portal (`admin/`)
* **Session-Based Authentication:** Bcrypt password hashing (`password_verify()`) and session fixation protection via `session_regenerate_id(true)`.
* **Executive Dashboard & Inquiry Manager (`dashboard.php`):** Real-time dish counters (Veg vs. Non-Veg breakdown), unread inquiry metrics, and full management of customer contact submissions (mark Read/Unread or delete).
* **Full Menu CRUD Interface (`manage_menu.php`):** Create, edit, filter, and delete dishes with automated cleanup of old dish photographs from disk.
* **Hardened Image Upload Pipeline:** Validates file size (max 5 MB) and inspects actual file headers via PHP's `finfo(FILEINFO_MIME_TYPE)` to permit only genuine `JPG`, `PNG`, and `WEBP` images with collision-safe unique filenames.
* **Responsive Table-to-Card Layout:** Admin data tables automatically transform into stacked, touch-friendly cards on mobile viewports.

---

## File Architecture

```text
restaurant_minimal/
│
├── assets/
│   ├── style.css            # All styling, responsive breakpoints, and Light/Dark theme tokens
│   ├── script.js            # Theme persistence, mobile hamburger drawer, and live menu filtering
│   └── images/              # Uploaded dish photographs directory (write-enabled)
│
├── includes/
│   └── db.php               # Centralized PDO database connection with strict error modes
│
├── admin/
│   ├── login.php            # Admin authentication portal
│   ├── dashboard.php        # Metrics overview and customer contact inquiry management
│   ├── manage_menu.php      # Full CRUD interface for dishes, categories, and image uploads
│   └── logout.php           # Complete session and cookie termination
│
├── index.php                # Public homepage (Hero, signature dishes, contact form, map iframe)
├── menu.php                 # Dynamic public menu with live search, category, and Veg/Non-Veg filters
├── schema.sql               # Database creation script with sample Indian menu & inquiry seed data
└── README.md                # Project documentation
```

---

## Database Schema (`restaurant_minimal_db`)

The application relies on three relational InnoDB tables defined in `schema.sql`:

1. **`admins`**
   * `id` (`INT UNSIGNED`, Primary Key, Auto Increment)
   * `username` (`VARCHAR(50)`, Unique)
   * `password` (`VARCHAR(255)`, Bcrypt Hashed)
   * `created_at` (`TIMESTAMP`)
2. **`menu_items`**
   * `id` (`INT UNSIGNED`, Primary Key, Auto Increment)
   * `name` (`VARCHAR(120)`)
   * `category` (`VARCHAR(60)`)
   * `diet_type` (`ENUM('Veg', 'Non-Veg')`)
   * `description` (`TEXT`)
   * `price` (`DECIMAL(10, 2)`)
   * `image_path` (`VARCHAR(255)`)
   * `created_at` (`TIMESTAMP`)
3. **`contact_messages`**
   * `id` (`INT UNSIGNED`, Primary Key, Auto Increment)
   * `full_name` (`VARCHAR(100)`)
   * `email` (`VARCHAR(120)`)
   * `phone` (`VARCHAR(30)`)
   * `subject` (`VARCHAR(120)`)
   * `message` (`TEXT`)
   * `status` (`ENUM('Unread', 'Read')`)
   * `created_at` (`TIMESTAMP`)

---

## Local Setup & Installation (XAMPP)

### Prerequisites
* **XAMPP** (or any stack running **PHP 8.0+** with `pdo_mysql` and `fileinfo` extensions enabled, plus **MySQL 5.7+ / MariaDB 10.4+**).

### Step-by-Step Installation

1. **Clone the Repository into `htdocs`:**
   ```bash
   cd C:/xampp/htdocs
   git clone [https://github.com/your-username/zaffran-clay-restaurant-cms.git](https://github.com/your-username/zaffran-clay-restaurant-cms.git) restaurant_minimal
   ```

2. **Start Services:**
   Open the **XAMPP Control Panel** and start both **Apache** and **MySQL**.

3. **Import the Database Schema:**
   * Navigate to `http://localhost/phpmyadmin`.
   * Click the **Import** tab or open the **SQL** tab.
   * Import or paste the contents of `schema.sql` and execute it. This automatically creates the `restaurant_minimal_db` database, builds all three tables, and seeds sample Indian dishes and inquiries.

4. **Verify Database Credentials:**
   Check `includes/db.php` to ensure the credentials match your local MySQL setup (default XAMPP settings are pre-configured):
   ```php
   $db_host = '127.0.0.1';
   $db_name = 'restaurant_minimal_db';
   $db_user = 'root';
   $db_pass = '';
   ```

5. **Ensure Image Upload Directory Exists:**
   Verify that `assets/images/` exists and has write permissions so PHP can store uploaded dish photographs.

6. **Launch the Application:**
   * **Public Website:** `http://localhost/restaurant_minimal/`
   * **Interactive Menu:** `http://localhost/restaurant_minimal/menu.php`
   * **Admin Portal:** `http://localhost/restaurant_minimal/admin/login.php`

---

## Default Admin Credentials

Use the following pre-seeded credentials to log into `/admin/login.php`:

| Field | Value |
| :--- | :--- |
| **Username** | `admin` |
| **Password** | `password` |

> **Security Note:** For production deployment, update the default administrator password in the `admins` table using `password_hash('your_new_password', PASSWORD_DEFAULT)` and configure HTTPS.

---

## Security & Engineering Best Practices

* **SQL Injection Prevention:** 100% of database queries use **PDO Prepared Statements** with emulated prepares disabled (`PDO::ATTR_EMULATE_PREPARES => false`).
* **Cross-Site Scripting (XSS) Protection:** All dynamic database outputs rendered in HTML attributes and text nodes are sanitized via `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
* **Strict Type Enforcement:** Every PHP script declares `declare(strict_types=1);` at the top of the file.
* **Secure File Handling:** Uploaded images are verified against actual MIME signatures (`image/jpeg`, `image/png`, `image/webp`) using `finfo` rather than trusting client-supplied file extensions.

---

## License

This project is open-source and available under the [MIT License](LICENSE).
