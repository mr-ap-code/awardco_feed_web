@echo off
:: Set variables
set PHP_URL=https://windows.php.net/downloads/releases/php-8.1.10-Win32-vs16-x64.zip
set PHP_DIR=C:\php
set PHP_INI=php.ini-production

:: Download PHP
echo Downloading PHP...
powershell -Command "Invoke-WebRequest -Uri %PHP_URL% -OutFile php.zip"

:: Extract the ZIP file
echo Extracting PHP...
powershell -Command "Expand-Archive -Path php.zip -DestinationPath %PHP_DIR%"

:: Rename php.ini-development to php.ini
if exist "%PHP_DIR%\%PHP_INI%" (
    echo Renaming php.ini-production to php.ini...
    rename "%PHP_DIR%\%PHP_INI%" php.ini
) else (
    echo php.ini-production not found!
)

:: Open php.ini for configuration
if exist "%PHP_DIR%\php.ini" (
    echo Opening php.ini for configuration...
    start notepad "%PHP_DIR%\php.ini"
) else (
    echo php.ini not found!
)

:: Enable curl extension
echo Enabling curl extension...
powershell -Command "(gc %PHP_DIR%\php.ini) -replace ';extension=curl', 'extension=curl' | Out-File -encoding ASCII %PHP_DIR%\php.ini"

:: Clean up
echo Cleaning up...
del php.zip

:: Add PHP to system PATH for the current session
echo Adding PHP to system PATH...
set PATH=%PATH%;%PHP_DIR%

:: Verify installation
echo Verifying PHP installation...
php -v

:: Set up the project directory
echo Setting up the project directory...
xcopy * C:\awardco-feed /E /H /C /I

:: Start PHP built-in web server
echo Starting PHP built-in web server...
php -S localhost:8080 -t public
echo PHP built-in web server started at http://localhost:8080

echo Done!
pause