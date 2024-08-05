@echo off

echo This script must be run under administrator privileges.
pause

mkdir %~dp0..\log
mkdir %~dp0..\upload

mklink /D "%~dp0..\app\bin" "%~dp0..\src\publ"

copy %~dp0..\ConfigEnv.tpl %~dp0..\ConfigEnv.php

echo.
echo Repo has been initialized. Following steps must be done manually:
echo.  - Run `composer install`
echo.  - Run `npm i`
echo.  - Create an empty database (if not exists).
echo.  - Modify ConfigEnv.php according to your environment.
echo.  - Run scripts\install.bat
pause
