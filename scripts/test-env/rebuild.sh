#/usr/bin/sh

composer config --global use-parent-dir true
export COMPOSER="composer-test-env.json"

composer install
composer update

npm --prefix ~/ceremonycrm.com/sub/test-libs/ADIOS install ~/ceremonycrm.com/sub/test-libs/ADIOS/

npm i
npm run build

rm -r ~/ceremonycrm.com/sub/test-app/app/bin
cp -r ~/ceremonycrm.com/sub/test-app/src/publ ~/ceremonycrm.com/sub/test-app/app/bin
