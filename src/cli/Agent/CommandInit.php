<?php

namespace HubletoMain\Cli\Agent;

class CommandInit extends \HubletoMain\Cli\Agent\Command
{
  public function run()
  {
    // define('HUBLETO_COMMUNITY_REPO', __DIR__ . '/../../apps/community');

    $configFile = $this->arguments[2] ?? '';

    if (!empty($configFile) && is_file($configFile)) {
      $config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($configFile)) ?? [];

      $this->cli->cyan("Initializing with following config:\n" . print_r($config, true). "\n");

      $rewriteBase = $config['rewriteBase'];
      $accountRootFolder = $config['accountRootFolder'];
      $accountUrl = $config['accountUrl'];
      $dbHost = $config['dbHost'];
      $dbUser = $config['dbUser'];
      $dbPassword = $config['dbPassword'];
      $dbName = $config['dbName'];
      $dbCodepage = $config['dbCodepage'];

      $companyName = $config['companyName'];
      $adminName = $config['adminName'];
      $adminFamilyName = $config['adminFamilyName'];
      $adminEmail = $config['adminEmail'];
      $adminPassword = $config['adminPassword'];

      $packagesToInstall = $config['packagesToInstall'];

    } else {
      $rewriteBases = [];
      $lastRewriteBase = '';
      $packagesToInstall = '';

      foreach (array_reverse(explode('/', str_replace('\\', '/', __DIR__))) as $tmpDir) {
        $rewriteBases[] = $lastRewriteBase . '/';
        $lastRewriteBase = '/' . $tmpDir . $lastRewriteBase;
      }

      $this->cli->cyan(': Configuration of the environment :' . "\n\n");
      $rewriteBase = $this->cli->choose($rewriteBases, 'ConfigEnv.rewriteBase', '/');
      $accountRootFolder = realpath(__DIR__ . '/../..');
      $accountUrl = $this->cli->read('ConfigEnv.accountUrl', 'http://localhost/' . trim($rewriteBase, '/'));
      $dbHost = $this->cli->read('ConfigEnv.dbHost', 'localhost');
      $dbUser = $this->cli->read('ConfigEnv.dbUser (user must exist)', 'root');
      $dbPassword = $this->cli->read('ConfigEnv.dbPassword');
      $dbName = $this->cli->read('ConfigEnv.dbName (database will be created)', 'my_hubleto');
      $dbCodepage = $this->cli->read('ConfigEnv.dbCodepage', 'utf8mb4');

      $this->cli->cyan("\n");
      $this->cli->cyan(': Configuration of the admin account :' . "\n\n");

      $companyName = $this->cli->read('Account.companyName', 'My Company');
      $adminName = $this->cli->read('Account.adminName', 'John');
      $adminFamilyName = $this->cli->read('Account.adminFamilyName', 'Smith');
      $adminEmail = $this->cli->read('Account.adminEmail (will be used also for login)', 'john.smith@example.com');
      $adminPassword = $this->cli->read('Account.adminPassword (leave empty to generate random password)');
    }

    $this->main->config['db_host'] = $dbHost;
    $this->main->config['db_user'] = $dbUser;
    $this->main->config['db_password'] = $dbPassword;
    $this->main->config['db_name'] = $dbName;
    $this->main->initDatabaseConnections();

    if (empty($packagesToInstall)) $packagesToInstall = 'core,sales';
    if (empty($adminPassword)) $adminPassword = \ADIOS\Core\Helper::randomPassword();

    // $this->mainConfig = [
    //   'db_host' => $dbHost,
    //   'db_user' => $dbUser,
    //   'db_password' => $dbPassword,
    //   'dir' => __DIR__,
    //   'logDir' => __DIR__ . '/log',

    //   'accountRootRewriteBase' => $rewriteBase,
    //   'accountRootFolder' => __DIR__,
    //   'accountRootUrl' => $accountUrl,
    //   'mainRootUrl' => $accountUrl, // main and account are the same folders in single-tenant installation
    //   'mainRootFolder' => __DIR__,
    // ];

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
      $accountRootFolder,
      $accountUrl,
      realpath(__DIR__ . '/../..'), // mainRootFolder
      $accountUrl, // mainRootUrl
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