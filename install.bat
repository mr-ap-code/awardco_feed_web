@echo off
:: Download PHP from https://windows.php.net/download/
:: Extract the ZIP file to C:\php

:: Rename php.ini-development to php.ini
:: Open php.ini and configure as needed

:: Add PHP to system PATH
setx PATH "%PATH%;C:\php"

:: Verify installation
php -v

:: Set up the project directory
mkdir C:\awardco-feed
xcopy * C:\awardco-feed /E /H /C /I

:: Start PHP built-in web server
php -S localhost:8080 -t public
echo PHP built-in web server started at http://localhost:8080