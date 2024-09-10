#/usr/bin/bash

exec 2>&1 1>/path/to/file.out

date

. ~/.nvm/nvm.sh
nvm install stable
nvm use stable

echo GIT PULL ADIOS
cd /var/www/html/github/ADIOS
git config pull.rebase false
git reset --hard
git pull

echo GIT PULL APP
cd /var/www/html/ceremonycrm/app
git config pull.rebase false
git reset --hard
git pull

chmod +x /var/www/html/ceremonycrm/app/scripts/test-env/rebuild.sh

cd /var/www/html/ceremonycrm/app

echo COMPOSER

composer --no-interaction config --global use-parent-dir true

composer --no-interaction install
composer --no-interaction update

echo NPM RUN BUILD

npm -v
npm --prefix /var/www/html/github/ADIOS install /var/www/html/github/ADIOS/
npm i
npm run build

echo COPY SRC TO BIN
rm -r /var/www/html/ceremonycrm/app/app/bin
cp -r /var/www/html/ceremonycrm/app/src/publ /var/www/html/ceremonycrm/app/app/bin

echo CHOWN, CHMOD
sudo chown -R www-data.www-data /var/www/html/ceremonycrm
sudo chmod -R 775 /var/www/html/ceremonycrm

