<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$uploadDir = __DIR__ . '/../assets/images/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$allowedCategories = [
    'Starters & Tandoor',
    'Main Course',
    'Biryani & Rice',
    'Artisan Breads',
    'Desserts & Beverages',
];

$error = '';
$success = '';

function handleImageUpload(array $file, string $uploadDir): string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed with error code ' . $file['error'] . '.');
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Image size must not exceed 5 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    if (!array_key_exists($mimeType, $allowedMimeTypes)) {
        throw new RuntimeException('Invalid image format. Only JPG, PNG, and WEBP files are permitted.');
    }

    $extension = $allowedMimeTypes[$mimeType];
    $safeFilename = uniqid('indian_dish_', true) . '.' . $extension;
    $destination = $uploadDir . $safeFilename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Failed to save uploaded image to assets/images/.');
    }

    return 'assets/images/' . $safeFilename;
}

// Handle Delete Request
$deleteId = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $deleteId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
} elseif (isset($_GET['delete'])) {
    $deleteId = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
}

if ($deleteId) {
    $stmt = $pdo->prepare('SELECT image_path FROM menu_items WHERE id = :id');
    $stmt->execute([':id' => $deleteId]);
    $itemToDelete = $stmt->fetch();

    if ($itemToDelete) {
        $delStmt = $pdo->prepare('DELETE FROM menu_items WHERE id = :id');
        $delStmt->execute([':id' => $deleteId]);

        $fullImagePath = __DIR__ . '/../' . $itemToDelete['image_path'];
        if (!empty($itemToDelete['image_path']) && file_exists($fullImagePath) && is_file($fullImagePath)) {
            @unlink($fullImagePath);
        }
        $success = 'Dish removed from the menu.';
    }
}

// Handle Create & Update (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] === 'save')) {
    $itemId      = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $name        = trim((string)($_POST['name'] ?? ''));
    $category    = trim((string)($_POST['category'] ?? 'Main Course'));
    $dietType    = ($_POST['diet_type'] ?? 'Veg') === 'Non-Veg' ? 'Non-Veg' : 'Veg';
    $description = trim((string)($_POST['description'] ?? ''));
    $priceRaw    = trim((string)($_POST['price'] ?? ''));

    if (!in_array($category, $allowedCategories, true)) {
        $category = 'Main Course';
    }

    if ($name === '' || $description === '' || $priceRaw === '') {
        $error = 'All fields (Dish Name, Category, Description, Price) are required.';
    } elseif (!is_numeric($priceRaw) || (float)$priceRaw < 0) {
        $error = 'Please enter a valid positive price in INR.';
    } else {
        $price = round((float)$priceRaw, 2);
        $hasNewImage = isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;

        try {
            if ($itemId) {
                $stmt = $pdo->prepare('SELECT image_path FROM menu_items WHERE id = :id');
                $stmt->execute([':id' => $itemId]);
                $existing = $stmt->fetch();

                if (!$existing) {
                    throw new RuntimeException('Dish record not found.');
                }

                $imagePath = $existing['image_path'];
                if ($hasNewImage) {
                    $imagePath = handleImageUpload($_FILES['image'], $uploadDir);
                    $oldFile = __DIR__ . '/../' . $existing['image_path'];
                    if (!empty($existing['image_path']) && file_exists($oldFile) && is_file($oldFile)) {
                        @unlink($oldFile);
                    }
                }

                $updateStmt = $pdo->prepare(
                    'UPDATE menu_items
                     SET name = :name, category = :category, diet_type = :diet_type, description = :description, price = :price, image_path = :image_path
                     WHERE id = :id'
                );
                $updateStmt->execute([
                    ':name'        => $name,
                    ':category'    => $category,
                    ':diet_type'   => $dietType,
                    ':description' => $description,
                    ':price'       => $price,
                    ':image_path'  => $imagePath,
                    ':id'          => $itemId,
                ]);
                $success = 'Dish updated successfully.';
            } else {
                if (!$hasNewImage) {
                    throw new RuntimeException('Please upload a dish photograph (JPG, PNG, or WEBP).');
                }
                $imagePath = handleImageUpload($_FILES['image'], $uploadDir);

                $insertStmt = $pdo->prepare(
                    'INSERT INTO menu_items (name, category, diet_type, description, price, image_path)
                     VALUES (:name, :category, :diet_type, :description, :price, :image_path)'
                );
                $insertStmt->execute([
                    ':name'        => $name,
                    ':category'    => $category,
                    ':diet_type'   => $dietType,
                    ':description' => $description,
                    ':price'       => $price,
                    ':image_path'  => $imagePath,
                ]);
                $success = 'New Indian dish published to the menu.';
            }
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        }
    }
}

// Fetch item for editing
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($editId) {
        $stmt = $pdo->prepare('SELECT id, name, category, diet_type, description, price, image_path FROM menu_items WHERE id = :id');
        $stmt->execute([':id' => $editId]);
        $editItem = $stmt->fetch() ?: null;
    }
}

$allItemsStmt = $pdo->query('SELECT id, name, category, diet_type, description, price, image_path FROM menu_items ORDER BY id DESC');
$menuItems = $allItemsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Indian Menu | Zaffran Admin</title>
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

            <?php if ($success !== ''): ?>
                <div class="alert alert-success" role="status">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>

            <div class="admin-grid">
                <!-- Left Column: Add / Edit Dish Form -->
                <aside class="panel-card">
                    <h2><?php echo $editItem ? 'Edit Indian Dish' : 'Add New Indian Dish'; ?></h2>
                    <form method="POST" action="manage_menu.php" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="save">
                        <?php if ($editItem): ?>
                            <input type="hidden" name="id" value="<?php echo (int)$editItem['id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="name" class="form-label">Dish Name</label>
                            <input type="text" id="name" name="name" class="form-control" required maxlength="120"
                                   placeholder="e.g., Murgh Tikka Masala"
                                   value="<?php echo htmlspecialchars($editItem['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="category" class="form-label">Course Category</label>
                                <select id="category" name="category" class="form-control" required>
                                    <?php
                                        $selectedCat = $editItem['category'] ?? 'Main Course';
                                        foreach ($allowedCategories as $catOption):
                                    ?>
                                        <option value="<?php echo htmlspecialchars($catOption, ENT_QUOTES, 'UTF-8'); ?>"
                                            <?php echo $selectedCat === $catOption ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($catOption, ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="diet_type" class="form-label">Dietary Classification</label>
                                <?php $selectedDiet = $editItem['diet_type'] ?? 'Veg'; ?>
                                <select id="diet_type" name="diet_type" class="form-control" required>
                                    <option value="Veg" <?php echo $selectedDiet === 'Veg' ? 'selected' : ''; ?>>Pure Veg</option>
                                    <option value="Non-Veg" <?php echo $selectedDiet === 'Non-Veg' ? 'selected' : ''; ?>>Non-Veg</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="price" class="form-label">Price (INR &#8377;)</label>
                            <input type="number" id="price" name="price" class="form-control" required step="0.01" min="0"
                                   placeholder="e.g., 380.00"
                                   value="<?php echo htmlspecialchars((string)($editItem['price'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Culinary Description &amp; Spices</label>
                            <textarea id="description" name="description" class="form-control" required placeholder="Describe the marinade, gravy base, and accompaniments..."><?php echo htmlspecialchars($editItem['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="image" class="form-label">
                                Dish Photograph (JPG, PNG, WEBP)
                                <?php if ($editItem): ?>
                                    <span style="display: block; font-weight: 400; color: var(--text-muted); font-size: 0.78rem;">Leave blank to keep existing photograph</span>
                                <?php endif; ?>
                            </label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp" <?php echo $editItem ? '' : 'required'; ?>>
                        </div>

                        <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">
                                <?php echo $editItem ? 'Update Dish' : 'Publish Dish'; ?>
                            </button>
                            <?php if ($editItem): ?>
                                <a href="manage_menu.php" class="btn btn-outline">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </aside>

                <!-- Right Column: Filterable Menu Directory -->
                <section class="panel-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                        <h2 style="margin: 0; padding: 0; border: 0;">Menu Inventory (<span id="menuVisibleCount"><?php echo count($menuItems); ?></span>)</h2>
                        <div class="search-box" style="max-width: 280px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="search" id="menuSearchInput" class="search-input" placeholder="Quick filter dishes...">
                        </div>
                    </div>

                    <?php if (empty($menuItems)): ?>
                        <p style="color: var(--text-secondary);">No dishes found. Add your first Indian delicacy using the form.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Dish, Category &amp; Diet</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($menuItems as $item): ?>
                                        <?php
                                            $thumbPath = '../' . $item['image_path'];
                                            $thumbExists = !empty($item['image_path']) && file_exists(__DIR__ . '/../' . $item['image_path']);
                                            $isVeg = ($item['diet_type'] === 'Veg');
                                        ?>
                                        <tr data-menu-item
                                            data-name="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-desc="<?php echo htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-category="<?php echo htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-diet="<?php echo htmlspecialchars($item['diet_type'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <td>
                                                <?php if ($thumbExists): ?>
                                                    <img src="<?php echo htmlspecialchars($thumbPath, ENT_QUOTES, 'UTF-8'); ?>" alt="" class="table-thumb">
                                                <?php else: ?>
                                                    <div class="table-thumb" style="display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 0.7rem;">
                                                        Handi
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
                                                    <strong style="color: var(--text-primary);">
                                                        <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </strong>
                                                    <span class="status-badge" style="background-color: var(--bg-elevated); color: var(--text-secondary);">
                                                        <?php echo htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                    <span style="font-size: 0.75rem; font-weight: 700; color: <?php echo $isVeg ? 'var(--veg-color)' : 'var(--nonveg-color)'; ?>;">
                                                        [<?php echo htmlspecialchars($item['diet_type'], ENT_QUOTES, 'UTF-8'); ?>]
                                                    </span>
                                                </div>
                                                <span style="color: var(--text-secondary); font-size: 0.83rem;">
                                                    <?php echo htmlspecialchars(mb_strimwidth($item['description'], 0, 90, '...'), ENT_QUOTES, 'UTF-8'); ?>
                                                </span>
                                            </td>
                                            <td style="font-weight: 700; color: var(--accent-primary); white-space: nowrap;">
                                                &#8377;<?php echo number_format((float)$item['price'], 2); ?>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="manage_menu.php?edit=<?php echo (int)$item['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
                                                    <form method="POST" action="manage_menu.php" data-confirm-delete data-item-name="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" style="display: inline;">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>">
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
        </div>
    </main>

    <script src="../assets/script.js"></script>
</body>
</html>