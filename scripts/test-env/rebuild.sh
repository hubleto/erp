#/usr/bin/sh

composer --no-interaction config --global use-parent-dir true

composer --no-interaction install
composer --no-interaction update

npm -v

npm --prefix /var/www/html/github/ADIOS install /var/www/html/github/ADIOS/

npm i
npm run build

rm -r /var/www/html/ceremonycrm/app/app/bin
cp -r /var/www/html/ceremonycrm/app/src/publ /var/www/html/ceremonycrm/app/app/bin
