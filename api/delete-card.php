<?php
require '../config.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    
    if (!$id) {
        sendJSON(['success' => false, 'message' => 'ID required']);
    }
    
    // Get card to delete image
    $stmt = $db->prepare('SELECT image_path FROM cards WHERE id = ?');
    $stmt->execute([$id]);
    $card = $stmt->fetch();
    
    if ($card && $card['image_path']) {
        $filepath = '../' . $card['image_path'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
    
    // Delete card
    $stmt = $db->prepare('DELETE FROM cards WHERE id = ?');
    $stmt->execute([$id]);
    
    sendJSON(['success' => true, 'message' => 'Card deleted']);
    
} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => $e->getMessage()]);
}
?>