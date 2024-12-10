<?php

namespace CeremonyCrmApp\Core;

use CeremonyCrmApp\Modules\Core\Settings\Models\ {
  Permission, Profile, RolePermission, User, UserRole, UserHasRole
};

class Account {
  public \CeremonyCrmApp $app;
  public string $adminEmail = '';
  public string $adminPassword = '';
  public string $companyName = '';
  public string $accountRootRewriteBase = '';
  public string $accountRootFolder = '';
  public string $accountRootUrl = '';
  public string $appRootFolder = '';
  public string $appRootUrl = '';
  public string $extRootFolder = '';

  public string $uid = '';
  public string $dbHost = '';
  public string $dbName = '';
  public string $dbUser = '';
  public string $dbPassword = '';

  public bool $randomize = false;

  public function __construct(
    \CeremonyCrmApp $app,
    string $uid,
    string $companyName,
    string $adminEmail,
    string $adminPassword,
    string $accountRootRewriteBase,
    string $accountRootFolder,
    string $accountRootUrl,
    string $appRootFolder,
    string $appRootUrl,
    string $extRootFolder,
    string $dbHost,
    string $dbName,
    string $dbUser,
    string $dbPassword,
    bool $randomize = false
  )
  {
    $this->app = $app;
    $this->uid = $uid;
    $this->companyName = $companyName;
    $this->adminEmail = $adminEmail;
    $this->adminPassword = $adminPassword;
    $this->accountRootRewriteBase = $accountRootRewriteBase;
    $this->accountRootFolder = $accountRootFolder;
    $this->accountRootUrl = $accountRootUrl;
    $this->appRootFolder = $appRootFolder;
    $this->appRootUrl = $appRootUrl;
    $this->extRootFolder = $extRootFolder;

    $this->dbHost = $dbHost;
    $this->dbName = $dbName;
    $this->dbUser = $dbUser;
    $this->dbPassword = $dbPassword;

    $this->randomize = $randomize;

  }

  public function validate()
  {
    if (strlen($this->uid) > 32) {
      throw new \CeremonyCrmApp\Exceptions\AccountValidationFailed('Account name is too long.');
    }

    if (!filter_var($this->adminEmail, FILTER_VALIDATE_EMAIL)) {
      throw new \CeremonyCrmApp\Exceptions\AccountValidationFailed('Invalid admin email.');
    }

    if (
      is_file($this->accountRootFolder . '/' . $this->uid)
      || is_dir($this->accountRootFolder . '/' . $this->uid)
    ) {
      throw new \CeremonyCrmApp\Exceptions\AccountAlreadyExists('Account folder already exists');
    }
  }

  public function createDatabase()
  {

    $this->app->pdo->execute("create database {$this->dbName} character set utf8 collate utf8_general_ci");
    $this->app->pdo->execute("create user {$this->dbUser} identified by '{$this->dbPassword}'");
    $this->app->pdo->execute("grant all on {$this->dbName}.* to {$this->dbUser}@{$this->dbHost} identified by '{$this->dbPassword}'");
    $this->app->pdo->execute("flush privileges");

    $this->app->config['db_name'] = $this->dbName;
    $this->app->config['db_codepage'] = "utf8mb4";
    $this->app->initDatabaseConnections();

    foreach ($this->app->registeredModels as $modelClass) {
      $model = $this->app->getModel($modelClass);
      $this->app->db->addTable(
        $model->getFullTableSqlName(),
        $model->columns(),
        $model->isJunctionTable
      );
    }

  }

  public function installTables()
  {

    (new \CeremonyCrmApp\Modules\Core\Settings\Loader($this->app))->installTables();
    (new \CeremonyCrmApp\Modules\Core\Documents\Loader($this->app))->installTables();
    (new \CeremonyCrmApp\Modules\Core\Services\Loader($this->app))->installTables();
    (new \CeremonyCrmApp\Modules\Core\Customers\Loader($this->app))->installTables();
    (new \CeremonyCrmApp\Modules\Core\Invoices\Loader($this->app))->installTables();
    (new \CeremonyCrmApp\Modules\Core\Billing\Loader($this->app))->installTables();
    (new \CeremonyCrmApp\Modules\Sales\Core\Loader($this->app))->installTables();
    (new \CeremonyCrmApp\Modules\Sales\Leads\Loader($this->app))->installTables();
    (new \CeremonyCrmApp\Modules\Sales\Deals\Loader($this->app))->installTables();

    $mProfile = new \CeremonyCrmApp\Modules\Core\Settings\Models\Profile($this->app);
    $mUser = new \CeremonyCrmApp\Modules\Core\Settings\Models\User($this->app);
    $mUserRole = new \CeremonyCrmApp\Modules\Core\Settings\Models\UserRole($this->app);
    $mUserHasRole = new \CeremonyCrmApp\Modules\Core\Settings\Models\UserHasRole($this->app);

    $idProfile = $mProfile->eloquent->create(['company' => $this->companyName])->id;

    $idUserAdministrator = $mUser->eloquent->create([
      'login' => $this->adminEmail,
      'password' => $mUser->hashPassword($this->adminPassword),
      'email' => $this->adminEmail,
      'is_active' => 1,
      'id_active_profile' => $idProfile,
    ])->id;

    $idRoleAdministrator = $mUserRole->eloquent->create(['id' => UserRole::ROLE_ADMINISTRATOR, 'role' => 'Administrator', 'grant_all' => 1])->id;
    $idRoleSalesManager = $mUserRole->eloquent->create(['id' => UserRole::ROLE_SALES_MANAGER, 'role' => 'Sales manager', 'grant_all' => 0])->id;
    $idRoleAccountant = $mUserRole->eloquent->create(['id' => UserRole::ROLE_ACCOUNTANT, 'role' => 'Accountant', 'grant_all' => 0])->id;

    $mUserHasRole->eloquent->create(['id_user' => $idUserAdministrator, 'id_role' => $idRoleAdministrator])->id;
  }

  public function getConfigAccountContent(): string
  {
    $configAccount = file_get_contents($this->app->config['dir'] . '/account_templates/ConfigAccount.tpl');
    $configAccount = str_replace('{{ appDir }}', $this->appRootFolder, $configAccount);
    $configAccount = str_replace('{{ extDir }}', $this->extRootFolder, $configAccount);
    $configAccount = str_replace('{{ appUrl }}', $this->appRootUrl, $configAccount);
    $configAccount = str_replace('{{ dbHost }}', $this->app->config['db_host'], $configAccount);
    $configAccount = str_replace('{{ dbUser }}', $this->dbUser, $configAccount);
    $configAccount = str_replace('{{ dbPassword }}', $this->dbPassword, $configAccount);
    $configAccount = str_replace('{{ dbName }}', $this->dbName, $configAccount);
    $configAccount = str_replace('{{ rewriteBase }}', $this->accountRootRewriteBase . $this->uid . '/', $configAccount);
    $configAccount = str_replace('{{ accountDir }}', $this->accountRootFolder . '/' . $this->uid, $configAccount);
    $configAccount = str_replace('{{ accountUrl }}', $this->accountRootUrl . '/' . $this->uid, $configAccount);
    return $configAccount;
  }

  public function createFoldersAndFiles()
  {
    // folders
    @mkdir($this->accountRootFolder . '/' . $this->uid);
    @mkdir($this->accountRootFolder . '/' . $this->uid . '/log');
    @mkdir($this->accountRootFolder . '/' . $this->uid . '/tmp');
    @mkdir($this->accountRootFolder . '/' . $this->uid . '/upload');

    // ConfigEnv.php

    file_put_contents($this->accountRootFolder . '/' . $this->uid . '/ConfigAccount.php', $this->getConfigAccountContent());

    // LoadApp.php
    $loadApp = file_get_contents($this->app->config['dir'] . '/account_templates/LoadApp.php');
    $loadApp = str_replace('{{ uid }}', $this->uid, $loadApp);
    $loadApp = str_replace('{{ appDir }}', $this->appRootFolder, $loadApp);
    $loadApp = str_replace('{{ extDir }}', $this->extRootFolder, $loadApp);
    file_put_contents($this->accountRootFolder . '/' . $this->uid . '/LoadApp.php', $loadApp);

    // index.php
    copy(
      $this->app->config['dir'] . '/account_templates/index.php',
      $this->accountRootFolder . '/' . $this->uid . '/index.php'
    );

    // .htaccess
    copy(
      $this->app->config['dir'] . '/account_templates/.htaccess',
      $this->accountRootFolder . '/' . $this->uid . '/.htaccess'
    );
  }

  public function installDefaultPermissions() {
    $modules = $this->app->getModules();
    array_walk($modules, function($module) {
      $module->installDefaultPermissions();
    });
  }

  public function createDevelScripts()
  {
    @mkdir($this->accountRootFolder . '/' . $this->uid . '/devel');

    $tplFolder = $this->app->config['dir'] . '/account_templates';
    $accFolder = $this->accountRootFolder . '/' . $this->uid;

    copy($tplFolder . '/devel/Reinstall.php', $accFolder . '/devel/Reinstall.php');
  }

  public function getDatabaseUser(): string {
    $dbUser = \ADIOS\Core\Helper::str2url($this->companyName);
    $dbUser = str_replace('-', '_', $dbUser);
    $dbUser =
      'usr_' . $dbUser
      . ($this->randomize ? '_' . substr(md5(date('YmdHis').rand(1, 10000)), 1, 5) : '')
    ;

    return $dbUser;
  }

}