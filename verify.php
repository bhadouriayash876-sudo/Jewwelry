<?php
require 'config.php';

$cert_id = isset($_GET['cert_id']) ? trim($_GET['cert_id']) : null;

if (!$cert_id) {
    $cert_data = null;
    $error = 'No certification ID provided';
} else {
    try {
        $stmt = $db->prepare('SELECT * FROM cards WHERE cert_number = ? AND status = ?');
        $stmt->execute([$cert_id, 'published']);
        $cert_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $error = $cert_data ? null : 'Certificate not found or not yet published';
    } catch (Exception $e) {
        $cert_data = null;
        $error = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #667eea;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .status {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .status.verified {
            background: #d4edda;
            color: #155724;
        }
        .status.invalid {
            background: #f8d7da;
            color: #721c24;
        }
        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }
        @media (max-width: 600px) {
            .details {
                grid-template-columns: 1fr;
            }
        }
        .detail-item {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .detail-item label {
            color: #667eea;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            display: block;
            margin-bottom: 5px;
        }
        .detail-item p {
            color: #333;
            font-size: 16px;
        }
        .error {
            padding: 20px;
            background: #ffebee;
            color: #c62828;
            border-radius: 8px;
            text-align: center;
        }
        .image-section {
            text-align: center;
            margin: 30px 0;
        }
        .image-section img {
            max-width: 300px;
            max-height: 300px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .qr-section {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .qr-section img {
            max-width: 200px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ Certificate Verification</h1>
            <?php if ($cert_data): ?>
                <div class="status verified">
                    <i class="fas fa-check-circle"></i> Certificate Verified
                </div>
            <?php else: ?>
                <div class="status invalid">
                    <i class="fas fa-times-circle"></i> Invalid Certificate
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($cert_data): ?>
            <div class="details">
                <div class="detail-item">
                    <label>Certification Number</label>
                    <p><?php echo htmlspecialchars($cert_data['cert_number']); ?></p>
                </div>
                <div class="detail-item">
                    <label>Job Number</label>
                    <p><?php echo htmlspecialchars($cert_data['job_number']); ?></p>
                </div>
                <div class="detail-item">
                    <label>Customer Name</label>
                    <p><?php echo htmlspecialchars($cert_data['customer_name']); ?></p>
                </div>
                <div class="detail-item">
                    <label>Product Type</label>
                    <p><?php echo htmlspecialchars($cert_data['product_type']); ?></p>
                </div>
                <div class="detail-item">
                    <label>Weight / Carat</label>
                    <p><?php echo htmlspecialchars($cert_data['weight']); ?></p>
                </div>
                <div class="detail-item">
                    <label>Colour</label>
                    <p><?php echo htmlspecialchars($cert_data['colour']); ?></p>
                </div>
                <div class="detail-item">
                    <label>Clarity</label>
                    <p><?php echo htmlspecialchars($cert_data['clarity']); ?></p>
                </div>
                <div class="detail-item">
                    <label>Certification Date</label>
                    <p><?php echo date('d M, Y', strtotime($cert_data['created_at'])); ?></p>
                </div>
            </div>
            
            <?php if ($cert_data['description']): ?>
                <div class="detail-item" style="grid-column: 1/-1;">
                    <label>Description</label>
                    <p><?php echo htmlspecialchars($cert_data['description']); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($cert_data['image_path']): ?>
                <div class="image-section">
                    <h3>Product Image</h3>
                    <img src="<?php echo htmlspecialchars($cert_data['image_path']); ?>" alt="Product">
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="error">
                <i class="fas fa-exclamation-circle" style="font-size: 30px; margin-bottom: 10px; display: block;"></i>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>