<?php
require_once 'init.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Return all markets
    $stmt = $pdo->query("SELECT id as _id, name, openTime, closeTime, isActive FROM markets ORDER BY id ASC");
    $markets = $stmt->fetchAll();
    
    // cast isActive to boolean
    foreach ($markets as &$market) {
        $market['isActive'] = (bool)$market['isActive'];
    }
    
    echo json_encode($markets);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
