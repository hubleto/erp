<?php

namespace HubletoMain\Installer;

use HubletoApp\Community\Settings\Models\ {
  Permission, Profile, RolePermission, User, UserRole, UserHasRole
};

class Installer {
  public \HubletoMain $main;
  public string $adminName = '';
  public string $adminFamilyName = '';
  public string $adminEmail = '';
  public string $adminPassword = '';
  public string $companyName = '';
  public string $accountRewriteBase = '';
  public string $accountFolder = '';
  public string $accountUrl = '';
  public string $mainFolder = '';
  public string $mainUrl = '';

  public string $env = '';
  public string $uid = '';
  public string $dbHost = '';
  public string $dbName = '';
  public string $dbUser = '';
  public string $dbPassword = '';

  public bool $randomize = false;

  public array $apps = [];

  public array $packages = [
    'core' => [
      \HubletoApp\Community\Settings\Loader::class => [ 'enabled' => true ],
      \HubletoApp\Community\Dashboard\Loader::class => [ 'enabled' => true ],
      \HubletoApp\Community\Upgrade\Loader::class => [ 'enabled' => true ],
      \HubletoApp\Community\Calendar\Loader::class => [ 'enabled' => true ],
      \HubletoApp\Community\Customers\Loader::class => [ 'enabled' => true ],
      \HubletoApp\Community\Documents\Loader::class => [ 'enabled' => true ],
    ],
    'invoices' => [
      \HubletoApp\Community\Billing\Loader::class => [ 'enabled' => true ],
      \HubletoApp\Community\Invoices\Loader::class => [ 'enabled' => true ],
      \HubletoApp\Community\Services\Loader::class => [ 'enabled' => true ],
    ],
    'sales' => [
      \HubletoApp\Community\Pipeline\Loader::class => [ 'enabled' => true ],
      \HubletoApp\Community\Deals\Loader::class => [ 'enabled' => true ],
      \HubletoApp\Community\Leads\Loader::class => [ 'enabled' => true ],
    ],
    'sync' => [
      \HubletoApp\Community\CalendarSync\Loader::class => [ 'enabled' => true ],
    ],
  ];

  public function __construct(
    \HubletoMain $main,
    string $env,
    string $uid,
    string $companyName,
    string $adminName,
    string $adminFamilyName,
    string $adminEmail,
    string $adminPassword,
    string $accountRewriteBase,
    string $accountFolder,
    string $accountUrl,
    string $mainFolder,
    string $mainUrl,
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
    $this->accountRewriteBase = $accountRewriteBase;
    $this->accountFolder = $accountFolder;
    $this->accountUrl = $accountUrl;
    $this->mainFolder = $mainFolder;
    $this->mainUrl = $mainUrl;

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
      is_file($this->accountFolder . '/' . $this->uid)
      || is_dir($this->accountFolder . '/' . $this->uid)
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

    (new \ADIOS\Models\Config($this->main))->install();

    (new \HubletoApp\Community\Settings\Loader($this->main))->installTables();
    (new \HubletoApp\Community\Documents\Loader($this->main))->installTables();
    (new \HubletoApp\Community\Services\Loader($this->main))->installTables();
    (new \HubletoApp\Community\Customers\Loader($this->main))->installTables();
    (new \HubletoApp\Community\Invoices\Loader($this->main))->installTables();
    (new \HubletoApp\Community\Billing\Loader($this->main))->installTables();
    (new \HubletoApp\Community\Pipeline\Loader($this->main))->installTables();
    (new \HubletoApp\Community\Leads\Loader($this->main))->installTables();
    (new \HubletoApp\Community\Deals\Loader($this->main))->installTables();

    $mProfile = new \HubletoApp\Community\Settings\Models\Profile($this->main);
    $mUser = new \HubletoApp\Community\Settings\Models\User($this->main);
    $mUserRole = new \HubletoApp\Community\Settings\Models\UserRole($this->main);
    $mUserHasRole = new \HubletoApp\Community\Settings\Models\UserHasRole($this->main);

    $idProfile = $mProfile->eloquent->create(['company' => $this->companyName])->id;

    $idUserAdministrator = $mUser->eloquent->create([
      'login' => $this->adminEmail,
      'password' => $mUser->hashPassword($this->adminPassword),
      'email' => $this->adminEmail,
      'is_active' => true,
      'id_active_profile' => $idProfile,
    ])->id;

    $idRoleAdministrator = $mUserRole->eloquent->create(['id' => UserRole::ROLE_ADMINISTRATOR, 'role' => 'Administrator', 'grant_all' => 1])->id;
    $idRoleSalesManager = $mUserRole->eloquent->create(['id' => UserRole::ROLE_SALES_MANAGER, 'role' => 'Sales manager', 'grant_all' => 0])->id;
    $idRoleAccountant = $mUserRole->eloquent->create(['id' => UserRole::ROLE_ACCOUNTANT, 'role' => 'Accountant', 'grant_all' => 0])->id;

    $mUserHasRole->eloquent->create(['id_user' => $idUserAdministrator, 'id_role' => $idRoleAdministrator])->id;
  }

  public function getConfigEnvContent(): string
  {
    $configEnv = file_get_contents(__DIR__ . '/template/ConfigEnv.tpl');
    $configEnv = str_replace('{{ mainFolder }}', $this->mainFolder, $configEnv);
    $configEnv = str_replace('{{ mainUrl }}', $this->mainUrl, $configEnv);
    $configEnv = str_replace('{{ dbHost }}', $this->main->config['db_host'], $configEnv);
    $configEnv = str_replace('{{ dbUser }}', $this->dbUser, $configEnv);
    $configEnv = str_replace('{{ dbPassword }}', $this->dbPassword, $configEnv);
    $configEnv = str_replace('{{ dbName }}', $this->dbName, $configEnv);
    $configEnv = str_replace('{{ rewriteBase }}', $this->accountRewriteBase . (empty($this->uid) ? '' : $this->uid . '/'), $configEnv);
    $configEnv = str_replace('{{ accountUrl }}', $this->accountUrl . (empty($this->uid) ? '' : '/' . $this->uid), $configEnv);

    $configEnv .= '' . "\n";
    $configEnv .= '$config[\'apps\'] = [' . "\n";
    foreach ($this->apps as $appClass => $appConfig) {
      $configEnv .= '  \\' . $appClass . '::class => ' . var_export($appConfig, true) . ',' . "\n";
    }
    $configEnv .= '];' . "\n";

    $configEnv .= '' . "\n";
    $configEnv .= '$config[\'env\'] = \'' . $this->env . '\';' . "\n";

    return $configEnv;
  }

  public function createFoldersAndFiles()
  {

    // folders
    @mkdir($this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid));
    @mkdir($this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/log');
    @mkdir($this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/tmp');
    @mkdir($this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/upload');

    // ConfigEnv.php

    file_put_contents($this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/ConfigEnv.php', $this->getConfigEnvContent());

    // index.php
    $index = file_get_contents(__DIR__ . '/template/index.php');
    $index = str_replace('{{ uid }}', $this->uid, $index);
    $index = str_replace('{{ mainFolder }}', $this->mainFolder, $index);
    file_put_contents($this->accountFolder . '/' . $this->uid . '/index.php', $index);

    // hubleto cli agent
    $hubletoCliAgentFile = $this->accountFolder . '/' . $this->uid . '/hubleto';
    if (!is_file($hubletoCliAgentFile)) {
      $hubleto = file_get_contents(__DIR__ . '/template/hubleto');
      $hubleto = str_replace('{{ mainFolder }}', $this->mainFolder, $hubleto);
      file_put_contents($hubletoCliAgentFile, $hubleto);
    }

    // .htaccess
    copy(
      __DIR__ . '/template/.htaccess',
      $this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/.htaccess'
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
  //   @mkdir($this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/devel');

  //   $tplFolder = __DIR__ . '/template';
  //   $accFolder = $this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid);

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