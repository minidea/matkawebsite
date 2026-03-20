<?php
require_once 'init.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Assuming /api/results.php?today=true for live results
    $action = $_GET['action'] ?? 'today';
    
    if ($action === 'today') {
        $today = date('Y-m-d');
        
        // Fetch all active markets and their current result
        $stmt = $pdo->prepare("
            SELECT m.id as m_id, m.name, m.openTime, m.closeTime, m.isActive,
                   r.openNumber, r.jodiNumber, r.closeNumber
            FROM markets m
            LEFT JOIN results r ON m.id = r.market_id AND r.result_date = ?
            WHERE m.isActive = 1
            ORDER BY m.id ASC
        ");
        $stmt->execute([$today]);
        $rows = $stmt->fetchAll();
        
        $output = [];
        foreach ($rows as $row) {
            $output[] = [
                'market' => [
                    '_id' => $row['m_id'],
                    'name' => $row['name'],
                    'openTime' => $row['openTime'],
                    'closeTime' => $row['closeTime']
                ],
                'openNumber' => $row['openNumber'],
                'jodiNumber' => $row['jodiNumber'],
                'closeNumber' => $row['closeNumber']
            ];
        }
        echo json_encode($output);
    } else {
        echo json_encode([]);
    }
} 
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin adding / updating a result
    checkAuth($pdo);
    
    $input = getJsonInput();
    $marketId = $input['market'] ?? null;
    $date = $input['date'] ?? date('Y-m-d');
    $openNumber = $input['openNumber'] ?? null;
    $jodiNumber = $input['jodiNumber'] ?? null;
    $closeNumber = $input['closeNumber'] ?? null;

    if (!$marketId) {
        http_response_code(400);
        echo json_encode(['error' => 'Market ID required']);
        exit;
    }

    try {
        // Upsert logic (Insert or Update if unique_market_date exists)
        $stmt = $pdo->prepare("
            INSERT INTO results (market_id, result_date, openNumber, jodiNumber, closeNumber)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            openNumber = VALUES(openNumber),
            jodiNumber = VALUES(jodiNumber),
            closeNumber = VALUES(closeNumber)
        ");
        $stmt->execute([$marketId, $date, $openNumber, $jodiNumber, $closeNumber]);
        
        echo json_encode(['message' => 'Result updated successfully', 'success' => true]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
