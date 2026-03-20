<?php
require_once 'init.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $marketId = $_GET['market'] ?? null;
    if (!$marketId) {
        http_response_code(400);
        echo json_encode(['error' => 'Market ID required']);
        exit;
    }

    $stmtMarket = $pdo->prepare("SELECT name FROM markets WHERE id = ?");
    $stmtMarket->execute([$marketId]);
    $market = $stmtMarket->fetch();

    if (!$market) {
        http_response_code(404);
        echo json_encode(['error' => 'Market not found']);
        exit;
    }

    $stmtResults = $pdo->prepare("
        SELECT result_date, openNumber, jodiNumber, closeNumber 
        FROM results 
        WHERE market_id = ? 
        ORDER BY result_date ASC
    ");
    $stmtResults->execute([$marketId]);
    $results = $stmtResults->fetchAll();

    // Just chunking identically as Jodi chart for 7-day row layouts
    $weeksData = [];
    $currentWeek = [];
    
    foreach ($results as $res) {
        $currentWeek[] = [ 'result' => [ 
            'openNumber' => $res['openNumber'],
            'jodiNumber' => $res['jodiNumber'],
            'closeNumber' => $res['closeNumber']
        ] ];
        
        if (count($currentWeek) === 7) {
            $weeksData[] = $currentWeek;
            $currentWeek = [];
        }
    }
    if (count($currentWeek) > 0) {
        while (count($currentWeek) < 7) {
            $currentWeek[] = null;
        }
        $weeksData[] = $currentWeek;
    }

    echo json_encode([
        'market' => ['_id' => $marketId, 'name' => $market['name']],
        'weeks' => $weeksData
    ]);
}
?>
