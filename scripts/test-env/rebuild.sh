#/usr/bin/sh

env COMPOSER=composer-test-env.json
composer install
composer update

npm i
npm run build