<?php
// Database configuration
define('APP_NAME', 'Jewellery Test Report ERP');
define('APP_VERSION', '1.0.0');
define('DATABASE_PATH', __DIR__ . '/data/cards.db');
define('UPLOAD_PATH', __DIR__ . '/uploads');
define('BASE_URL', 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/');
define('QR_BASE_URL', BASE_URL . 'verify.php?cert_id=');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB

// Create uploads directory if not exists
if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
    mkdir(UPLOAD_PATH . '/images', 0755, true);
    mkdir(UPLOAD_PATH . '/pdfs', 0755, true);
}

// Database connection
try {
    $db = new PDO('sqlite:' . DATABASE_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if not exist
    $db->exec("CREATE TABLE IF NOT EXISTS cards (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        job_number TEXT UNIQUE NOT NULL,
        cert_number TEXT UNIQUE NOT NULL,
        customer_name TEXT NOT NULL,
        description TEXT,
        product_type TEXT,
        weight TEXT,
        colour TEXT,
        clarity TEXT,
        comments TEXT,
        image_path TEXT,
        status TEXT DEFAULT 'draft',
        qr_code TEXT,
        created_by TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_by TEXT,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        published_by TEXT,
        published_at DATETIME
    )");
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]));
}

function sendJSON($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}
?>