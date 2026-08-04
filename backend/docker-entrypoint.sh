#!/bin/sh
set -e

# var/data and public/uploads are named volumes (see docker-compose.yml) and
# start out empty AND root-owned on a fresh installation, since this
# entrypoint itself runs as root. php-fpm's worker processes run as
# www-data (see www.conf), so without the chown below every write -- even
# just creating the .lock file -- fails with "Permission denied".
mkdir -p var/data public/uploads
[ -f var/data/data.json ] || cp public/data.json.dist var/data/data.json
[ -f var/data/data_ticker.json ] || cp public/data_ticker.json.dist var/data/data_ticker.json
[ -f var/data/config.json ] || cp public/config.json.dist var/data/config.json
chown -R www-data:www-data var/data public/uploads

# Deferred from build time (see Dockerfile) to now, when real DATABASE_URL /
# APP_SECRET / etc. environment variables are actually available.
php bin/console cache:clear --no-interaction
php bin/console assets:install public --no-interaction
php bin/console importmap:install --no-interaction

# Safe to run on every start: migrations track what's already applied, and
# create-admin skips itself once any admin account exists. depends_on's
# service_healthy condition (see docker-compose.yml) guarantees the database
# is actually ready by the time this runs, not just that its container
# process has started -- no more interactive "are you sure?" prompts or
# racing a database that's still initializing.
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console app:create-admin

exec "$@"
