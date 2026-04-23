<?php
require '../config.php';

try {
    $job_number = $_POST['job_number'] ?? null;
    $cert_number = $_POST['cert_number'] ?? null;
    $customer_name = $_POST['customer_name'] ?? null;
    $description = $_POST['description'] ?? null;
    $product_type = $_POST['product_type'] ?? null;
    $weight = $_POST['weight'] ?? null;
    $colour = $_POST['colour'] ?? null;
    $clarity = $_POST['clarity'] ?? null;
    $comments = $_POST['comments'] ?? null;
    $status = $_POST['status'] ?? 'draft';
    
    if (!$customer_name) {
        sendJSON(['success' => false, 'message' => 'Customer name is required']);
    }
    
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        if ($_FILES['image']['size'] > MAX_UPLOAD_SIZE) {
            sendJSON(['success' => false, 'message' => 'File too large']);
        }
        
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            sendJSON(['success' => false, 'message' => 'Invalid image format']);
        }
        
        $filename = uniqid('img_') . '.' . $ext;
        $filepath = UPLOAD_PATH . '/images/' . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $image_path = 'uploads/images/' . $filename;
        }
    }
    
    // Check if card exists
    $stmt = $db->prepare('SELECT id FROM cards WHERE cert_number = ?');
    $stmt->execute([$cert_number]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update
        $stmt = $db->prepare('UPDATE cards SET customer_name = ?, description = ?, product_type = ?, weight = ?, colour = ?, clarity = ?, comments = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE cert_number = ?');
        $stmt->execute([$customer_name, $description, $product_type, $weight, $colour, $clarity, $comments, $status, $cert_number]);
        $id = $existing['id'];
    } else {
        // Insert
        $stmt = $db->prepare('INSERT INTO cards (job_number, cert_number, customer_name, description, product_type, weight, colour, clarity, comments, image_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$job_number, $cert_number, $customer_name, $description, $product_type, $weight, $colour, $clarity, $comments, $image_path, $status]);
        $id = $db->lastInsertId();
    }
    
    // Fetch the saved card
    $stmt = $db->prepare('SELECT * FROM cards WHERE id = ?');
    $stmt->execute([$id]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    sendJSON(['success' => true, 'card' => $card]);
    
} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => $e->getMessage()]);
}
?>