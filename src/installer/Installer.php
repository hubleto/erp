<?php

namespace HubletoMain\Installer;

class Installer {
  public \HubletoMain $main;

  public string $adminName = '';
  public string $adminFamilyName = '';
  public string $adminNick = '';
  public string $adminEmail = '';
  public string $adminPassword = '';
  public string $accountFullName = '';
  public string $accountRewriteBase = '';
  public string $rootFolder = '';
  public string $rootUrl = '';
  public string $srcFolder = '';
  public string $srcUrl = '';

  public string $premiumRepoFolder = '';

  public string $env = '';
  public string $uid = '';
  public string $dbHost = '';
  public string $dbName = '';
  public string $dbUser = '';
  public string $dbPassword = '';

  public string $smtpHost = '';
  public string $smtpPort = '';
  public string $smtpEncryption = '';
  public string $smtpLogin = '';
  public string $smtpPassword = '';

  public bool $randomize = false;

  /** @property array<string, array<string, mixed>> */
  public array $appsToInstall = [];

  /** @property array<string, string> */
  public array $externalAppsRepositories = [];

  /** @property array<string, mixed> */
  public array $extraConfigEnv = [];

  public array $packages = [
    'core' => [
      \HubletoApp\Community\Settings\Loader::class => [ 'sidebarOrder' => 99997, ],
      \HubletoApp\Community\Tools\Loader::class => [ 'sidebarOrder' => 99997, ],
      \HubletoApp\Community\Desktop\Loader::class => [ ],
      \HubletoApp\Community\Usage\Loader::class => [ ],
      \HubletoApp\Community\Mail\Loader::class => [ 'sidebarOrder' => 125, ],
      \HubletoApp\Community\Notifications\Loader::class => [ 'sidebarOrder' => 125, ],
      \HubletoApp\Community\Documents\Loader::class => [ 'sidebarOrder' => 120, ],
      \HubletoApp\Community\Customers\Loader::class => [ 'sidebarOrder' => 102, 'calendarColor' => '#3DC266' ],
      \HubletoApp\Community\Contacts\Loader::class => [ 'sidebarOrder' => 101, ],
      \HubletoApp\Community\Calendar\Loader::class => [ 'sidebarOrder' => 110, ],
      \HubletoApp\Community\Dashboards\Loader::class => [ 'sidebarOrder' => 99995, ],
      \HubletoApp\Community\Reports\Loader::class => [ 'sidebarOrder' => 99996, ],
      \HubletoApp\Community\Help\Loader::class => [ 'sidebarOrder' => 99998, ],
      \HubletoApp\Community\About\Loader::class => [ 'sidebarOrder' => 99998, ],
    ],
    'cloud' => [
      \HubletoApp\Community\Cloud\Loader::class => [ 'sidebarOrder' => 99999, ],
    ],
    'stock' => [
      \HubletoApp\Community\Suppliers\Loader::class => [ 'sidebarOrder' => 210, ],
      \HubletoApp\Community\Products\Loader::class => [ 'sidebarOrder' => 220, ],
      \HubletoApp\Community\Warehouses\Loader::class => [ 'sidebarOrder' => 230, ],
      \HubletoApp\Community\Inventory\Loader::class => [ 'sidebarOrder' => 240, ],
    ],
    'sales' => [
      \HubletoApp\Community\Suppliers\Loader::class => [ 'sidebarOrder' => 200, ],
      \HubletoApp\Community\Products\Loader::class => [ 'sidebarOrder' => 200, ],
      \HubletoApp\Community\Campaigns\Loader::class => [ 'sidebarOrder' => 202, ],
      \HubletoApp\Community\Leads\Loader::class => [ 'sidebarOrder' => 210, 'calendarColor' => '#C00994', ],
      \HubletoApp\Community\Pipeline\Loader::class => [ 'sidebarOrder' => 220, ],
      \HubletoApp\Community\Deals\Loader::class => [
        'sidebarOrder' => 230,
        'calendarColor' => '#D7B628',
      ],
    ],
    'shop' => [
      \HubletoApp\Community\Suppliers\Loader::class => [ 'sidebarOrder' => 200, ],
      \HubletoApp\Community\Products\Loader::class => [ 'sidebarOrder' => 310, ],
      \HubletoApp\Community\Orders\Loader::class => [ 'sidebarOrder' => 320, ],
    ],
    'invoices' => [
      \HubletoApp\Community\Billing\Loader::class => [ 'sidebarOrder' => 400, ],
      \HubletoApp\Community\Invoices\Loader::class => [ 'sidebarOrder' => 410, ],
      \HubletoApp\Community\Products\Loader::class => [ 'sidebarOrder' => 420, ],
    ],
  ];

  public function __construct(
    \HubletoMain $main,
    string $env,
    string $uid,
    string $accountFullName,
    string $adminName,
    string $adminFamilyName,
    string $adminNick,
    string $adminEmail,
    string $adminPassword,
    string $accountRewriteBase,
    string $rootFolder,
    string $rootUrl,
    string $srcFolder,
    string $srcUrl,
    string $dbHost,
    string $dbName,
    string $dbUser,
    string $dbPassword,
    string $smtpHost,
    string $smtpPort,
    string $smtpEncryption,
    string $smtpLogin,
    string $smtpPassword,
    bool $randomize = false
  )
  {
    $this->main = $main;
    $this->env = $env;
    $this->uid = $uid;
    $this->accountFullName = $accountFullName;
    $this->adminName = $adminName;
    $this->adminFamilyName = $adminFamilyName;
    $this->adminNick = $adminNick;
    $this->adminEmail = $adminEmail;
    $this->adminPassword = $adminPassword;
    $this->accountRewriteBase = $accountRewriteBase;
    $this->rootFolder = str_replace('\\', '/', $rootFolder);
    $this->rootUrl = $rootUrl;
    $this->srcFolder = str_replace('\\', '/', $srcFolder);
    $this->srcUrl = $srcUrl;

    $this->dbHost = $dbHost;
    $this->dbName = $dbName;
    $this->dbUser = $dbUser;
    $this->dbPassword = $dbPassword;

    $this->smtpHost = $smtpHost;
    $this->smtpPort = $smtpPort;
    $this->smtpEncryption = $smtpEncryption;
    $this->smtpLogin = $smtpLogin;
    $this->smtpPassword = $smtpPassword;

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
      is_file($this->rootFolder)
      || is_dir($this->rootFolder)
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

  public function initSmtp(): void {
    $this->main->config->set('smtp_host', $this->smtpHost);
    $this->main->config->set('smtp_port', $this->smtpPort);
    $this->main->config->set('smtp_encryption', $this->smtpEncryption);
    $this->main->config->set('smtp_login', $this->smtpLogin);
    $this->main->config->set('smtp_password', $this->smtpPassword);
  }

  public function installBaseModels(): void
  {
    (new \HubletoMain\Core\Models\Token($this->main))->install();
    (new \ADIOS\Models\Config($this->main))->install();
  }

  public function installApps(int $round): void
  {
    $this->main->config->set('premiumRepoFolder', $this->premiumRepoFolder);
    foreach ($this->appsToInstall as $appNamespace => $appConfig) {
      $this->main->apps->installApp($round, $appNamespace, $appConfig, true);
    }
  }

  public function addCompanyAndAdminUser(): void
  {
    $mCompany = new \HubletoApp\Community\Settings\Models\Company($this->main);
    $mUser = new \HubletoApp\Community\Settings\Models\User($this->main);
    $mUserHasRole = new \HubletoApp\Community\Settings\Models\UserHasRole($this->main);

    $idCompany = $mCompany->record->recordCreate(['name' => $this->accountFullName])['id'];

    $idUserAdministrator = $mUser->record->recordCreate([
      'first_name' => $this->adminName,
      'last_name' => $this->adminFamilyName,
      'nick' => $this->adminNick,
      'login' => $this->adminEmail,
      'password' => $this->adminPassword == '' ? '' : $mUser->hashPassword($this->adminPassword),
      'email' => $this->adminEmail,
      'is_active' => true,
      'id_default_company' => $idCompany,
      'language' => 'en',
    ])['id'];

    $mUserHasRole->record->recordCreate([
      'id_user' => $idUserAdministrator,
      'id_role' => \HubletoApp\Community\Settings\Models\UserRole::ROLE_ADMINISTRATOR,
    ])['id'];

    if ($this->adminPassword == '' && $this->smtpHost != '') {
      $this->main->setUrlParam('login', $this->adminEmail);
      $this->main->auth->forgotPassword();
    }
  }

  public function getConfigEnvContent(): string
  {
    $configEnv = (string) file_get_contents(__DIR__ . '/../code_templates/project/ConfigEnv.php.tpl');
    $configEnv = str_replace('{{ srcFolder }}', $this->srcFolder, $configEnv);
    $configEnv = str_replace('{{ rootFolder }}', $this->rootFolder, $configEnv);
    $configEnv = str_replace('{{ srcUrl }}', $this->srcUrl, $configEnv);
    $configEnv = str_replace('{{ dbHost }}', $this->main->config->getAsString('db_host'), $configEnv);
    $configEnv = str_replace('{{ dbUser }}', $this->dbUser, $configEnv);
    $configEnv = str_replace('{{ dbPassword }}', $this->dbPassword, $configEnv);
    $configEnv = str_replace('{{ dbName }}', $this->dbName, $configEnv);
    $configEnv = str_replace('{{ rewriteBase }}', $this->accountRewriteBase, $configEnv);
    $configEnv = str_replace('{{ rootUrl }}', $this->rootUrl, $configEnv);
    $configEnv = str_replace('{{ accountFullName }}', $this->accountFullName, $configEnv);
    $configEnv = str_replace('{{ sessionSalt }}', \ADIOS\Core\Helper::str2url($this->uid), $configEnv);
    $configEnv = str_replace('{{ accountUid }}', \ADIOS\Core\Helper::str2url($this->uid), $configEnv);
    $configEnv = str_replace('{{ premiumRepoFolder }}', $this->premiumRepoFolder, $configEnv);

    $configEnv = str_replace('{{ smtpHost }}', $this->smtpHost, $configEnv);
    $configEnv = str_replace('{{ smtpPort }}', $this->smtpPort, $configEnv);
    $configEnv = str_replace('{{ smtpEncryption }}', $this->smtpEncryption, $configEnv);
    $configEnv = str_replace('{{ smtpLogin }}', $this->smtpLogin, $configEnv);
    $configEnv = str_replace('{{ smtpPassword }}', $this->smtpPassword, $configEnv);

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

    if (count($this->extraConfigEnv) > 0) {
      foreach ($this->extraConfigEnv as $cfgParam => $cfgValue) {
        if (is_string($cfgValue)) {
          $configEnv .= '$config[\'' . $cfgParam . '\'] = \'' . $cfgValue . '\';' . "\n";
        } else if (is_bool($cfgValue)) {
          $configEnv .= '$config[\'' . $cfgParam . '\'] = ' . ($cfgValue ? 'true' : 'false') . ';' . "\n";
        } else if (is_numeric($cfgValue)) {
          $configEnv .= '$config[\'' . $cfgParam . '\'] = ' . (int) $cfgValue . ';' . "\n";
        } else if (is_array($cfgValue)) {
          $configEnv .= '$config[\'' . $cfgParam . '\'] = ' . var_export($cfgValue, true) . ';' . "\n";
        }
      }
    }

    return $configEnv;
  }

  public function createFoldersAndFiles(): void
  {

    // folders
    @mkdir($this->rootFolder);
    @mkdir($this->rootFolder . '/log');
    @mkdir($this->rootFolder . '/upload');

    // ConfigEnv.php

    file_put_contents($this->rootFolder . '/ConfigEnv.php', $this->getConfigEnvContent());

    // index.php
    $index = (string) file_get_contents(__DIR__ . '/../code_templates/project/index.php.tpl');
    $index = str_replace('{{ accountUid }}', \ADIOS\Core\Helper::str2url($this->accountFullName), $index);
    $index = str_replace('{{ srcFolder }}', $this->srcFolder, $index);
    $index = str_replace('{{ rootFolder }}', $this->rootFolder, $index);
    file_put_contents($this->rootFolder . '/index.php', $index);

    // hubleto cli agent
    $hubletoCliAgentFile = $this->rootFolder . '/hubleto';
    if (!is_file($hubletoCliAgentFile)) {
      $hubleto = (string) file_get_contents(__DIR__ . '/../code_templates/project/hubleto.tpl');
      $hubleto = str_replace('{{ srcFolder }}', $this->srcFolder, $hubleto);
      file_put_contents($hubletoCliAgentFile, $hubleto);
    }

    // .htaccess
    copy(
      __DIR__ . '/../code_templates/project/.htaccess.tpl',
      $this->rootFolder . '/.htaccess'
    );
  }

  public function installDefaultPermissions(): void
  {
    $apps = $this->main->apps->getEnabledApps();
    array_walk($apps, function($apps) {
      $apps->installDefaultPermissions();
    });
  }

}