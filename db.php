<?php
// db.php - Database Connection
$host = 'localhost';
$dbname = 'satta_matka_pro'; // Change to your database name in cPanel
$username = 'root'; // Change to your cPanel DB user
$password = ''; // Change to your cPanel DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed. Please check db.php settings.']);
    exit;
}
?>
