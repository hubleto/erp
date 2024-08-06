# Ceremony CRM

Installation steps:

  *  vytvor si v root foldri s projektami adresar **ROOT/ceremonycrm** (tak, aby bol viditelny cez http://localhost/ceremony)
  *  do neho si naklonuj https://github.com/wai-blue/ceremonycrm tak, aby vysledny naklonovany kod sa nachadzal v **ROOT/ceremonycrm/app** a teda aby bol viditelny cez http://localhost/ceremony/app)
  *  cd **ROOT/ceremonycrm/app**
      - `npm i`
      - `composer install`
  *  vytvor si adresar **ROOT/www** a rozbal si do neho prilozeny zip
  *  vytvor si adresar **ROOT/www/log**
  *  uprav si DB connection v **ROOT/www/config.php**
  *  vytvor si adresar **ROOT/www/accounts**
  *  v prehliadaci si otvor http://localhost/ceremonycrm/www/create_account.php a vytvor si ucet