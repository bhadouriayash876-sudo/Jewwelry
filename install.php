<?php
/**
 * Jewellery Test Report ERP - Auto Installer
 * Run this once to set up the system
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', dirname(__FILE__));
define('UPLOADS_DIR', BASE_PATH . '/uploads');
define('DATABASE_FILE', BASE_PATH . '/data/cards.db');
define('DATA_DIR', BASE_PATH . '/data');

$step = isset($_GET['step']) ? (int)$_GET['step'] : 0;
$message = '';
$status = 'info';

// Helper function
function createDirectory($path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        chmod($path, 0755);
        return true;
    }
    return false;
}

// Step 1: Check Requirements
if ($step === 0) {
    $checks = [];
    $all_pass = true;
    
    // PHP Version
    $checks['php_version'] = [
        'label' => 'PHP Version >= 7.4',
        'pass' => version_compare(PHP_VERSION, '7.4', '>='),
        'value' => PHP_VERSION
    ];
    
    // Write Permissions
    $checks['write_permission'] = [
        'label' => 'Write Permissions',
        'pass' => is_writable(BASE_PATH),
        'value' => is_writable(BASE_PATH) ? 'Yes' : 'No'
    ];
    
    // Check SQLite
    $checks['sqlite'] = [
        'label' => 'SQLite Support',
        'pass' => extension_loaded('sqlite3') || extension_loaded('pdo_sqlite'),
        'value' => extension_loaded('sqlite3') ? 'sqlite3' : (extension_loaded('pdo_sqlite') ? 'pdo_sqlite' : 'Not found')
    ];
    
    // JSON Support
    $checks['json'] = [
        'label' => 'JSON Support',
        'pass' => extension_loaded('json'),
        'value' => extension_loaded('json') ? 'Yes' : 'No'
    ];
    
    foreach ($checks as $check) {
        if (!$check['pass']) $all_pass = false;
    }
}

// Step 1.5: Create Directories
elseif ($step === 1) {
    $dirs = [
        DATA_DIR,
        UPLOADS_DIR,
        BASE_PATH . '/uploads/images',
        BASE_PATH . '/uploads/pdfs'
    ];
    
    $created = [];
    foreach ($dirs as $dir) {
        $created[$dir] = createDirectory($dir);
    }
}

// Step 2: Create Database
elseif ($step === 2) {
    try {
        // Create SQLite database
        $db = new PDO('sqlite:' . DATABASE_FILE);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create table
        $sql = "CREATE TABLE IF NOT EXISTS cards (
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
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $db->exec($sql);
        
        $message = 'Database created successfully!';
        $status = 'success';
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $status = 'error';
    }
}

// Step 3: Create Config File
elseif ($step === 3) {
    $config_content = '<?php
define("APP_NAME", "Jewellery Test Report ERP");
define("APP_VERSION", "1.0.0");
define("DATABASE_PATH", "' . DATABASE_FILE . '");
define("UPLOAD_PATH", "' . UPLOADS_DIR . '");
define("BASE_URL", "http" . (isset($_SERVER["HTTPS"]) ? "s" : "") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/");
define("QR_CODE_URL", BASE_URL . "verify.php?cert_id=");

// Database connection
try {
    $db = new PDO("sqlite:" . DATABASE_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>';
    
    file_put_contents(BASE_PATH . '/config.php', $config_content);
    $message = 'Configuration file created!';
    $status = 'success';
}

// Step 4: Complete
elseif ($step === 4) {
    $message = 'Installation complete!';
    $status = 'success';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jewellery ERP - Installer</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 10px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.3s ease;
        }
        .step-content {
            min-height: 200px;
        }
        .step-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
        .check-item {
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .check-item.pass {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .check-item.fail {
            background: #ffebee;
            color: #c62828;
        }
        .check-icon {
            font-size: 20px;
            font-weight: bold;
        }
        .check-label {
            flex: 1;
        }
        .check-value {
            font-size: 12px;
            opacity: 0.8;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .message.success {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .message.error {
            background: #ffebee;
            color: #c62828;
        }
        .message.info {
            background: #e3f2fd;
            color: #1565c0;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        button {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #e0e0e0;
        }
        .btn-success {
            background: #4caf50;
            color: white;
        }
        .list-item {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            color: #666;
        }
        .list-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">✨ Jewellery Test Report ERP</div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo ($step / 4) * 100; ?>%"></div>
        </div>
        
        <div class="step-content">
            <?php if ($message): ?>
                <div class="message <?php echo $status; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($step === 0): ?>
                <div class="step-title">System Requirements</div>
                <?php foreach ($checks as $key => $check): ?>
                    <div class="check-item <?php echo $check['pass'] ? 'pass' : 'fail'; ?>">
                        <div class="check-icon"><?php echo $check['pass'] ? '✓' : '✗'; ?></div>
                        <div class="check-label">
                            <div><?php echo $check['label']; ?></div>
                            <div class="check-value"><?php echo $check['value']; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
            <?php elseif ($step === 1): ?>
                <div class="step-title">Creating Directories</div>
                <?php foreach ($created as $dir => $result): ?>
                    <div class="list-item">
                        ✓ <?php echo str_replace(BASE_PATH, '', $dir); ?>
                    </div>
                <?php endforeach; ?>
                
            <?php elseif ($step === 2): ?>
                <div class="step-title">Database Setup</div>
                <div class="list-item">✓ SQLite database initialized</div>
                <div class="list-item">✓ Tables created</div>
                <div class="list-item">✓ Ready for data</div>
                
            <?php elseif ($step === 3): ?>
                <div class="step-title">Configuration</div>
                <div class="list-item">✓ Config file created</div>
                <div class="list-item">✓ Database path configured</div>
                <div class="list-item">✓ Upload directories configured</div>
                
            <?php elseif ($step === 4): ?>
                <div class="step-title">Installation Complete! 🎉</div>
                <div class="list-item" style="margin-top: 20px;">
                    Your Jewellery Test Report ERP is ready to use!<br><br>
                    <strong>Next Steps:</strong>
                    <ul style="margin-top: 10px; padding-left: 20px;">
                        <li>Remove or rename install.php for security</li>
                        <li>Open <a href="index.html" style="color: #667eea;">index.html</a> to start using the system</li>
                        <li>Upload your logo to /public/images/logo.png</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="button-group">
            <?php if ($step > 0): ?>
                <button class="btn-secondary" onclick="window.location.href='?step=<?php echo $step - 1; ?>'">← Back</button>
            <?php endif; ?>
            
            <?php if ($step < 4): ?>
                <button class="btn-primary" onclick="window.location.href='?step=<?php echo $step + 1; ?>'">
                    <?php echo $step === 0 && !$all_pass ? 'Fix Issues First' : 'Continue →'; ?>
                </button>
            <?php else: ?>
                <button class="btn-success" onclick="window.location.href='index.html'">Open Application</button>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>