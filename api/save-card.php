<?php
require '../auth.php';
require '../config.php';

requireAdminOrOwner();

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
    
    if (!$job_number || !$cert_number) {
        sendJSON(['success' => false, 'message' => 'Job and certification numbers are required']);
    }
    
    // Sanitize inputs
    $customer_name = htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $product_type = htmlspecialchars($product_type, ENT_QUOTES, 'UTF-8');
    $weight = htmlspecialchars($weight, ENT_QUOTES, 'UTF-8');
    $colour = htmlspecialchars($colour, ENT_QUOTES, 'UTF-8');
    $clarity = htmlspecialchars($clarity, ENT_QUOTES, 'UTF-8');
    $comments = htmlspecialchars($comments, ENT_QUOTES, 'UTF-8');
    
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        if ($_FILES['image']['size'] > MAX_UPLOAD_SIZE) {
            sendJSON(['success' => false, 'message' => 'File too large (max 10MB)']);
        }
        
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            sendJSON(['success' => false, 'message' => 'Invalid image format']);
        }
        
        // Validate image
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            sendJSON(['success' => false, 'message' => 'Invalid image file']);
        }
        
        $filename = uniqid('img_') . '.' . $ext;
        $filepath = UPLOAD_PATH . '/images/' . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $image_path = 'uploads/images/' . $filename;
        } else {
            sendJSON(['success' => false, 'message' => 'Failed to upload image']);
        }
    }
    
    // Check if card exists
    $stmt = $db->prepare('SELECT id FROM cards WHERE cert_number = ?');
    $stmt->execute([$cert_number]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update
        $stmt = $db->prepare('UPDATE cards SET customer_name = ?, description = ?, product_type = ?, weight = ?, colour = ?, clarity = ?, comments = ?, status = ?, updated_at = CURRENT_TIMESTAMP, updated_by = ? WHERE cert_number = ?');
        $stmt->execute([$customer_name, $description, $product_type, $weight, $colour, $clarity, $comments, $status, getUsername(), $cert_number]);
        $id = $existing['id'];
        
        if ($image_path) {
            $updateImgStmt = $db->prepare('UPDATE cards SET image_path = ? WHERE id = ?');
            $updateImgStmt->execute([$image_path, $id]);
        }
    } else {
        // Insert
        $stmt = $db->prepare('INSERT INTO cards (job_number, cert_number, customer_name, description, product_type, weight, colour, clarity, comments, image_path, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$job_number, $cert_number, $customer_name, $description, $product_type, $weight, $colour, $clarity, $comments, $image_path, $status, getUsername()]);
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
