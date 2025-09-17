<?php declare(strict_types=1);

namespace Hubleto\Erp\Installer;

use Hubleto\App\Community\Settings\Models\User;
use Hubleto\App\Community\Settings\Models\UserRole;
use Hubleto\App\Community\Settings\Models\UserHasRole;

class Installer extends \Hubleto\Framework\Core
{

  public string $adminName = '';
  public string $adminFamilyName = '';
  public string $adminNick = '';
  public string $adminEmail = '';
  public string $adminPassword = '';
  public string $accountFullName = '';
  public string $accountRewriteBase = '';
  public string $projectFolder = '';
  public string $projectUrl = '';
  public string $releaseFolder = '';
  public string $secureFolder = '';
  public string $assetsUrl = '';

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

  public function __construct(
    string $env,
    string $uid,
    string $accountFullName,
    string $adminName,
    string $adminFamilyName,
    string $adminNick,
    string $adminEmail,
    string $adminPassword,
    string $accountRewriteBase,
    string $projectFolder,
    string $releaseFolder,
    string $secureFolder,
    string $projectUrl,
    string $assetsUrl,
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
  ) {
    $this->env = $env;
    $this->uid = $uid;
    $this->accountFullName = $accountFullName;
    $this->adminName = $adminName;
    $this->adminFamilyName = $adminFamilyName;
    $this->adminNick = $adminNick;
    $this->adminEmail = $adminEmail;
    $this->adminPassword = $adminPassword;
    $this->accountRewriteBase = $accountRewriteBase;
    $this->projectFolder = str_replace('\\', '/', $projectFolder);
    $this->releaseFolder = str_replace('\\', '/', $releaseFolder);
    $this->secureFolder = str_replace('\\', '/', $secureFolder);
    $this->projectUrl = $projectUrl;
    $this->assetsUrl = $assetsUrl;

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
      throw new \Hubleto\Erp\Exceptions\AccountValidationFailed('Account name is too long.');
    }

    if (!filter_var($this->adminEmail, FILTER_VALIDATE_EMAIL)) {
      throw new \Hubleto\Erp\Exceptions\AccountValidationFailed('Invalid admin email.');
    }

    if (
      is_file($this->projectFolder)
      || is_dir($this->projectFolder)
    ) {
      throw new \Hubleto\Erp\Exceptions\AccountAlreadyExists('Project folder already exists');
    }
  }

  public function createDatabase(): void
  {

    $this->config()->set('db_name', '');
    $this->config()->set('db_host', $this->dbHost);
    $this->config()->set('db_user', $this->dbUser);
    $this->config()->set('db_password', $this->dbPassword);
    $this->db()->init();

    $this->db()->execute("drop database if exists `{$this->dbName}`");
    $this->db()->execute("create database `{$this->dbName}` character set utf8 collate utf8_general_ci");

    $this->config()->set('db_name', $this->dbName);
    $this->config()->set('db_codepage', "utf8mb4");
    $this->db()->init();

  }

  public function initSmtp(): void
  {
    $this->config()->set('smtp_host', $this->smtpHost);
    $this->config()->set('smtp_port', $this->smtpPort);
    $this->config()->set('smtp_encryption', $this->smtpEncryption);
    $this->config()->set('smtp_login', $this->smtpLogin);
    $this->config()->set('smtp_password', $this->smtpPassword);
  }

  public function installBaseModels(): void
  {
    $this->getModel(\Hubleto\Framework\Models\Token::class)->install();
    $this->getModel(\Hubleto\Framework\Models\Config::class)->install();
  }

  public function installApps(int $round): void
  {
    $this->config()->set('premiumRepoFolder', $this->premiumRepoFolder);
    foreach ($this->appsToInstall as $appNamespace => $appConfig) {
      $this->appManager()->installApp($round, $appNamespace, $appConfig, true);
    }
  }

  public function addCompanyAndAdminUser(): void
  {
    $mCompany = $this->getModel(\Hubleto\App\Community\Settings\Models\Company::class);
    $mUser = $this->getService(User::class);
    $mUserHasRole = $this->getService(UserHasRole::class);

    $idCompany = $mCompany->record->recordCreate(['name' => $this->accountFullName])['id'];

    $idUserAdministrator = $mUser->record->recordCreate([
      "type" => User::TYPE_ADMINISTRATOR,
      'first_name' => $this->adminName,
      'last_name' => $this->adminFamilyName,
      'nick' => $this->adminNick,
      'login' => $this->adminEmail,
      'password' => $this->adminPassword == '' ? '' : $mUser->encryptPassword($this->adminPassword),
      'email' => $this->adminEmail,
      'is_active' => true,
      'id_default_company' => $idCompany,
      'language' => 'en',
    ])['id'];

    $mUserHasRole->record->recordCreate([
      'id_user' => $idUserAdministrator,
      'id_role' => UserRole::ROLE_ADMINISTRATOR,
    ])['id'];

    if ($this->adminPassword == '' && $this->smtpHost != '') {
      $this->router()->setUrlParam('login', $this->adminEmail);
      $this->authProvider()->forgotPassword();
    }
  }

  public function getConfigEnvContent(): string
  {
    $configEnv = (string) file_get_contents(__DIR__ . '/Templates/ConfigEnv.php.tpl');
    $configEnv = str_replace('{{ projectFolder }}', $this->projectFolder, $configEnv);
    $configEnv = str_replace('{{ releaseFolder }}', $this->releaseFolder, $configEnv);
    $configEnv = str_replace('{{ secureFolder }}', $this->secureFolder, $configEnv);
    $configEnv = str_replace('{{ projectUrl }}', $this->projectUrl, $configEnv);
    $configEnv = str_replace('{{ assetsUrl }}', $this->assetsUrl, $configEnv);
    $configEnv = str_replace('{{ dbHost }}', $this->config()->getAsString('db_host'), $configEnv);
    $configEnv = str_replace('{{ dbUser }}', $this->dbUser, $configEnv);
    $configEnv = str_replace('{{ dbPassword }}', $this->dbPassword, $configEnv);
    $configEnv = str_replace('{{ dbName }}', $this->dbName, $configEnv);
    $configEnv = str_replace('{{ rewriteBase }}', $this->accountRewriteBase, $configEnv);
    $configEnv = str_replace('{{ accountFullName }}', $this->accountFullName, $configEnv);
    $configEnv = str_replace('{{ sessionSalt }}', \Hubleto\Framework\Helper::str2url($this->uid), $configEnv);
    $configEnv = str_replace('{{ accountUid }}', \Hubleto\Framework\Helper::str2url($this->uid), $configEnv);
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
        } elseif (is_bool($cfgValue)) {
          $configEnv .= '$config[\'' . $cfgParam . '\'] = ' . ($cfgValue ? 'true' : 'false') . ';' . "\n";
        } elseif (is_numeric($cfgValue)) {
          $configEnv .= '$config[\'' . $cfgParam . '\'] = ' . (int) $cfgValue . ';' . "\n";
        } elseif (is_array($cfgValue)) {
          $configEnv .= '$config[\'' . $cfgParam . '\'] = ' . var_export($cfgValue, true) . ';' . "\n";
        }
      }
    }

    return $configEnv;
  }

  public function createFoldersAndFiles(): void
  {

    // folders
    @mkdir($this->projectFolder);
    @mkdir($this->projectFolder . '/log');
    @mkdir($this->projectFolder . '/upload');
    @mkdir($this->projectFolder . '/secure');

    // ConfigEnv.php

    file_put_contents($this->projectFolder . '/ConfigEnv.php', $this->getConfigEnvContent());

    // boot.php
    $index = (string) file_get_contents(__DIR__ . '/Templates/boot.php.tpl');
    $index = str_replace('{{ accountUid }}', \Hubleto\Framework\Helper::str2url($this->accountFullName), $index);
    $index = str_replace('{{ projectFolder }}', $this->projectFolder, $index);
    $index = str_replace('{{ releaseFolder }}', $this->releaseFolder, $index);
    $index = str_replace('{{ secureFolder }}', $this->secureFolder, $index);
    file_put_contents($this->projectFolder . '/boot.php', $index);

    // index.php
    $index = (string) file_get_contents(__DIR__ . '/Templates/index.php.tpl');
    file_put_contents($this->projectFolder . '/index.php', $index);

    // cron.php
    $index = (string) file_get_contents(__DIR__ . '/Templates/cron.php.tpl');
    file_put_contents($this->projectFolder . '/cron.php', $index);

    // hubleto cli agent
    $hubletoCliAgentFile = $this->projectFolder . '/hubleto';
    if (!is_file($hubletoCliAgentFile)) {
      $hubleto = (string) file_get_contents(__DIR__ . '/Templates/hubleto.tpl');
      file_put_contents($hubletoCliAgentFile, $hubleto);
    }

    // .htaccess
    copy(
      __DIR__ . '/Templates/.htaccess.tpl',
      $this->projectFolder . '/.htaccess'
    );

    // .htaccess-secure
    copy(
      __DIR__ . '/Templates/.htaccess-secure.tpl',
      $this->secureFolder . '/.htaccess'
    );  }

  public function installDefaultPermissions(): void
  {
    $apps = $this->appManager()->getEnabledApps();
    array_walk($apps, function ($apps) {
      $apps->installDefaultPermissions();
    });
  }

}
