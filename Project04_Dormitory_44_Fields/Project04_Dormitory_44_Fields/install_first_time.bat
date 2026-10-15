@echo off
setlocal
cd /d "%~dp0backend"
if errorlevel 1 goto failed
where php >nul 2>nul
if errorlevel 1 if exist C:\xampp\php\php.exe set "PATH=C:\xampp\php;%PATH%"
where php >nul 2>nul
if errorlevel 1 goto failed
if not exist .env copy .env.example .env
where composer >nul 2>nul
if errorlevel 1 goto failed
call composer install
if errorlevel 1 goto failed
php setup_db.php
if errorlevel 1 goto failed
php -r "$e=file_get_contents('.env'); exit(preg_match('/^APP_KEY=base64:/m',$e) ? 0 : 1);"
if errorlevel 1 php artisan key:generate
if errorlevel 1 goto failed
php artisan migrate
if errorlevel 1 goto failed
echo Installed. Optional demo data on a NEW EMPTY database: php artisan db:seed
echo Run start.bat to open the project.
pause

exit /b 0
:failed
echo FAILED. Check the error above. PHP, Composer and MySQL must be available.
pause
exit /b 1
