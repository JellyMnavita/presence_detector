@echo off
echo Demarrage du serveur PHP pour le monitoring...
echo.

REM Obtenir l'adresse IP locale
for /f "tokens=2 delims=:" %%i in ('ipconfig ^| findstr /i "IPv4"') do set IP=%%i
for /f "tokens=1" %%i in ("%IP%") do set IP=%%i

echo Serveur disponible sur:
echo - Local: http://localhost:8000
echo - Reseau: http://%IP%:8000
echo Dashboard: http://%IP%:8000/monitoring.html
echo API: http://%IP%:8000/api.php
echo.
echo Appuyez sur Ctrl+C pour arreter le serveur
echo.

REM Demarrer le serveur PHP sur toutes les interfaces (0.0.0.0)
start "" "http://localhost:8000/monitoring.html"
php -S 0.0.0.0:8000

pause