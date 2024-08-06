<?php

namespace CeremonyCrmApp\Lib;

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

  public function __construct(
    \CeremonyCrmApp $app,
    string $companyName,
    string $adminEmail,
    string $accountRootRewriteBase,
    string $accountRootFolder,
    string $accountRootUrl,
    string $appRootFolder,
    string $appRootUrl
  ) {
    $this->app = $app;
    $this->companyName = $companyName;
    $this->adminEmail = $adminEmail;
    $this->accountRootRewriteBase = $accountRootRewriteBase;
    $this->accountRootFolder = $accountRootFolder;
    $this->accountRootUrl = $accountRootUrl;
    $this->appRootFolder = $appRootFolder;
    $this->appRootUrl = $appRootUrl;

    $this->uid = \ADIOS\Core\Helper::str2url($this->companyName);
    $this->uid = $this->uid . '-' . rand(100, 999);

    $this->dbHost = $this->app->config['db_host'];
    $this->dbName = 'crm_' . str_replace('-', '_', $this->uid);
    $this->dbUser = 'usr_' . str_replace('-', '_', $this->uid);
    $this->dbPassword = \ADIOS\Core\Helper::randomPassword();

    $this->adminPassword = \ADIOS\Core\Helper::randomPassword();

// $this->dbName = 'crm_wai_5cac7';

  }

  public function validate() {
    if (!filter_var($this->adminEmail, FILTER_VALIDATE_EMAIL)) {
      throw new Exception('Invalid admin email.');
    }

    if (
      is_file($this->accountRootFolder . '/' . $this->uid)
      || is_dir($this->accountRootFolder . '/' . $this->uid)
    ) {
      throw new Exception('Account folder already exists');
    }
  }

  public function createDatabase() {

    $this->app->initDatabaseConnections();
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

    $this->app->install();

    $mUser = new \CeremonyCrmApp\Modules\Core\Settings\Models\User($this->app);
    $idUserAdministrator = $mUser->eloquent->create([
      'login' => $this->adminEmail,
      'password' => $mUser->hashPassword($this->adminPassword),
      'is_active' => 1,
    ])->id;

    $mUserRole = new \CeremonyCrmApp\Modules\Core\Settings\Models\UserRole($this->app);
    $idRoleAdministrator = $mUserRole->eloquent->create(['name' => 'Administrator'])->id;

    $mUserHasRole = new \CeremonyCrmApp\Modules\Core\Settings\Models\UserHasRole($this->app);
    $mUserHasRole->eloquent->create(['id_user' => $idUserAdministrator, 'id_role' => $idRoleAdministrator])->id;

  }

  public function createFoldersAndFiles() {
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

    // index.php
    $index = file_get_contents($this->app->config['dir'] . '/account_templates/index.php');
    $index = str_replace('{{ appDir }}', $this->appRootFolder, $index);
    file_put_contents($this->accountRootFolder . '/' . $this->uid . '/index.php', $index);
  }

  public function getDatabaseUser(): string {
    $dbUser = \ADIOS\Core\Helper::str2url($this->companyName);
    $dbUser = str_replace('-', '_', $dbUser);
    $dbUser = 'usr_' . $dbUser . '_' . substr(md5(date('YmdHis').rand(1, 10000)), 1, 5);

    return $dbUser;
  }

}