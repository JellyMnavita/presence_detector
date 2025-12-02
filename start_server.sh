#!/bin/bash
echo "Démarrage du serveur PHP pour le monitoring..."
echo ""

# Obtenir l'adresse IP locale
IP=$(hostname -I | awk '{print $1}')

echo "Serveur disponible sur:"
echo "- Local: http://localhost:8000"
echo "- Réseau: http://$IP:8000"
echo "Dashboard: http://$IP:8000/monitoring.html"
echo "API: http://$IP:8000/api.php"
echo ""
echo "Appuyez sur Ctrl+C pour arrêter le serveur"
echo ""

# Ouvrir le navigateur (Linux/Mac)
if command -v xdg-open > /dev/null; then
    xdg-open "http://localhost:8000/monitoring.html"
elif command -v open > /dev/null; then
    open "http://localhost:8000/monitoring.html"
fi

# Démarrer le serveur PHP sur toutes les interfaces
php -S 0.0.0.0:8000