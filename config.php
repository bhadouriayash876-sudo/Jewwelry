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
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]));
}

function sendJSON($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>