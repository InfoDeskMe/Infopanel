@echo off

echo installing necessary files...
cd app\
COPY  .env.dist .env
Rem DEL /S .env.dist
cd public\
COPY  config.json.dist config.json
COPY  data_ticker.json.dist data_ticker.json
COPY  data.json.dist data.json
cd ..\..\
docker-compose build
docker-compose up -d
echo *************************************************
echo The installation is still in progress. Please wait...
echo *************************************************
timeout /t 45
cd app\
call docker cp dump.sql database_symfony:/dump.sql
call docker exec -i database_symfony mysql -h 0.0.0.0 -P 3306 -usymfony -psymfony symfony_docker < dump.sql
call docker exec -it php npm install --legacy-peer-deps
call docker exec -it php npm run build
call docker exec -it php composer require symfony/flex
call docker exec -it php composer install
DEL /S .env
COPY  .envprod.dist .env
Rem DEL /S .env_prod.dist
echo The installation has been finished...
start http://localhost
pause
