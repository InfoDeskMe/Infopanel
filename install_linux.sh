#!/bin/bash

echo installing necessary files...
cd app/
cp .env.dist .env
rm -rf .env.dist 
cd public/
cp config.json.dist config.json
cp data_ticker.json.dist data_ticker.json
cp data.json.dist data.json
cd ../../
docker-compose build 
docker-compose up -d
echo '*************************************************'
echo 'Installation is still in progress. Please wait...'
echo '*************************************************'
sleep 45 ## necessary, because the mysql container needs longer sometimes...
cd app/
docker cp dump.sql database_symfony:/dump.sql
docker exec -i database_symfony mysql -h 0.0.0.0 -P 3306 -usymfony -psymfony symfony_docker < dump.sql
docker exec -it php npm install --legacy-peer-deps
docker exec -it php npm run build
docker exec -it php composer require symfony/flex
docker exec -it php composer install 
rm -rf .env
cp .envprod.dist .env
xdg-open http://localhost
open http://localhost
rm -rf .env_prod.dist

echo The Installation has been finished. Please try to call localhost in your browser
read