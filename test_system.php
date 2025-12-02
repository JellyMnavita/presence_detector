<?php
// test_system.php - Script de test pour vérifier le système

echo "=== Test du système de monitoring ===\n\n";

// Test 1: Vérifier la configuration
echo "1. Test de configuration...\n";
try {
    $config = require_once 'config.php';
    echo "✅ Configuration chargée avec succès\n";
    echo "   - Host: " . $config['db']['host'] . ":" . $config['db']['port'] . "\n";
    echo "   - Database: " . $config['db']['name'] . "\n\n";
} catch (Exception $e) {
    echo "❌ Erreur de configuration: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Connexion à la base de données
echo "2. Test de connexion à la base de données...\n";
try {
    $dbConfig = $config['db'];
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ Connexion à la base de données réussie\n\n";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    echo "   Vérifiez que MySQL est démarré et que la base 'monitoring' existe\n\n";
    exit(1);
}

// Test 3: Vérifier la table
echo "3. Test de la table fingerprint_events...\n";
try {
    $stmt = $pdo->query("DESCRIBE fingerprint_events");
    $columns = $stmt->fetchAll();
    if ($columns) {
        echo "✅ Table fingerprint_events trouvée\n";
        echo "   Colonnes: ";
        foreach ($columns as $col) {
            echo $col['Field'] . " ";
        }
        echo "\n\n";
    } else {
        throw new Exception('Table non trouvée');
    }
} catch (PDOException $e) {
    echo "❌ Erreur table: " . $e->getMessage() . "\n";
    echo "   Exécutez le script database.sql\n\n";
    exit(1);
}

// Test 4: Insérer des données de test
echo "4. Test d'insertion de données...\n";
try {
    $stmt = $pdo->prepare("INSERT INTO fingerprint_events (fp_id, confidence, known, ts) VALUES (?, ?, ?, ?)");
    
    $fp_id = 999;
    $confidence = 85;
    $known = 1;
    $ts = date('Y-m-d H:i:s');
    
    $stmt->execute([$fp_id, $confidence, $known, $ts]);
    
    echo "✅ Insertion de test réussie (ID: " . $pdo->lastInsertId() . ")\n\n";
} catch (PDOException $e) {
    echo "❌ Erreur insertion: " . $e->getMessage() . "\n\n";
}

// Test 5: Test de l'API
echo "5. Test de l'API...\n";
try {
    // Simuler une requête GET
    $_GET['action'] = 'stats';
    
    ob_start();
    include 'api.php';
    $output = ob_get_clean();
    
    $data = json_decode($output, true);
    if ($data && $data['status'] === 'ok') {
        echo "✅ API fonctionne correctement\n";
        echo "   Statistiques: " . json_encode($data['data']) . "\n\n";
    } else {
        throw new Exception('Réponse API invalide');
    }
} catch (Exception $e) {
    echo "❌ Erreur API: " . $e->getMessage() . "\n\n";
}

$pdo = null;

echo "=== Test terminé ===\n";
echo "Si tous les tests sont ✅, vous pouvez:\n";
echo "1. Double-cliquer sur start_server.bat (Windows)\n";
echo "2. Ou exécuter: php -S localhost:8000\n";
echo "3. Puis ouvrir: http://localhost:8000/monitoring.html\n";
?>