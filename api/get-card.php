<?php
require '../config.php';

try {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        sendJSON(['success' => false, 'message' => 'ID required']);
    }
    
    $stmt = $db->prepare('SELECT * FROM cards WHERE id = ?');
    $stmt->execute([$id]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$card) {
        sendJSON(['success' => false, 'message' => 'Card not found']);
    }
    
    sendJSON(['success' => true, 'card' => $card]);
    
} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => $e->getMessage()]);
}
?>