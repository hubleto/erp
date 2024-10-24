<?php

namespace CeremonyCrmApp\Core;

use CeremonyCrmApp\Modules\Core\Settings\Models\ {
  Permission, Profile, RolePermission, User, UserRole, UserHasRole
};

class Account {
  public \CeremonyCrmApp $app;
  public string $adminEmail = '';
  public string $companyName = '';
  public string $accountRootRewriteBase = '';
  public string $accountRootFolder = '';
  public string $accountRootUrl = '';
  public string $appRootFolder = '';
  public string $appRootUrl = '';

  public string $uid = '';
  public string $dbHost = '';
  public string $dbName = '';
  public string $dbUser = '';
  public string $dbPassword = '';
  public string $adminPassword = '';

  public bool $randomize = false;

  public function __construct(
    \CeremonyCrmApp $app,
    string $companyName,
    string $adminEmail,
    string $accountRootRewriteBase,
    string $accountRootFolder,
    string $accountRootUrl,
    string $appRootFolder,
    string $appRootUrl,
    bool $randomize = false
  )
  {
    $this->app = $app;
    $this->companyName = $companyName;
    $this->adminEmail = $adminEmail;
    $this->accountRootRewriteBase = $accountRootRewriteBase;
    $this->accountRootFolder = $accountRootFolder;
    $this->accountRootUrl = $accountRootUrl;
    $this->appRootFolder = $appRootFolder;
    $this->appRootUrl = $appRootUrl;

    $this->randomize = $randomize;

    $this->uid = \ADIOS\Core\Helper::str2url($this->companyName);
    $this->uid = $this->uid . ($this->randomize ? '-' . rand(100, 999) : '');

    $this->dbHost = $this->app->config['db_host'];
    $this->dbName = 'crm_account_' . str_replace('-', '_', $this->uid);
    $this->dbUser = 'crm_account_usr_' . str_replace('-', '_', $this->uid);
    $this->dbPassword = \ADIOS\Core\Helper::randomPassword();

    $this->adminPassword = \ADIOS\Core\Helper::randomPassword();

// $this->dbName = 'crm_wai_5cac7';

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

    // $this->app->install();

    $mProfile = new Profile($this->app);
    $mUser = new User($this->app);
    $mUserRole = new UserRole($this->app);
    $mUserHasRole = new UserHasRole($this->app);
    $mPermission = new Permission($this->app);
    $mRolePermission = new RolePermission($this->app);

    $mProfile->dropTableIfExists()->install();
    $mUser->dropTableIfExists()->install();
    $mUserRole->dropTableIfExists()->install();
    $mUserHasRole->dropTableIfExists()->install();
    $mPermission->dropTableIfExists()->install();
    $mRolePermission->dropTableIfExists()->install();

    $idProfile = $mProfile->eloquent->create(['company' => $this->companyName])->id;

    $idUserAdministrator = $mUser->eloquent->create([
      'login' => $this->adminEmail,
      'password' => $mUser->hashPassword($this->adminPassword),
      'email' => $this->adminEmail,
      'is_active' => 1,
      'id_active_profile' => $idProfile,
    ])->id;

    $idRoleAdministrator = $mUserRole->eloquent->create(['role' => 'Administrator'])->id;

    $mUserHasRole->eloquent->create(['id_user' => $idUserAdministrator, 'id_role' => $idRoleAdministrator])->id;


  }

  public function createFoldersAndFiles()
  {
    // folders
    @mkdir($this->accountRootFolder . '/' . $this->uid);
    @mkdir($this->accountRootFolder . '/' . $this->uid . '/log');
    @mkdir($this->accountRootFolder . '/' . $this->uid . '/tmp');
    @mkdir($this->accountRootFolder . '/' . $this->uid . '/upload');

    // ConfigEnv.php
    $configAccount = file_get_contents($this->app->config['dir'] . '/account_templates/ConfigAccount.tpl');
    $configAccount = str_replace('{{ appDir }}', $this->appRootFolder, $configAccount);
    $configAccount = str_replace('{{ appUrl }}', $this->appRootUrl, $configAccount);
    $configAccount = str_replace('{{ dbHost }}', $this->app->config['db_host'], $configAccount);
    $configAccount = str_replace('{{ dbUser }}', $this->dbUser, $configAccount);
    $configAccount = str_replace('{{ dbPassword }}', $this->dbPassword, $configAccount);
    $configAccount = str_replace('{{ dbName }}', $this->dbName, $configAccount);
    $configAccount = str_replace('{{ rewriteBase }}', $this->accountRootRewriteBase . $this->uid . '/', $configAccount);
    $configAccount = str_replace('{{ accountDir }}', $this->accountRootFolder . '/' . $this->uid, $configAccount);
    $configAccount = str_replace('{{ accountUrl }}', $this->accountRootUrl . '/' . $this->uid, $configAccount);

    file_put_contents($this->accountRootFolder . '/' . $this->uid . '/ConfigAccount.php', $configAccount);

    // LoadApp.php
    $loadApp = file_get_contents($this->app->config['dir'] . '/account_templates/LoadApp.php');
    $loadApp = str_replace('{{ appDir }}', $this->appRootFolder, $loadApp);
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

  public function generateTestData()
  {
    $registeredModules = $this->app->getRegisteredModules();
    array_walk($registeredModules, function($moduleClass) {
      $module = new $moduleClass($this->app);
      $module->generateTestData();
    });
  }

  public function createPermissions() {
    $registeredModules = $this->app->getRegisteredModules();
    array_walk($registeredModules, function($moduleClass) {
      $module = new $moduleClass($this->app);
      $module->createPermissions();
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