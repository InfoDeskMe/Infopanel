@echo off

echo installing necessary files...
cd backend\
COPY  .env.dist .env
Rem DEL /S .env.dist
cd public\
COPY  config.json.dist config.json
COPY  data_ticker.json.dist data_ticker.json
COPY  data.json.dist data.json
cd ..\..\
docker compose build
docker compose up -d
echo *************************************************
echo The installation is still in progress. Please wait...
echo *************************************************
timeout /t 45
cd backend\
call docker cp dump.sql database_infopanel:/dump.sql
call docker exec -i database_infopanel mysql -h 0.0.0.0 -P 3306 -usymfony -psymfony symfony_docker < dump.sql
call docker exec -it backend_infopanel composer install
COPY  .envprod.dist .env
echo The installation has been finished...
start http://localhost
pause
