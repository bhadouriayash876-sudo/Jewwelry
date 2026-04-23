<?php
require '../config.php';

try {
    $stmt = $db->prepare('SELECT * FROM cards ORDER BY created_at DESC');
    $stmt->execute();
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $filename = 'jewellery_cards_' . date('Y-m-d-H-i-s') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Header
    fputcsv($output, ['Job Number', 'Certification Number', 'Customer Name', 'Product Type', 'Weight', 'Colour', 'Clarity', 'Description', 'Comments', 'Status', 'Created Date']);
    
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
            $card['created_at']
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>