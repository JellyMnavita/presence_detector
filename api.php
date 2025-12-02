<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $config = require_once 'config.php';
    $dbConfig = $config['db'];

    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'recent':
            $limit = intval($_GET['limit'] ?? 50);
            // Utiliser une requête directe au lieu de prepare pour LIMIT
            $stmt = $pdo->query("SELECT * FROM fingerprint_events ORDER BY id_event DESC LIMIT $limit");
            $events = $stmt->fetchAll();
            echo json_encode(['status' => 'ok', 'data' => $events]);
            break;

        case 'stats':
            $stmt = $pdo->query("SELECT 
                COUNT(*) as total,
                SUM(known) as known_count,
                COUNT(*) - SUM(known) as unknown_count,
                AVG(confidence) as avg_confidence
                FROM fingerprint_events 
                WHERE DATE(ts) = CURDATE()");
            $stats = $stmt->fetch();
            echo json_encode(['status' => 'ok', 'data' => $stats]);
            break;

        case 'hourly':
            $stmt = $pdo->query("SELECT 
                HOUR(ts) as hour,
                COUNT(*) as count,
                SUM(known) as known
                FROM fingerprint_events 
                WHERE DATE(ts) = CURDATE()
                GROUP BY HOUR(ts)
                ORDER BY hour");
            $hourly = $stmt->fetchAll();
            echo json_encode(['status' => 'ok', 'data' => $hourly]);
            break;

        default:
            throw new Exception('Invalid action');
    }

    $pdo = null;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
