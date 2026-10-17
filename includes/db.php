<?php
declare(strict_types=1);

$db_host = '127.0.0.1';
$db_name = 'restaurant_minimal_db';
$db_user = 'root';
$db_pass = ''; // Default XAMPP MySQL password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host={$db_host};dbname={$db_name};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    error_log('Database Connection Error: ' . $e->getMessage());
    http_response_code(500);
    exit('Database connection failed. Please ensure XAMPP MySQL is running and schema.sql has been imported.');
}