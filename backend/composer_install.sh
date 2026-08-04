#!/bin/bash -e
composer install 
#composer install && php -S backend_infopanel:9000
##composer install && composer require symfony/debug-bundle --dev && php bin/console server:run 0.0.0.0:9000