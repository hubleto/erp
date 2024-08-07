#/usr/bin/sh

nvm use stable

composer --no-interaction config --global use-parent-dir true
# export COMPOSER="composer-test-env.json"

composer --no-interaction install
composer --no-interaction update

npm --prefix /var/www/html/ceremonycrm/libs/ADIOS install /var/www/html/ceremonycrm/libs/ADIOS/

npm i
npm run build

rm -r /var/www/html/ceremonycrm/app/app/bin
cp -r /var/www/html/ceremonycrm/app/src/publ /var/www/html/ceremonycrm/app/app/bin
