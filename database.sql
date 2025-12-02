-- Script de création de la base de données pour le système de monitoring
-- Exécutez ce script dans votre serveur MySQL/MariaDB

CREATE DATABASE IF NOT EXISTS monitoring;
USE monitoring;

CREATE TABLE IF NOT EXISTS fingerprint_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fp_id INT NOT NULL,
    confidence INT NOT NULL,
    known TINYINT(1) NOT NULL DEFAULT 0,
    ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour améliorer les performances
CREATE INDEX idx_ts ON fingerprint_events(ts);
CREATE INDEX idx_known ON fingerprint_events(known);
CREATE INDEX idx_fp_id ON fingerprint_events(fp_id);

-- Données de test (optionnel)
INSERT INTO fingerprint_events (fp_id, confidence, known, ts) VALUES
-- Événements récents
(1, 95, 1, NOW() - INTERVAL 30 MINUTE),
(3, 88, 1, NOW() - INTERVAL 45 MINUTE),
(-1, 42, 0, NOW() - INTERVAL 1 HOUR),
(2, 91, 1, NOW() - INTERVAL 90 MINUTE),
(-1, 38, 0, NOW() - INTERVAL 2 HOUR),
(1, 97, 1, NOW() - INTERVAL 3 HOUR),
(4, 85, 1, NOW() - INTERVAL 4 HOUR),
(-1, 55, 0, NOW() - INTERVAL 5 HOUR),
(2, 89, 1, NOW() - INTERVAL 6 HOUR),
(3, 93, 1, NOW() - INTERVAL 7 HOUR),
-- Événements d'hier
(1, 94, 1, NOW() - INTERVAL 1 DAY + INTERVAL 2 HOUR),
(2, 86, 1, NOW() - INTERVAL 1 DAY + INTERVAL 4 HOUR),
(-1, 41, 0, NOW() - INTERVAL 1 DAY + INTERVAL 6 HOUR),
(3, 90, 1, NOW() - INTERVAL 1 DAY + INTERVAL 8 HOUR),
(4, 87, 1, NOW() - INTERVAL 1 DAY + INTERVAL 10 HOUR),
-- Événements variés dans la journée
(1, 96, 1, CURDATE() + INTERVAL 8 HOUR),
(2, 84, 1, CURDATE() + INTERVAL 9 HOUR),
(-1, 47, 0, CURDATE() + INTERVAL 10 HOUR),
(3, 92, 1, CURDATE() + INTERVAL 11 HOUR),
(1, 89, 1, CURDATE() + INTERVAL 14 HOUR),
(-1, 39, 0, CURDATE() + INTERVAL 15 HOUR),
(4, 91, 1, CURDATE() + INTERVAL 16 HOUR),
(2, 88, 1, CURDATE() + INTERVAL 17 HOUR);