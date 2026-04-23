<?php
require '../config.php';

try {
    $status = $_GET['status'] ?? null;
    
    if ($status) {
        $stmt = $db->prepare('SELECT * FROM cards WHERE status = ? ORDER BY created_at DESC');
        $stmt->execute([$status]);
    } else {
        $stmt = $db->prepare('SELECT * FROM cards ORDER BY created_at DESC');
        $stmt->execute();
    }
    
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    sendJSON(['success' => true, 'cards' => $cards]);
    
} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => $e->getMessage()]);
}
?>