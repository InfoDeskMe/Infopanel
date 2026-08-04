@echo off
setlocal enabledelayedexpansion

echo installing necessary files...

if not exist .env (
    echo Generating random database credentials and app secret...
    rem hex, not base64: MYSQL_PASSWORD ends up embedded in a DATABASE_URL
    rem docker-compose.yml builds, where base64's '+' and '/' would corrupt it.
    rem RandomNumberGenerator]::GetBytes(int) is a .NET 6+ convenience overload
    rem that doesn't exist in Windows PowerShell 5.1 (.NET Framework) -- use
    rem ::Create().GetBytes(byte[]) instead, which works on both.
    for /f "delims=" %%i in ('powershell -NoProfile -Command "$b=New-Object byte[](24); [System.Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($b); -join ($b | ForEach-Object { $_.ToString('x2') })"') do set GEN_ROOT_PW=%%i
    for /f "delims=" %%i in ('powershell -NoProfile -Command "$b=New-Object byte[](24); [System.Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($b); -join ($b | ForEach-Object { $_.ToString('x2') })"') do set GEN_USER_PW=%%i
    for /f "delims=" %%i in ('powershell -NoProfile -Command "$b=New-Object byte[](16); [System.Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($b); -join ($b | ForEach-Object { $_.ToString('x2') })"') do set GEN_APP_SECRET=%%i
    (
        echo MYSQL_ROOT_PASSWORD=!GEN_ROOT_PW!
        echo MYSQL_DATABASE=symfony_docker
        echo MYSQL_USER=symfony
        echo MYSQL_PASSWORD=!GEN_USER_PW!
        echo APP_SECRET=!GEN_APP_SECRET!
    ) > .env
)

docker compose build
echo *************************************************
echo The installation is still in progress. Please wait...
echo *************************************************
rem --wait blocks until every service with a healthcheck reports healthy,
rem instead of guessing a fixed timeout -- migrations and the initial admin
rem account are created automatically once the database is actually ready
rem (see backend/docker-entrypoint.sh), no interactive prompts involved.
docker compose up -d --wait
echo The installation has been finished...
echo Initial admin credentials (shown once, change on first login):
docker logs backend_infopanel 2>&1 | findstr /C:"Initial admin account created" /C:"Email:" /C:"Password:"
start http://localhost
pause
