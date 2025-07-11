<?php

namespace HubletoMain\Cli\Agent;

class CommandInit extends \HubletoMain\Cli\Agent\Command
{
  public array $initConfig = [];

  public function parseConfigFile(string $configFile): array
  {
    $configStr = (string) file_get_contents($configFile);
    $config = (array) (\Symfony\Component\Yaml\Yaml::parse($configStr) ?? []);
    return $config;
  }

  public function run(): void
  {
    $rewriteBase = null;
    $rootFolder = null;
    $rootUrl = null;
    $srcFolder = null;
    $srcUrl = null;
    $dbHost = null;
    $dbUser = null;
    $dbPassword = null;
    $dbName = null;
    $dbCodepage = null;
    $smtpHost = null;
    $smtpPort = null;
    $smtpEncryption = null;
    $smtpLogin = null;
    $smtpPassword = null;
    $accountFullName = null;
    $adminName = null;
    $adminFamilyName = null;
    $adminNick = null;
    $adminNick = null;
    $adminEmail = null;
    $adminPassword = null;
    $packagesToInstall = null;
    $appsToInstall = null;
    $externalAppsRepositories = [];
    $premiumRepoFolder = null;

    $configFile = (string) ($this->arguments[2] ?? '');

    if (!empty($configFile) && is_file($configFile)) {
      $config = $this->parseConfigFile($configFile);
    } else {
      $config = $this->initConfig;
    }

    if (isset($config['rewriteBase'])) $rewriteBase = $config['rewriteBase'];
    if (isset($config['rootFolder'])) $rootFolder = $config['rootFolder'];
    if (isset($config['rootUrl'])) $rootUrl = $config['rootUrl'];
    if (isset($config['srcFolder'])) $srcFolder = $config['srcFolder'];
    if (isset($config['srcUrl'])) $srcUrl = $config['srcUrl'];
    if (isset($config['dbHost'])) $dbHost = $config['dbHost'];
    if (isset($config['dbUser'])) $dbUser = $config['dbUser'];
    if (isset($config['dbPassword'])) $dbPassword = $config['dbPassword'];
    if (isset($config['dbName'])) $dbName = $config['dbName'];
    if (isset($config['dbCodepage'])) $dbCodepage = $config['dbCodepage'];
    if (isset($config['accountFullName'])) $accountFullName = $config['accountFullName'];
    if (isset($config['adminName'])) $adminName = $config['adminName'];
    if (isset($config['adminFamilyName'])) $adminFamilyName = $config['adminFamilyName'];
    if (isset($config['adminNick'])) $adminNick = $config['adminNick'];
    if (isset($config['adminEmail'])) $adminEmail = $config['adminEmail'];
    if (isset($config['adminPassword'])) $adminPassword = $config['adminPassword'];
    if (isset($config['packagesToInstall'])) $packagesToInstall = $config['packagesToInstall'];
    if (isset($config['appsToInstall'])) $appsToInstall = $config['appsToInstall'];
    if (isset($config['externalAppsRepositories'])) $externalAppsRepositories = $config['externalAppsRepositories'];
    if (isset($config['premiumRepoFolder'])) $premiumRepoFolder = $config['premiumRepoFolder'];

    if (isset($config['smtpHost'])) $smtpHost = $config['smtpHost'];
    if (isset($config['smtpPort'])) $smtpPort = $config['smtpPort'];
    if (isset($config['smtpEncryption'])) $smtpEncryption = $config['smtpEncryption'];
    if (isset($config['smtpLogin'])) $smtpLogin = $config['smtpLogin'];
    if (isset($config['smtpPassword'])) $smtpPassword = $config['smtpPassword'];

    $rewriteBases = [];
    $lastRewriteBase = '';

    $paths = explode('/', str_replace('\\', '/', (string) realpath(__DIR__ . '/../../..')));
    array_pop($paths);
    foreach (array_reverse($paths) as $tmpFolder) {
      $rewriteBases[] = $lastRewriteBase . '/';
      $lastRewriteBase = '/' . $tmpFolder . $lastRewriteBase;
    }

    if ($rewriteBase === null) $rewriteBase = $this->cli->choose($rewriteBases, 'ConfigEnv.rewriteBase', '/');
    if ($rootFolder === null) $rootFolder = realpath(__DIR__ . '/../../../..');
    if ($rootUrl === null) $rootUrl = $this->cli->read('ConfigEnv.rootUrl', 'http://localhost/' . trim((string) $rewriteBase, '/'));
    if ($srcFolder === null) $srcFolder = realpath(__DIR__ . '/../../..');
    if ($srcUrl === null) $srcUrl = $this->cli->read('ConfigEnv.srcUrl', 'http://localhost/' . trim((string) $rewriteBase, '/') . '/hbl');
    if ($dbHost === null) $dbHost = $this->cli->read('ConfigEnv.dbHost', 'localhost');
    if ($dbUser === null) $dbUser = $this->cli->read('ConfigEnv.dbUser (user must exist)', 'root');
    if ($dbPassword === null) $dbPassword = $this->cli->read('ConfigEnv.dbPassword');
    if ($dbName === null) $dbName = $this->cli->read('ConfigEnv.dbName (database will be created, if it not exists)', 'my_hubleto');
    if ($dbCodepage === null) $dbCodepage = $this->cli->read('ConfigEnv.dbCodepage', 'utf8mb4');
    if ($accountFullName === null) $accountFullName = $this->cli->read('Account.accountFullName', 'My Company');
    if ($adminName === null) $adminName = $this->cli->read('Account.adminName', 'John');
    if ($adminFamilyName === null) $adminFamilyName = $this->cli->read('Account.adminFamilyName', 'Smith');
    if ($adminNick === null) $adminNick = $this->cli->read('Account.adminNick', 'johny');
    if ($adminEmail === null) $adminEmail = $this->cli->read('Account.adminEmail (will be used also for login)', 'john.smith@example.com');
    if ($adminPassword === null) $adminPassword = $this->cli->read('Account.adminPassword (leave empty to generate random password)');

    if ($this->cli->isLaunchedFromTerminal()) {
      $confirm = '';
      if (isset($config['confirm'])) $confirm = $config['confirm'];
      while ($confirm != 'yes') {
        $confirm = $this->cli->read('Hubleto will be installed now. Type \'yes\' to continue or \'exit\' to cancel');
        if ($confirm == 'exit') exit;
      }
    }

//    if ($smtpHost === null) $smtpHost = $this->cli->read('ConfigEnv.smtpHost');
//    if ($smtpHost != null && $smtpPort === null) $smtpPort = $this->cli->read('ConfigEnv.smtpPort');
//    if ($smtpHost != null && $smtpEncryption === null) $smtpEncryption = $this->cli->choose(['ssl', 'tls'], 'ConfigEnv.smtpEncryption', 'ssl');
//    if ($smtpHost != null && $smtpLogin === null) $smtpLogin = $this->cli->read('ConfigEnv.smtpLogin');
//    if ($smtpHost != null && $smtpPassword === null) $smtpPassword = $this->cli->read('ConfigEnv.smtpPassword');

    $errors = [];
    $errorColumns = [];
    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
      $errorColumns[] = 'adminEmail';
      $errors[] = 'Invalid admin email.';
    }
    if (!filter_var($rootUrl, FILTER_VALIDATE_URL)) {
      $errorColumns[] = 'rootUrl';
      $errors[] = 'Invalid account url.';
    }
    if (!filter_var($srcUrl, FILTER_VALIDATE_URL)) {
      $errorColumns[] = 'srcUrl';
      $errors[] = 'Invalid main url.';
    }

    if (empty($packagesToInstall)) $packagesToInstall = 'core,sales';
    if (empty($adminPassword) && !isset($smtpHost)) $adminPassword = \ADIOS\Core\Helper::randomPassword();

    $this->cli->green("  ###         ###         ###   \n");
    $this->cli->green("  ###         ###         ###   \n");
    $this->cli->green("  ### #####   ### #####   ###   \n");
    $this->cli->green("  ##########  ##########  ###   \n");
    $this->cli->green("  ###    ###  ###     ### ###   \n");
    $this->cli->green("  ###    ###  ###     ### ###   \n");
    $this->cli->green("  ###    ###  ##### ####  ####  \n");
    $this->cli->green("  ###    ###  ### #####    ###  \n");
    $this->cli->cyan("\n");
    $this->cli->green("Hubleto, Business Application Hub & opensource CRM/ERP\n");
    $this->cli->cyan("\n");

    if (sizeof($errors) > 0) {
      $this->cli->red("Some fields contain incorrect values: " . join(" ", $errorColumns) . "\n");
      $this->cli->red(join("\n", $errors));
      $this->cli->white("\n");
      throw new \ErrorException("Some fields contain incorrect values: " . join(" ", $errorColumns) . "\n");
    }

    $this->cli->cyan("Initializing with following config:\n");
    $this->cli->cyan('  -> rewriteBase = ' . (string) $rewriteBase . "\n");
    $this->cli->cyan('  -> rootFolder = ' . (string) $rootFolder . "\n");
    $this->cli->cyan('  -> rootUrl = ' . (string) $rootUrl . "\n");
    $this->cli->cyan('  -> dbHost = ' . (string) $dbHost . "\n");
    $this->cli->cyan('  -> dbUser = ' . (string) $dbUser . "\n");
    $this->cli->cyan('  -> dbPassword = ***' . "\n");
    $this->cli->cyan('  -> dbName = ' . (string) $dbName . "\n");
    $this->cli->cyan('  -> dbCodepage = ' . (string) $dbCodepage . "\n");
    $this->cli->cyan('  -> accountFullName = ' . (string) $accountFullName . "\n");
    $this->cli->cyan('  -> adminName = ' . (string) $adminName . "\n");
    $this->cli->cyan('  -> adminFamilyName = ' . (string) $adminFamilyName . "\n");
    $this->cli->cyan('  -> adminNick = ' . (string) $adminNick . "\n");
    $this->cli->cyan('  -> adminEmail = ' . (string) $adminEmail . "\n");
    $this->cli->cyan('  -> adminPassword = ' . (string) $adminPassword . "\n");
    $this->cli->cyan('  -> packagesToInstall = ' . (string) $packagesToInstall . "\n");

    $this->main->config->set('srcFolder', $srcFolder);
    $this->main->config->set('url', $srcUrl);
    $this->main->config->set('rootFolder', $rootFolder);
    $this->main->config->set('rootUrl', $rootUrl);

    $this->main->config->set('db_host', $dbHost);
    $this->main->config->set('db_user', $dbUser);
    $this->main->config->set('db_password', $dbPassword);
    $this->main->config->set('db_name', $dbName);

    $this->main->apps->setCli($this->cli);

    $this->cli->cyan("\n");
    $this->cli->cyan("Hurray. Installing your Hubleto packages: " . join(", ", explode(",", (string) $packagesToInstall)) . "\n");

    // install
    $installer = new \HubletoMain\Installer\Installer(
      $this->main,
      'local-env',
      trim(\ADIOS\Core\Helper::str2url((string) $rewriteBase), '/-'), // uid
      (string) $accountFullName,
      (string) $adminName,
      (string) $adminFamilyName,
      (string) $adminNick,
      (string) $adminEmail,
      (string) $adminPassword,
      (string) $rewriteBase,
      (string) $rootFolder,
      (string) $rootUrl,
      (string) $srcFolder,
      (string) $srcUrl,
      (string) $dbHost,
      (string) $dbName,
      (string) $dbUser,
      (string) $dbPassword,
      (string) $smtpHost,
      (string) $smtpPort,
      (string) $smtpEncryption,
      (string) $smtpLogin,
      (string) $smtpPassword,
      false, // randomize (deprecated)
    );

    $installer->appsToInstall = [];
    foreach (explode(',', (string) $packagesToInstall) as $package) {
      $package = trim((string) $package);

      /** @var array<string, array<string, mixed>> */
      $appsInPackage = (is_array($installer->packages[$package] ?? null) ? $installer->packages[$package] : []);

      $installer->appsToInstall = array_merge(
        $installer->appsToInstall,
        $appsInPackage
      );
    }

    if (is_array($appsToInstall)) {
      foreach ($appsToInstall as $appToInstall => $appConfig) {
        if (!isset($installer->appsToInstall[$appToInstall])) {
          if (!is_array($appConfig)) $appConfig = [];
          $installer->appsToInstall[$appToInstall] = $appConfig;
        }
      }
    }

    $installer->premiumRepoFolder = (string) ($premiumRepoFolder ?? '');
    $installer->externalAppsRepositories = $externalAppsRepositories;
    
    if (isset($config['extraConfigEnv'])) {
      $installer->extraConfigEnv = $config['extraConfigEnv'];
    }

    $this->cli->cyan("  -> Creating folders and files.\n");
    $installer->createFoldersAndFiles();

    $this->cli->cyan("  -> Creating database.\n");
    $installer->createDatabase();

    if ($smtpHost != '') {
      $this->cli->cyan("  -> Initializing SMTP.\n");
      $installer->initSmtp();
    }

    $this->cli->cyan("  -> Creating base tables.\n");
    $installer->installBaseModels();

    $this->cli->cyan("  -> Installing apps, round #1.\n");
    $installer->installApps(1);

    $this->cli->cyan("  -> Installing apps, round #2.\n");
    $installer->installApps(2);

    $this->cli->cyan("  -> Installing apps, round #3.\n");
    $installer->installApps(3);

    $this->cli->cyan("  -> Adding default company and admin user.\n");
    $installer->addCompanyAndAdminUser();

    $this->cli->cyan("\n");
    $this->cli->cyan("All done! You're a fantastic CRM developer. Now you can:\n");
    $this->cli->cyan("  -> Open " . (string) $rootUrl . "?user={$adminEmail}\n");
    $this->cli->cyan("     and use this password: " . (string) $adminPassword . "\n");
    $this->cli->cyan("  -> Note for NGINX users: don't forget to configure your locations in nginx.conf.\n");
    $this->cli->cyan("  -> Check the developer's guide at https://developer.hubleto.com.\n");
    $this->cli->cyan("\n");
    $this->cli->yellow("💡 TIP: Run command below to create your new app 'MyFirstApp'.\n");
    $this->cli->colored("cyan", "black", "Run: php hubleto app create HubletoApp\\Custom\\MyFirstApp");
  }
}