<?php
require '../auth.php';
require '../config.php';

requireAdminOrOwner();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    
    if (!$id) {
        sendJSON(['success' => false, 'message' => 'ID required']);
    }
    
    $stmt = $db->prepare('SELECT cert_number FROM cards WHERE id = ?');
    $stmt->execute([$id]);
    $card = $stmt->fetch();
    
    if (!$card) {
        sendJSON(['success' => false, 'message' => 'Card not found']);
    }
    
    // Generate QR code
    $qr_text = BASE_URL . 'verify.php?cert_id=' . urlencode($card['cert_number']);
    
    $stmt = $db->prepare('UPDATE cards SET status = ?, qr_code = ?, published_at = CURRENT_TIMESTAMP, published_by = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
    $stmt->execute(['published', $qr_text, getUsername(), $id]);
    
    // Fetch updated card
    $stmt = $db->prepare('SELECT * FROM cards WHERE id = ?');
    $stmt->execute([$id]);
    $updated = $stmt->fetch(PDO::FETCH_ASSOC);
    
    sendJSON(['success' => true, 'card' => $updated]);
    
} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => $e->getMessage()]);
}
?>
