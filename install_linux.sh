#!/bin/bash

echo installing necessary files...

if [ ! -f .env ]; then
  echo "Generating random database credentials and app secret..."
  # hex, not base64: MYSQL_PASSWORD ends up embedded in a DATABASE_URL
  # docker-compose.yml builds, where base64's '+' and '/' would corrupt it.
  {
    echo "MYSQL_ROOT_PASSWORD=$(openssl rand -hex 24)"
    echo "MYSQL_DATABASE=symfony_docker"
    echo "MYSQL_USER=symfony"
    echo "MYSQL_PASSWORD=$(openssl rand -hex 24)"
    echo "APP_SECRET=$(openssl rand -hex 16)"
  } > .env
fi

docker compose build
echo '*************************************************'
echo 'Installation is still in progress. Please wait...'
echo '*************************************************'
# --wait blocks until every service with a healthcheck reports healthy,
# instead of guessing a fixed sleep -- migrations and the initial admin
# account are created automatically once the database is actually ready
# (see backend/docker-entrypoint.sh), no interactive prompts involved.
docker compose up -d --wait
xdg-open http://localhost
open http://localhost

echo '***********************************************************************************'
echo 'The Installation has been finished. Please try to call localhost in your browser...'
echo 'Initial admin credentials (shown once, change on first login):'
echo '***********************************************************************************'
docker logs backend_infopanel 2>&1 | grep -A 3 "Initial admin account created"
read
