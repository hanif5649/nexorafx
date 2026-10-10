<?php
/**
 * Global Configuration - 2026 Investment Platform
 * Secure PDO Connection and System Constants
 */

// 1. Error Reporting (Disable in Production)
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 in production

// 2. Database Credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'nexora');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// 3. PDO Connection Logic
try {
    // UPDATED: Changed from pgsql to mysql to match your InnoDB schema
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT         => true, 
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (\PDOException $e) {

    exit($e->getMessage());

}

// 4. Global Site Settings Fetcher
// We use a try-catch here as well in case the site_settings table isn't created yet
try {
    $stmt = $pdo->query("SELECT * FROM site_settings LIMIT 1");
    $settings = $stmt->fetch();
} catch (Exception $e) {
    $settings = [];
}

define('SITE_NAME', $settings['site_name'] ?? 'Fortun14 Capital');
define('SITE_EMAIL', $settings['email'] ?? 'admin@fortun14.com');
define('SITE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]");

// 5. Timezone Setup
date_default_timezone_set('UTC');

?>