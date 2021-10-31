@echo off

echo installing necessary files...
cd app\
COPY  .env.dist .env
DEL /S .env.dist
cd public\
COPY  config.json.dist config.json
COPY  data_ticker.json.dist data_ticker.json
COPY  data.json.dist data.json
cd ..\..\
docker-compose build
docker-compose up -d
cd app\
call docker exec -it php npm install
call docker exec -it php npm run build
call docker exec -it php composer install
call docker exec -it php php bin/console doctrine:database:create
call docker exec -it php php bin/console doctrine:migrations:migrate
call docker exec -it php php bin/console doctrine:fixtures:load
DEL /S .env
COPY  .envprod.dist .env
DEL /S .env_prod.dist
DEL /S src\DataFixtures\AppFixtures.php
echo The Installation has been finished. Please try to call localhost in your browser
pause
