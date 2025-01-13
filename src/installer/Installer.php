<?php

namespace HubletoMain\Installer;

use HubletoApp\Settings\Models\ {
  Permission, Profile, RolePermission, User, UserRole, UserHasRole
};

class Installer {
  public \HubletoMain $main;
  public string $adminName = '';
  public string $adminFamilyName = '';
  public string $adminEmail = '';
  public string $adminPassword = '';
  public string $companyName = '';
  public string $accountRootRewriteBase = '';
  public string $accountRootFolder = '';
  public string $accountRootUrl = '';
  public string $mainRootFolder = '';
  public string $mainRootUrl = '';
  public string $extRootFolder = '';

  public string $env = '';
  public string $uid = '';
  public string $dbHost = '';
  public string $dbName = '';
  public string $dbUser = '';
  public string $dbPassword = '';

  public bool $randomize = false;

  public array $enabledApps = [];

  public function __construct(
    \HubletoMain $main,
    string $env,
    string $uid,
    string $companyName,
    string $adminName,
    string $adminFamilyName,
    string $adminEmail,
    string $adminPassword,
    string $accountRootRewriteBase,
    string $accountRootFolder,
    string $accountRootUrl,
    string $mainRootFolder,
    string $mainRootUrl,
    string $extRootFolder,
    string $dbHost,
    string $dbName,
    string $dbUser,
    string $dbPassword,
    bool $randomize = false
  )
  {
    $this->main = $main;
    $this->env = $env;
    $this->uid = $uid;
    $this->companyName = $companyName;
    $this->adminName = $adminName;
    $this->adminFamilyName = $adminFamilyName;
    $this->adminEmail = $adminEmail;
    $this->adminPassword = $adminPassword;
    $this->accountRootRewriteBase = $accountRootRewriteBase;
    $this->accountRootFolder = $accountRootFolder;
    $this->accountRootUrl = $accountRootUrl;
    $this->mainRootFolder = $mainRootFolder;
    $this->mainRootUrl = $mainRootUrl;
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
      throw new \HubletoMain\Exceptions\AccountValidationFailed('Account name is too long.');
    }

    if (!filter_var($this->adminEmail, FILTER_VALIDATE_EMAIL)) {
      throw new \HubletoMain\Exceptions\AccountValidationFailed('Invalid admin email.');
    }

    if (
      is_file($this->accountRootFolder . '/' . $this->uid)
      || is_dir($this->accountRootFolder . '/' . $this->uid)
    ) {
      throw new \HubletoMain\Exceptions\AccountAlreadyExists('Account folder already exists');
    }
  }

  public function createDatabase()
  {

    $this->main->pdo->execute("drop database if exists `{$this->dbName}`");
    $this->main->pdo->execute("create database `{$this->dbName}` character set utf8 collate utf8_general_ci");

    $this->main->config['db_name'] = $this->dbName;
    $this->main->config['db_codepage'] = "utf8mb4";
    $this->main->initDatabaseConnections();

    foreach ($this->main->registeredModels as $modelClass) {
      $model = $this->main->getModel($modelClass);
      $this->main->db->addTable(
        $model->getFullTableSqlName(),
        $model->columns(),
        $model->isJunctionTable
      );
    }

  }

  public function installTables()
  {

    (new \HubletoApp\Settings\Loader($this->main))->installTables();
    (new \HubletoApp\Documents\Loader($this->main))->installTables();
    (new \HubletoApp\Services\Loader($this->main))->installTables();
    (new \HubletoApp\Customers\Loader($this->main))->installTables();
    (new \HubletoApp\Invoices\Loader($this->main))->installTables();
    (new \HubletoApp\Billing\Loader($this->main))->installTables();
    (new \HubletoApp\Pipeline\Loader($this->main))->installTables();
    (new \HubletoApp\Leads\Loader($this->main))->installTables();
    (new \HubletoApp\Deals\Loader($this->main))->installTables();

    $mProfile = new \HubletoApp\Settings\Models\Profile($this->main);
    $mUser = new \HubletoApp\Settings\Models\User($this->main);
    $mUserRole = new \HubletoApp\Settings\Models\UserRole($this->main);
    $mUserHasRole = new \HubletoApp\Settings\Models\UserHasRole($this->main);

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
    $configAccount = file_get_contents(__DIR__ . '/template/ConfigAccount.tpl');
    $configAccount = str_replace('{{ mainRootFolder }}', $this->mainRootFolder, $configAccount);
    $configAccount = str_replace('{{ mainRootUrl }}', $this->mainRootUrl, $configAccount);
    $configAccount = str_replace('{{ dbHost }}', $this->main->config['db_host'], $configAccount);
    $configAccount = str_replace('{{ dbUser }}', $this->dbUser, $configAccount);
    $configAccount = str_replace('{{ dbPassword }}', $this->dbPassword, $configAccount);
    $configAccount = str_replace('{{ dbName }}', $this->dbName, $configAccount);
    $configAccount = str_replace('{{ rewriteBase }}', $this->accountRootRewriteBase . (empty($this->uid) ? '' : $this->uid . '/'), $configAccount);
    $configAccount = str_replace('{{ accountUrl }}', $this->accountRootUrl . (empty($this->uid) ? '' : '/' . $this->uid), $configAccount);

    $configAccount .= '' . "\n";
    $configAccount .= '$config[\'enabledApps\'] = [' . "\n";
    foreach ($this->enabledApps as $app) {
      $configAccount .= '  ' . $app . ',' . "\n";
    }
    $configAccount .= '];' . "\n";

    $configAccount .= '' . "\n";
    $configAccount .= '$config[\'env\'] = \'' . $this->env . '\';' . "\n";

    return $configAccount;
  }

  public function createFoldersAndFiles()
  {
    // folders
    @mkdir($this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid));
    @mkdir($this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/log');
    @mkdir($this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/tmp');
    @mkdir($this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/upload');

    // ConfigEnv.php

    file_put_contents($this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/ConfigAccount.php', $this->getConfigAccountContent());

    // LoadMain.php
    $loadMain = file_get_contents(__DIR__ . '/template/LoadMain.php');
    $loadMain = str_replace('{{ uid }}', $this->uid, $loadMain);
    $loadMain = str_replace('{{ mainRootFolder }}', $this->mainRootFolder, $loadMain);
    file_put_contents($this->accountRootFolder . '/' . $this->uid . '/LoadMain.php', $loadMain);

    // index.php
    copy(
      __DIR__ . '/template/index.php',
      $this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/index.php'
    );

    // .htaccess
    copy(
      __DIR__ . '/template/.htaccess',
      $this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/.htaccess'
    );
  }

  public function installDefaultPermissions() {
    $modules = $this->main->getRegisteredApps();
    array_walk($modules, function($module) {
      $module->installDefaultPermissions();
    });
  }

  // public function createDevelScripts()
  // {
  //   @mkdir($this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/devel');

  //   $tplFolder = __DIR__ . '/template';
  //   $accFolder = $this->accountRootFolder . (empty($this->uid) ? '' : '/' . $this->uid);

  //   copy($tplFolder . '/devel/Reinstall.php', $accFolder . '/devel/Reinstall.php');
  // }

  // public function getDatabaseUser(): string {
  //   $dbUser = \ADIOS\Core\Helper::str2url($this->companyName);
  //   $dbUser = str_replace('-', '_', $dbUser);
  //   $dbUser =
  //     'usr_' . $dbUser
  //     . ($this->randomize ? '_' . substr(md5(date('YmdHis').rand(1, 10000)), 1, 5) : '')
  //   ;

  //   return $dbUser;
  // }

}