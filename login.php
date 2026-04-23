<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Demo credentials (change in production)
    $admin_user = 'admin';
    $admin_pass = 'admin123'; // CHANGE THIS!
    
    $owner_user = 'owner';
    $owner_pass = 'owner123'; // CHANGE THIS!
    
    $valid = false;
    $role = '';
    
    if ($username === $admin_user && $password === $admin_pass) {
        $valid = true;
        $role = 'admin';
    } elseif ($username === $owner_user && $password === $owner_pass) {
        $valid = true;
        $role = 'owner';
    }
    
    if ($valid) {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        header('Location: index.html');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jewellery Test Report ERP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 100%;
            padding: 50px;
            text-align: center;
        }
        
        .logo {
            font-size: 40px;
            margin-bottom: 20px;
        }
        
        .login-container h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 24px;
        }
        
        .login-container p {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            display: none;
        }
        
        .error.show {
            display: block;
        }
        
        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .demo-credentials {
            background: #e3f2fd;
            border: 1px solid #90caf9;
            color: #1565c0;
            padding: 15px;
            border-radius: 8px;
            margin-top: 30px;
            font-size: 12px;
            text-align: left;
        }
        
        .demo-credentials h3 {
            margin-bottom: 10px;
            font-size: 13px;
        }
        
        .demo-credentials p {
            margin: 5px 0;
            font-family: monospace;
        }
        
        .demo-credentials strong {
            color: #0d47a1;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">✨</div>
        <h1>Jewellery ERP</h1>
        <p>Professional Certification Card Management</p>
        
        <div class="error" id="errorMsg"></div>
        
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter username" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            
            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="demo-credentials">
            <h3><i class="fas fa-info-circle"></i> Demo Credentials</h3>
            <p><strong>Admin:</strong> admin / admin123</p>
            <p><strong>Owner:</strong> owner / owner123</p>
            <p style="margin-top: 10px; font-size: 11px; color: #0d47a1;">⚠️ Change passwords in production!</p>
        </div>
    </div>
    
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('logout') === '1') {
            document.getElementById('errorMsg').textContent = 'Logged out successfully';
            document.getElementById('errorMsg').classList.add('show');
            document.getElementById('errorMsg').style.background = '#d4edda';
            document.getElementById('errorMsg').style.color = '#155724';
            document.getElementById('errorMsg').style.borderColor = '#c3e6cb';
        }
        
        <?php if ($error): ?>
        document.getElementById('errorMsg').textContent = '<?php echo $error; ?>';
        document.getElementById('errorMsg').classList.add('show');
        <?php endif; ?>
    </script>
</body>
</html>
