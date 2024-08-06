# Ceremony CRM

Installation steps:

  *  vytvor si v root foldri s projektami adresar **ROOT/ceremonycrm** (tak, aby bol viditelny cez http://localhost/ceremony)
  *  vytvor si adresar **ROOT/ceremonycrm/app** a do neho si naklonuj https://github.com/wai-blue/ceremonycrm
  *  cd **ROOT/ceremonycrm/app**
      - `npm i`
      - `composer install`
      - `npm run build`
  * vytvor si symlink, target = **ROOT/ceremonycrm/src**, link = **ROOT/ceremonycrm/app/bin**
  *  vytvor si adresar **ROOT/ceremonycrm/www** a rozbal si do neho prilozeny zip
  *  vytvor si adresar **ROOT/ceremonycrm/www/log**
  *  uprav si DB connection v **ROOT/ceremonycrm/www/config.php**
  *  vytvor si adresar **ROOT/ceremonycrm/accounts**
  *  v prehliadaci si otvor http://localhost/ceremonycrm/www/create_account.php a vytvor si ucet