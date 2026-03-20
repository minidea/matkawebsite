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

    // Get the last N days (e.g., 30 days) and chunk them by weeks.
    // In real app, you would fetch all and organize by week chunks.
    $stmtResults = $pdo->prepare("
        SELECT result_date, jodiNumber 
        FROM results 
        WHERE market_id = ? 
        ORDER BY result_date ASC
    ");
    $stmtResults->execute([$marketId]);
    $results = $stmtResults->fetchAll();

    // Grouping by weeks (naive approach for demo: group every 7 records)
    // Normally you group by week of year. Here we chunk the flat list for standard layout.
    $weeksData = [];
    $currentWeek = [];
    
    foreach ($results as $res) {
        $currentWeek[] = [ 'result' => [ 'jodiNumber' => $res['jodiNumber'] ] ];
        if (count($currentWeek) === 7) {
            $weeksData[] = $currentWeek;
            $currentWeek = [];
        }
    }
    if (count($currentWeek) > 0) {
        // Pad the remainder to make 7 cells
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
