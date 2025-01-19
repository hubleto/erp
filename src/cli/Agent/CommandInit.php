<?php

namespace HubletoMain\Cli\Agent;

class CommandInit extends \HubletoMain\Cli\Agent\Command
{
  public function run()
  {
    // define('HUBLETO_COMMUNITY_REPO', __DIR__ . '/../../apps/community');

    $rewriteBase = null;
    $accountFolder = null;
    $accountUrl = null;
    $mainFolder = null;
    $mainUrl = null;
    $dbHost = null;
    $dbUser = null;
    $dbPassword = null;
    $dbName = null;
    $dbCodepage = null;
    $companyName = null;
    $adminName = null;
    $adminFamilyName = null;
    $adminEmail = null;
    $adminPassword = null;
    $packagesToInstall = null;


    $configFile = $this->arguments[2] ?? '';

    if (!empty($configFile) && is_file($configFile)) {
      $config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($configFile)) ?? [];

      if (isset($config['rewriteBase'])) $rewriteBase = $config['rewriteBase'];
      if (isset($config['accountFolder'])) $accountFolder = $config['accountFolder'];
      if (isset($config['accountUrl'])) $accountUrl = $config['accountUrl'];
      if (isset($config['mainFolder'])) $mainFolder = $config['mainFolder'];
      if (isset($config['mainUrl'])) $mainUrl = $config['mainUrl'];
      if (isset($config['dbHost'])) $dbHost = $config['dbHost'];
      if (isset($config['dbUser'])) $dbUser = $config['dbUser'];
      if (isset($config['dbPassword'])) $dbPassword = $config['dbPassword'];
      if (isset($config['dbName'])) $dbName = $config['dbName'];
      if (isset($config['dbCodepage'])) $dbCodepage = $config['dbCodepage'];
      if (isset($config['companyName'])) $companyName = $config['companyName'];
      if (isset($config['adminName'])) $adminName = $config['adminName'];
      if (isset($config['adminFamilyName'])) $adminFamilyName = $config['adminFamilyName'];
      if (isset($config['adminEmail'])) $adminEmail = $config['adminEmail'];
      if (isset($config['adminPassword'])) $adminPassword = $config['adminPassword'];
      if (isset($config['packagesToInstall'])) $packagesToInstall = $config['packagesToInstall'];
    }

    $rewriteBases = [];
    $lastRewriteBase = '';

    foreach (array_reverse(explode('/', str_replace('\\', '/', __DIR__))) as $tmpDir) {
      $rewriteBases[] = $lastRewriteBase . '/';
      $lastRewriteBase = '/' . $tmpDir . $lastRewriteBase;
    }

    if ($rewriteBase === null) $rewriteBase = $this->cli->choose($rewriteBases, 'ConfigEnv.rewriteBase', '/');
    if ($accountFolder === null) $accountFolder = realpath(__DIR__ . '/../..');
    if ($accountUrl === null) $accountUrl = $this->cli->read('ConfigEnv.accountUrl', 'http://localhost/' . trim($rewriteBase, '/'));
    if ($mainFolder === null) $mainFolder = realpath(__DIR__ . '/../..');
    if ($mainUrl === null) $mainUrl = $accountUrl;
    if ($dbHost === null) $dbHost = $this->cli->read('ConfigEnv.dbHost', 'localhost');
    if ($dbUser === null) $dbUser = $this->cli->read('ConfigEnv.dbUser (user must exist)', 'root');
    if ($dbPassword === null) $dbPassword = $this->cli->read('ConfigEnv.dbPassword');
    if ($dbName === null) $dbName = $this->cli->read('ConfigEnv.dbName (database will be created)', 'my_hubleto');
    if ($dbCodepage === null) $dbCodepage = $this->cli->read('ConfigEnv.dbCodepage', 'utf8mb4');
    if ($companyName === null) $companyName = $this->cli->read('Account.companyName', 'My Company');
    if ($adminName === null) $adminName = $this->cli->read('Account.adminName', 'John');
    if ($adminFamilyName === null) $adminFamilyName = $this->cli->read('Account.adminFamilyName', 'Smith');
    if ($adminEmail === null) $adminEmail = $this->cli->read('Account.adminEmail (will be used also for login)', 'john.smith@example.com');
    if ($adminPassword === null) $adminPassword = $this->cli->read('Account.adminPassword (leave empty to generate random password)');

    $this->cli->cyan("Initializing with following config:\n");
    $this->cli->cyan("  rewriteBase = {$rewriteBase}\n");
    $this->cli->cyan("  accountFolder = {$accountFolder}\n");
    $this->cli->cyan("  accountUrl = {$accountUrl}\n");
    $this->cli->cyan("  dbHost = {$dbHost}\n");
    $this->cli->cyan("  dbUser = {$dbUser}\n");
    $this->cli->cyan("  dbPassword = {$dbPassword}\n");
    $this->cli->cyan("  dbName = {$dbName}\n");
    $this->cli->cyan("  dbCodepage = {$dbCodepage}\n");
    $this->cli->cyan("  companyName = {$companyName}\n");
    $this->cli->cyan("  adminName = {$adminName}\n");
    $this->cli->cyan("  adminFamilyName = {$adminFamilyName}\n");
    $this->cli->cyan("  adminEmail = {$adminEmail}\n");
    $this->cli->cyan("  adminPassword = {$adminPassword}\n");
    $this->cli->cyan("  packagesToInstall = {$packagesToInstall}\n");

    $this->main->config['db_host'] = $dbHost;
    $this->main->config['db_user'] = $dbUser;
    $this->main->config['db_password'] = $dbPassword;
    $this->main->config['db_name'] = $dbName;
    $this->main->initDatabaseConnections();

    if (empty($packagesToInstall)) $packagesToInstall = 'core,sales';
    if (empty($adminPassword)) $adminPassword = \ADIOS\Core\Helper::randomPassword();

    $this->cli->cyan("\n");
    $this->cli->cyan("Hurray. Installing your Hubleto...\n");

    // install
    $installer = new \HubletoMain\Installer\Installer(
      $this->main,
      'local-env',
      '', // uid
      $companyName,
      $adminName,
      $adminFamilyName,
      $adminEmail,
      $adminPassword,
      $rewriteBase,
      $accountFolder,
      $accountUrl,
      realpath(__DIR__ . '/../../..'), // mainFolder
      $mainUrl, // mainUrl
      $dbHost,
      $dbName,
      $dbUser,
      $dbPassword,
      false, // randomize (deprecated)
    );

    $installer->installedApps = [];
    foreach (explode(',', $packagesToInstall) as $package) {
      $this->cli->cyan("  Package: {$package}\n");
      $installer->installedApps = array_merge(
        $installer->installedApps,
        $installer->packages[trim($package)]
      );
    }

    $installer->createDatabase();
    $installer->installTables();
    $installer->installDefaultPermissions();
    $installer->createFoldersAndFiles();

    $this->cli->cyan("\n");
    $this->cli->cyan("All done! You're a fantastic CRM developer. Now you can:\n");
    $this->cli->cyan("  -> Open {$accountUrl} and sign in with '{$adminEmail}' and '{$adminPassword}'.\n");
    $this->cli->cyan("  -> Note for NGINX users: don't forget to configure your locations in nginx.conf.\n");
    $this->cli->cyan("     See https://developer.hubleto.com/nginx for more details.\n");
    $this->cli->cyan("  -> Create your app in ./src/apps.\n");
    $this->cli->cyan("     See https://developer.hubleto.com/start-developing-own-module for tips how to start.\n");
    $this->cli->cyan("     See https://developer.hubleto.com/publish-module for instructions how to publish.\n");
    $this->cli->cyan("  -> Check the developer's guide at https://developer.hubleto.com for more tips & tricks.\n");
  }
}