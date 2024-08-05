# Ceremony CRM

Installation steps:

  * Clone this repo
  * Run scripts/inity_repo_run_as_administrator.bat
    * You must run this script as administrator - it creates symlink.
  * Run `composer install`
  * Run `npm i`
  * Create an empty database (if not exists).
  * Modify ConfigEnv.php according to your environment.
  * Run scripts\install.bat

Now you can login as `administrator` with password `administrator`.
