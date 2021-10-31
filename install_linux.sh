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
cd app/
docker exec -it php npm install
docker exec -it php npm run build
docker exec -it php composer install
docker exec -it php php bin/console doctrine:database:create
docker exec -it php php bin/console doctrine:migrations:migrate
docker exec -it php php bin/console doctrine:fixtures:load
rm -rf .env
cp .envprod.dist .env
rm -rf .env_prod.dist
rm -rf src/DataFixtures/AppFixtures.php 

echo The Installation has been finished. Please try to call localhost in your browser
read