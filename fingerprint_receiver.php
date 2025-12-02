<?php
// fingerprint_receiver.php
// Placez ce fichier sur votre serveur PHP (ex: XAMPP, WAMP) accessible par l'ESP (mettre IP du PC accessible)

try {
    // Load config
    $config = require_once 'config.php';
    $dbConfig = $config['db'];

    // Read incoming JSON
    $raw = file_get_contents('php://input');
    if (!$raw) {
        throw new Exception('No payload received');
    }
    $data = json_decode($raw, true);
    if ($data === null) {
        throw new Exception('Invalid JSON format');
    }

    // Sanitize and validate
    $fp_id = isset($data['id']) ? intval($data['id']) : -1;
    $confidence = isset($data['confidence']) ? intval($data['confidence']) : 0;
    $known = isset($data['known']) && $data['known'] ? 1 : 0;
    $ts = isset($data['ts']) ? htmlspecialchars(strip_tags($data['ts']), ENT_QUOTES, 'UTF-8') : date('Y-m-d H:i:s');

    // Connect to database
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("INSERT INTO fingerprint_events (fp_id, confidence, known, ts) VALUES (?, ?, ?, ?)");
    $stmt->execute([$fp_id, $confidence, $known, $ts]);
    
    echo json_encode(['status'=>'ok','inserted_id'=>$pdo->lastInsertId()]);
    $pdo = null;
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status'=>'error','msg'=>'Database error']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);
}
?>
