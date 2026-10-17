CREATE DATABASE IF NOT EXISTS restaurant_minimal_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE restaurant_minimal_db;

DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS admins;

-- 1. Admins Table
CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default Admin Account (Username: admin | Password: password)
INSERT INTO admins (username, password) VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
);

-- 2. Menu Items Table (Enhanced with Indian Categories & Veg/Non-Veg Diet Type)
CREATE TABLE menu_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    category VARCHAR(60) NOT NULL DEFAULT 'Main Course',
    diet_type ENUM('Veg', 'Non-Veg') NOT NULL DEFAULT 'Veg',
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seed Authentic Indian Dishes (Prices in INR)
INSERT INTO menu_items (name, category, diet_type, description, price, image_path) VALUES
('Paneer Tikka Angara', 'Starters & Tandoor', 'Veg', 'Charcoal-roasted cottage cheese cubes marinated in hung curd, Kashmiri red chilli, roasted cumin, and smoky mustard oil, served with mint chutney.', 320.00, 'assets/images/sample-paneer-tikka.jpg'),
('Murgh Malai Kebab', 'Starters & Tandoor', 'Non-Veg', 'Succulent boneless chicken morsels steeped in cream cheese, crushed Tellicherry peppercorns, green cardamom, and ginger-garlic, skewered in the clay oven.', 380.00, 'assets/images/sample-malai-kebab.jpg'),
('Old Delhi Murgh Makhani', 'Main Course', 'Non-Veg', 'Tandoor-charred chicken simmered overnight in a velvety heirloom tomato gravy finished with fenugreek leaves, ginger juliennes, and white butter.', 460.00, 'assets/images/sample-butter-chicken.jpg'),
('Dal Bukhara Royal', 'Main Course', 'Veg', 'Whole black urad lentils slow-cooked over glowing charcoal embers for 18 hours with tomato puree, garlic, ginger, and rich churned cream.', 340.00, 'assets/images/sample-dal-makhani.jpg'),
('Awadhi Dum Gosht Biryani', 'Biryani & Rice', 'Non-Veg', 'Long-grain aged basmati rice and tender mutton slow-cooked in a sealed handi with saffron milk, caramelized onions, kewra water, and whole spices.', 520.00, 'assets/images/sample-mutton-biryani.jpg'),
('Subz Zafrani Tarkari Biryani', 'Biryani & Rice', 'Veg', 'Fragrant basmati rice layered with seasonal garden vegetables, paneer, toasted cashews, saffron strands, and fresh mint leaves in dum style.', 360.00, 'assets/images/sample-veg-biryani.jpg'),
('Smoked Garlic Butter Naan', 'Artisan Breads', 'Veg', 'Hand-stretched leavened refined flour bread baked against the scorching walls of the tandoor, brushed with roasted garlic and artisanal makkhan.', 95.00, 'assets/images/sample-garlic-naan.jpg'),
('Kesari Pistachio Rasmalai', 'Desserts & Beverages', 'Veg', 'Soft poached chhena medallions soaked in thickened saffron-cardamom milk reduction, garnished with slivered Iranian pistachios and rose petals.', 180.00, 'assets/images/sample-rasmalai.jpg');

-- 3. Contact Messages Table (Customer Inquiries & Table/Catering Requests)
CREATE TABLE contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    subject VARCHAR(120) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Unread', 'Read') NOT NULL DEFAULT 'Unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sample Contact Inquiries for Admin Dashboard Preview
INSERT INTO contact_messages (full_name, email, phone, subject, message, status) VALUES
('Anirban Chatterjee', 'anirban.c@example.com', '+91 98300 45120', 'Family Dinner Reservation (8 Guests)', 'Hello team, we would like to book a corner table for 8 guests this Sunday at 8:00 PM. Please let us know if live tandoor platters are available.', 'Unread'),
('Priya Sengupta', 'priya.sengupta@example.com', '+91 94341 88219', 'Corporate Bulk Biryani Catering', 'We are organizing a team lunch next Friday at City Centre and need 35 boxes of Awadhi Dum Biryani and Paneer Tikka. Kindly share your bulk quotation.', 'Read');