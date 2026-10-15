@echo off
setlocal
cd /d "%~dp0backend"
if errorlevel 1 goto failed
where php >nul 2>nul
if errorlevel 1 if exist C:\xampp\php\php.exe set "PATH=C:\xampp\php;%PATH%"
where php >nul 2>nul
if errorlevel 1 goto failed
if not exist vendor\autoload.php goto failed
if not exist .env goto failed
start "" "http://localhost:8080/login"
php artisan serve --port=8080
if errorlevel 1 goto failed

exit /b 0
:failed
echo FAILED. Check the error above. PHP, Composer and MySQL must be available.
pause
exit /b 1
