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
  public string $accountFullName = '';
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

  /** @property array<string, array<string, mixed>> */
  public array $appsToInstall = [];

  /** @property array<string, string> */
  public array $externalAppsRepositories = [];

  public array $packages = [
    'core' => [
      \HubletoApp\Community\Settings\Loader::class => [ 'sidebarOrder' => 99997, ],
      \HubletoApp\Community\Dashboard\Loader::class => [ ],
      \HubletoApp\Community\Premium\Loader::class => [ 'sidebarOrder' => 99999, ],
      \HubletoApp\Community\Calendar\Loader::class => [ 'sidebarOrder' => 110, ],
      \HubletoApp\Community\Customers\Loader::class => [ 'sidebarOrder' => 101, ],
      \HubletoApp\Community\Contacts\Loader::class => [ ],
      \HubletoApp\Community\Reports\Loader::class => [ 'sidebarOrder' => 99996, ],
      \HubletoApp\Community\Help\Loader::class => [ 'sidebarOrder' => 99998, ],
    ],
    'documents' => [
      \HubletoApp\Community\Documents\Loader::class => [ 'sidebarOrder' => 120, ],
    ],
    'sales' => [
      \HubletoApp\Community\Pipeline\Loader::class => [ 'sidebarOrder' => 200, ],
      \HubletoApp\Community\Services\Loader::class => [ 'sidebarOrder' => 210, ],
      \HubletoApp\Community\Leads\Loader::class => [ 'sidebarOrder' => 220, ],
      \HubletoApp\Community\Deals\Loader::class => [ 'sidebarOrder' => 230, ],
      \HubletoApp\Community\Goals\Loader::class => [ 'sidebarOrder' => 240, ],
    ],
    'shop' => [
      \HubletoApp\Community\Products\Loader::class => [ 'sidebarOrder' => 310, ],
      \HubletoApp\Community\Orders\Loader::class => [ 'sidebarOrder' => 320, ],
    ],
    'invoices' => [
      \HubletoApp\Community\Billing\Loader::class => [ 'sidebarOrder' => 400, ],
      \HubletoApp\Community\Invoices\Loader::class => [ 'sidebarOrder' => 410, ],
      \HubletoApp\Community\Services\Loader::class => [ 'sidebarOrder' => 420, ],
    ],
  ];

  public function __construct(
    \HubletoMain $main,
    string $env,
    string $uid,
    string $accountFullName,
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
    $this->accountFullName = $accountFullName;
    $this->adminName = $adminName;
    $this->adminFamilyName = $adminFamilyName;
    $this->adminEmail = $adminEmail;
    $this->adminPassword = $adminPassword;
    $this->accountRewriteBase = $accountRewriteBase;
    $this->accountFolder = str_replace('\\', '/', $accountFolder);
    $this->accountUrl = $accountUrl;
    $this->mainFolder = str_replace('\\', '/', $mainFolder);
    $this->mainUrl = $mainUrl;

    $this->dbHost = $dbHost;
    $this->dbName = $dbName;
    $this->dbUser = $dbUser;
    $this->dbPassword = $dbPassword;

    $this->randomize = $randomize;

  }

  public function validate(): void
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

  public function createDatabase(): void
  {

    $this->main->config->set('db_name', '');
    $this->main->config->set('db_host', $this->dbHost);
    $this->main->config->set('db_user', $this->dbUser);
    $this->main->config->set('db_password', $this->dbPassword);
    $this->main->initDatabaseConnections();

    $this->main->pdo->execute("drop database if exists `{$this->dbName}`");
    $this->main->pdo->execute("create database `{$this->dbName}` character set utf8 collate utf8_general_ci");

    $this->main->config->set('db_name', $this->dbName);
    $this->main->config->set('db_codepage', "utf8mb4");
    $this->main->initDatabaseConnections();

  }

  public function installApps(): void
  {
    (new \ADIOS\Models\Config($this->main))->install();

    foreach ($this->appsToInstall as $appNamespace => $appConfig) {
      $this->main->appManager->installApp($appNamespace, $appConfig, true);
    }
  }

  public function addCompanyProfileAndAdminUser(): void
  {
    $mProfile = new \HubletoApp\Community\Settings\Models\Profile($this->main);
    $mUser = new \HubletoApp\Community\Settings\Models\User($this->main);
    $mUserHasRole = new \HubletoApp\Community\Settings\Models\UserHasRole($this->main);

    $idProfile = $mProfile->eloquent->create(['company' => $this->accountFullName])->id;

    $idUserAdministrator = $mUser->eloquent->create([
      'login' => $this->adminEmail,
      'password' => $mUser->hashPassword($this->adminPassword),
      'email' => $this->adminEmail,
      'is_active' => true,
      'id_active_profile' => $idProfile,
    ])->id;

    $mUserHasRole->eloquent->create([
      'id_user' => $idUserAdministrator,
      'id_role' => \HubletoApp\Community\Settings\Models\UserRole::ROLE_ADMINISTRATOR,
    ])->id;
  }

  public function getConfigEnvContent(): string
  {
    $configEnv = (string) file_get_contents(__DIR__ . '/../code_templates/project/ConfigEnv.php.tpl');
    $configEnv = str_replace('{{ mainFolder }}', $this->mainFolder, $configEnv);
    $configEnv = str_replace('{{ mainUrl }}', $this->mainUrl, $configEnv);
    $configEnv = str_replace('{{ dbHost }}', $this->main->config->getAsString('db_host'), $configEnv);
    $configEnv = str_replace('{{ dbUser }}', $this->dbUser, $configEnv);
    $configEnv = str_replace('{{ dbPassword }}', $this->dbPassword, $configEnv);
    $configEnv = str_replace('{{ dbName }}', $this->dbName, $configEnv);
    $configEnv = str_replace('{{ rewriteBase }}', $this->accountRewriteBase . (empty($this->uid) ? '' : $this->uid . '/'), $configEnv);
    $configEnv = str_replace('{{ accountUrl }}', $this->accountUrl . (empty($this->uid) ? '' : '/' . $this->uid), $configEnv);
    $configEnv = str_replace('{{ accountFullName }}', $this->accountFullName, $configEnv);
    $configEnv = str_replace('{{ sessionSalt }}', \ADIOS\Core\Helper::str2url($this->accountFullName), $configEnv);
    $configEnv = str_replace('{{ accountUid }}', \ADIOS\Core\Helper::str2url($this->accountFullName), $configEnv);

    if (count($this->externalAppsRepositories) > 0) {
      $configEnv .= '' . "\n";
      $configEnv .= '$config[\'externalAppsRepositories\'] = [' . "\n";
      foreach ($this->externalAppsRepositories as $vendor => $folder) {
        $configEnv .= '  \'' . $vendor . '\' => \'' . str_replace('\\', '/', (string) ($folder)) . '\',' . "\n";
      }
      $configEnv .= '];' . "\n";
    }

    $configEnv .= '' . "\n";
    $configEnv .= '$config[\'env\'] = \'' . $this->env . '\';' . "\n";

    return $configEnv;
  }

  public function createFoldersAndFiles(): void
  {

    // folders
    @mkdir($this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid));
    @mkdir($this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/log');
    @mkdir($this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/tmp');
    @mkdir($this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/upload');

    // ConfigEnv.php

    file_put_contents($this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/ConfigEnv.php', $this->getConfigEnvContent());

    // index.php
    $index = (string) file_get_contents(__DIR__ . '/../code_templates/project/index.php.tpl');
    $index = str_replace('{{ accountUid }}', \ADIOS\Core\Helper::str2url($this->accountFullName), $index);
    $index = str_replace('{{ mainFolder }}', $this->mainFolder, $index);
    file_put_contents($this->accountFolder . '/' . $this->uid . '/index.php', $index);

    // hubleto cli agent
    $hubletoCliAgentFile = $this->accountFolder . '/' . $this->uid . '/hubleto';
    if (!is_file($hubletoCliAgentFile)) {
      $hubleto = (string) file_get_contents(__DIR__ . '/../code_templates/project/hubleto.tpl');
      $hubleto = str_replace('{{ mainFolder }}', $this->mainFolder, $hubleto);
      file_put_contents($hubletoCliAgentFile, $hubleto);
    }

    // .htaccess
    copy(
      __DIR__ . '/../code_templates/project/.htaccess.tpl',
      $this->accountFolder . (empty($this->uid) ? '' : '/' . $this->uid) . '/.htaccess'
    );
  }

  public function installDefaultPermissions(): void
  {
    $apps = $this->main->appManager->getRegisteredApps();
    array_walk($apps, function($apps) {
      $apps->installDefaultPermissions();
    });
  }

}