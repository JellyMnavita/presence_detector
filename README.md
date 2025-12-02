# Système de Monitoring - Détecteur de Présence

## 📋 Description
Système complet de monitoring pour détecteur de présence avec empreintes digitales, comprenant une API PHP sécurisée et un frontend web en temps réel.

## 🔧 Installation

### 1. Prérequis
- Serveur web (XAMPP, WAMP, ou LAMP)
- PHP 7.4+
- MySQL/MariaDB
- Navigateur web moderne

### 2. Configuration de la base de données
```bash
# Connectez-vous à MySQL
mysql -u root -p

# Exécutez le script SQL
source database.sql
```

### 3. Lancement du serveur

#### Option A: Serveur PHP intégré (Recommandé)
1. Ouvrez un terminal dans le dossier du projet
2. Exécutez `php test_system.php` pour vérifier la configuration
3. Double-cliquez sur `start_server.bat` (Windows) ou `./start_server.sh` (Linux/Mac)
4. Le navigateur s'ouvrira automatiquement sur le dashboard

#### Option B: Serveur web traditionnel
1. Placez tous les fichiers dans votre dossier web (ex: `htdocs` pour XAMPP)
2. Modifiez `config.php` avec vos paramètres de base de données
3. Assurez-vous que le port 3307 est correct pour votre installation MySQL

### 4. Variables d'environnement (Production)
Pour la production, créez un fichier `.env` :
```
DB_HOST=localhost
DB_PORT=3307
DB_USER=root
DB_PASS=votre_mot_de_passe
DB_NAME=monitoring
```

## 🚀 Utilisation

### API Endpoints
- `POST /fingerprint_receiver.php` - Recevoir les données de l'ESP
- `GET /api.php?action=recent&limit=50` - Événements récents
- `GET /api.php?action=stats` - Statistiques du jour
- `GET /api.php?action=hourly` - Données par heure

### Interface Web
- **Serveur PHP intégré**: http://localhost:8000/monitoring.html
- **Serveur traditionnel**: Ouvrez `monitoring.html` dans votre navigateur

## 📊 Fonctionnalités

### Dashboard
- ✅ Statistiques en temps réel
- ✅ Graphique d'activité par heure
- ✅ Liste des événements récents
- ✅ Actualisation automatique (30s)
- ✅ Interface responsive

### Sécurité
- ✅ Configuration externalisée
- ✅ Gestion d'erreurs avec exceptions
- ✅ Validation et sanitisation des données
- ✅ Requêtes préparées (SQL injection protection)

## 🔒 Améliorations de sécurité appliquées

1. **Identifiants externalisés** : Plus de mots de passe en dur
2. **Gestion d'erreurs robuste** : Exceptions au lieu d'exit()
3. **Validation des données** : Filtrage des entrées utilisateur
4. **Configuration correcte** : Host et port séparés
5. **Requêtes sécurisées** : Vérification des échecs de préparation

## 📱 Format des données ESP
```json
{
    "id": 1,
    "confidence": 95,
    "known": true,
    "ts": "2024-01-15 14:30:00"
}
```

## 🧪 Test du système

Avant de lancer le monitoring, testez votre installation :
```bash
php test_system.php
```

Ce script vérifie :
- ✅ Configuration
- ✅ Connexion base de données
- ✅ Structure des tables
- ✅ Insertion de données
- ✅ Fonctionnement de l'API

## 🛠️ Dépannage

### Erreur de connexion DB
- Vérifiez les paramètres dans `config.php`
- Assurez-vous que MySQL est démarré
- Vérifiez le port (3306 par défaut, 3307 dans votre cas)

### CORS Error
- L'API inclut les headers CORS nécessaires
- Servez les fichiers depuis un serveur web, pas en local

### Pas de données
- Vérifiez que la table existe (`database.sql`)
- Testez l'API directement : `api.php?action=stats`