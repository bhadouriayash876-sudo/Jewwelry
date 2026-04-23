<?php
require '../auth.php';
require '../config.php';

requireAdminOrOwner();

try {
    $stmt = $db->prepare('SELECT * FROM cards ORDER BY created_at DESC');
    $stmt->execute();
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $filename = 'jewellery_cards_' . date('Y-m-d-H-i-s') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Header
    fputcsv($output, ['Job Number', 'Certification Number', 'Customer Name', 'Product Type', 'Weight', 'Colour', 'Clarity', 'Description', 'Comments', 'Status', 'Created Date', 'Created By']);
    
    // Data
    foreach ($cards as $card) {
        fputcsv($output, [
            $card['job_number'],
            $card['cert_number'],
            $card['customer_name'],
            $card['product_type'],
            $card['weight'],
            $card['colour'],
            $card['clarity'],
            $card['description'],
            $card['comments'],
            $card['status'],
            $card['created_at'],
            $card['created_by']
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>
