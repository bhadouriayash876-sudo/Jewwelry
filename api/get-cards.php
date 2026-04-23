<?php
require '../auth.php';
require '../config.php';

requireAdminOrOwner();

try {
    $status = $_GET['status'] ?? null;
    $search = $_GET['search'] ?? null;
    
    $query = 'SELECT * FROM cards WHERE 1=1';
    $params = [];
    
    if ($status) {
        $query .= ' AND status = ?';
        $params[] = $status;
    }
    
    if ($search) {
        $query .= ' AND (cert_number LIKE ? OR customer_name LIKE ? OR job_number LIKE ?)';
        $search_term = '%' . $search . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    $query .= ' ORDER BY created_at DESC';
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendJSON(['success' => true, 'cards' => $cards]);
    
} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => $e->getMessage()]);
}
?>
