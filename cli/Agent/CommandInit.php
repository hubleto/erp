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

    if (isset($config['rewriteBase'])) {
      $rewriteBase = $config['rewriteBase'];
    }
    if (isset($config['rootFolder'])) {
      $rootFolder = $config['rootFolder'];
    }
    if (isset($config['rootUrl'])) {
      $rootUrl = $config['rootUrl'];
    }
    if (isset($config['srcFolder'])) {
      $srcFolder = $config['srcFolder'];
    }
    if (isset($config['srcUrl'])) {
      $srcUrl = $config['srcUrl'];
    }
    if (isset($config['dbHost'])) {
      $dbHost = $config['dbHost'];
    }
    if (isset($config['dbUser'])) {
      $dbUser = $config['dbUser'];
    }
    if (isset($config['dbPassword'])) {
      $dbPassword = $config['dbPassword'];
    }
    if (isset($config['dbName'])) {
      $dbName = $config['dbName'];
    }
    if (isset($config['dbCodepage'])) {
      $dbCodepage = $config['dbCodepage'];
    }
    if (isset($config['accountFullName'])) {
      $accountFullName = $config['accountFullName'];
    }
    if (isset($config['adminName'])) {
      $adminName = $config['adminName'];
    }
    if (isset($config['adminFamilyName'])) {
      $adminFamilyName = $config['adminFamilyName'];
    }
    if (isset($config['adminNick'])) {
      $adminNick = $config['adminNick'];
    }
    if (isset($config['adminEmail'])) {
      $adminEmail = $config['adminEmail'];
    }
    if (isset($config['adminPassword'])) {
      $adminPassword = $config['adminPassword'];
    }
    if (isset($config['packagesToInstall'])) {
      $packagesToInstall = $config['packagesToInstall'];
    }
    if (isset($config['appsToInstall'])) {
      $appsToInstall = $config['appsToInstall'];
    }
    if (isset($config['externalAppsRepositories'])) {
      $externalAppsRepositories = $config['externalAppsRepositories'];
    }
    if (isset($config['premiumRepoFolder'])) {
      $premiumRepoFolder = $config['premiumRepoFolder'];
    }

    if (isset($config['smtpHost'])) {
      $smtpHost = $config['smtpHost'];
    }
    if (isset($config['smtpPort'])) {
      $smtpPort = $config['smtpPort'];
    }
    if (isset($config['smtpEncryption'])) {
      $smtpEncryption = $config['smtpEncryption'];
    }
    if (isset($config['smtpLogin'])) {
      $smtpLogin = $config['smtpLogin'];
    }
    if (isset($config['smtpPassword'])) {
      $smtpPassword = $config['smtpPassword'];
    }

    $rewriteBases = [];
    $lastRewriteBase = '';

    $paths = explode('/', str_replace('\\', '/', $this->main->config->getAsString('rootFolder')));
    foreach (array_reverse($paths) as $tmpFolder) {
      $rewriteBases[] = $lastRewriteBase . '/';
      $lastRewriteBase = '/' . $tmpFolder . $lastRewriteBase;
    }

    if ($rewriteBase === null) {
      $rewriteBase = \Hubleto\Terminal::choose($rewriteBases, 'ConfigEnv.rewriteBase', '/');
    }
    if ($rootFolder === null) {
      $rootFolder = $this->main->config->getAsString('rootFolder');
    }
    if ($rootUrl === null) {
      $rootUrl = \Hubleto\Terminal::read('ConfigEnv.rootUrl', 'http://localhost/' . trim((string) $rewriteBase, '/'));
    }
    if ($srcFolder === null) {
      $srcFolder = realpath(__DIR__ . '/../../..');
    }
    if ($srcUrl === null) {
      $srcUrl = \Hubleto\Terminal::read('ConfigEnv.srcUrl', 'http://localhost/' . trim((string) $rewriteBase, '/') . '/vendor/hubleto/main');
    }
    if ($dbHost === null) {
      $dbHost = \Hubleto\Terminal::read('ConfigEnv.dbHost', 'localhost');
    }
    if ($dbUser === null) {
      $dbUser = \Hubleto\Terminal::read('ConfigEnv.dbUser (user must exist)', 'root');
    }
    if ($dbPassword === null) {
      $dbPassword = \Hubleto\Terminal::read('ConfigEnv.dbPassword');
    }
    if ($dbName === null) {
      $dbName = \Hubleto\Terminal::read('ConfigEnv.dbName (database will be created, if it not exists)', 'my_hubleto');
    }
    if ($dbCodepage === null) {
      $dbCodepage = \Hubleto\Terminal::read('ConfigEnv.dbCodepage', 'utf8mb4');
    }
    if ($accountFullName === null) {
      $accountFullName = \Hubleto\Terminal::read('Account.accountFullName', 'My Company');
    }
    if ($adminName === null) {
      $adminName = \Hubleto\Terminal::read('Account.adminName', 'John');
    }
    if ($adminFamilyName === null) {
      $adminFamilyName = \Hubleto\Terminal::read('Account.adminFamilyName', 'Smith');
    }
    if ($adminNick === null) {
      $adminNick = \Hubleto\Terminal::read('Account.adminNick', 'johny');
    }
    if ($adminEmail === null) {
      $adminEmail = \Hubleto\Terminal::read('Account.adminEmail (will be used also for login)', 'john.smith@example.com');
    }
    if ($adminPassword === null) {
      $adminPassword = \Hubleto\Terminal::read('Account.adminPassword (leave empty to generate random password)');
    }

    if (\Hubleto\Terminal::isLaunchedFromTerminal()) {
      $confirm = '';
      if (isset($config['confirm'])) {
        $confirm = $config['confirm'];
      }
      while ($confirm != 'yes') {
        $confirm = \Hubleto\Terminal::read('Hubleto will be installed now. Type \'yes\' to continue or \'exit\' to cancel');
        if ($confirm == 'exit') {
          exit;
        }
      }
    }

    //    if ($smtpHost === null) $smtpHost = \Hubleto\Terminal::read('ConfigEnv.smtpHost');
    //    if ($smtpHost != null && $smtpPort === null) $smtpPort = \Hubleto\Terminal::read('ConfigEnv.smtpPort');
    //    if ($smtpHost != null && $smtpEncryption === null) $smtpEncryption = \Hubleto\Terminal::choose(['ssl', 'tls'], 'ConfigEnv.smtpEncryption', 'ssl');
    //    if ($smtpHost != null && $smtpLogin === null) $smtpLogin = \Hubleto\Terminal::read('ConfigEnv.smtpLogin');
    //    if ($smtpHost != null && $smtpPassword === null) $smtpPassword = \Hubleto\Terminal::read('ConfigEnv.smtpPassword');

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

    if (empty($packagesToInstall)) {
      $packagesToInstall = 'core,sales';
    }
    if (empty($adminPassword) && !isset($smtpHost)) {
      $adminPassword = \Hubleto\Framework\Helper::randomPassword();
    }

    \Hubleto\Terminal::green("  ###         ###         ###   \n");
    \Hubleto\Terminal::green("  ###         ###         ###   \n");
    \Hubleto\Terminal::green("  ### #####   ### #####   ###   \n");
    \Hubleto\Terminal::green("  ##########  ##########  ###   \n");
    \Hubleto\Terminal::green("  ###    ###  ###     ### ###   \n");
    \Hubleto\Terminal::green("  ###    ###  ###     ### ###   \n");
    \Hubleto\Terminal::green("  ###    ###  ##### ####  ####  \n");
    \Hubleto\Terminal::green("  ###    ###  ### #####    ###  \n");
    \Hubleto\Terminal::cyan("\n");
    \Hubleto\Terminal::green("Hubleto, Business Application Hub & opensource CRM/ERP\n");
    \Hubleto\Terminal::cyan("\n");

    if (sizeof($errors) > 0) {
      \Hubleto\Terminal::red("Some fields contain incorrect values: " . join(" ", $errorColumns) . "\n");
      \Hubleto\Terminal::red(join("\n", $errors));
      \Hubleto\Terminal::white("\n");
      throw new \ErrorException("Some fields contain incorrect values: " . join(" ", $errorColumns) . "\n");
    }

    \Hubleto\Terminal::cyan("Initializing with following config:\n");
    \Hubleto\Terminal::cyan('  -> rewriteBase = ' . (string) $rewriteBase . "\n");
    \Hubleto\Terminal::cyan('  -> rootFolder = ' . (string) $rootFolder . "\n");
    \Hubleto\Terminal::cyan('  -> rootUrl = ' . (string) $rootUrl . "\n");
    \Hubleto\Terminal::cyan('  -> dbHost = ' . (string) $dbHost . "\n");
    \Hubleto\Terminal::cyan('  -> dbUser = ' . (string) $dbUser . "\n");
    \Hubleto\Terminal::cyan('  -> dbPassword = ***' . "\n");
    \Hubleto\Terminal::cyan('  -> dbName = ' . (string) $dbName . "\n");
    \Hubleto\Terminal::cyan('  -> dbCodepage = ' . (string) $dbCodepage . "\n");
    \Hubleto\Terminal::cyan('  -> accountFullName = ' . (string) $accountFullName . "\n");
    \Hubleto\Terminal::cyan('  -> adminName = ' . (string) $adminName . "\n");
    \Hubleto\Terminal::cyan('  -> adminFamilyName = ' . (string) $adminFamilyName . "\n");
    \Hubleto\Terminal::cyan('  -> adminNick = ' . (string) $adminNick . "\n");
    \Hubleto\Terminal::cyan('  -> adminEmail = ' . (string) $adminEmail . "\n");
    \Hubleto\Terminal::cyan('  -> adminPassword = ' . (string) $adminPassword . "\n");
    \Hubleto\Terminal::cyan('  -> packagesToInstall = ' . (string) $packagesToInstall . "\n");

    $this->main->config->set('srcFolder', $srcFolder);
    $this->main->config->set('url', $srcUrl);
    $this->main->config->set('rootFolder', $rootFolder);
    $this->main->config->set('rootUrl', $rootUrl);

    $this->main->config->set('db_host', $dbHost);
    $this->main->config->set('db_user', $dbUser);
    $this->main->config->set('db_password', $dbPassword);
    $this->main->config->set('db_name', $dbName);

    \Hubleto\Terminal::cyan("\n");
    \Hubleto\Terminal::cyan("Hurray. Installing your Hubleto packages: " . join(", ", explode(",", (string) $packagesToInstall)) . "\n");

    // install
    $installer = new \HubletoMain\Installer\Installer(
      $this->main,
      'local-env',
      trim(\Hubleto\Framework\Helper::str2url((string) $rewriteBase), '/-'), // uid
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
          if (!is_array($appConfig)) {
            $appConfig = [];
          }
          $installer->appsToInstall[$appToInstall] = $appConfig;
        }
      }
    }

    $installer->premiumRepoFolder = (string) ($premiumRepoFolder ?? '');
    $installer->externalAppsRepositories = $externalAppsRepositories;

    if (isset($config['extraConfigEnv'])) {
      $installer->extraConfigEnv = $config['extraConfigEnv'];
    }

    \Hubleto\Terminal::cyan("  -> Creating folders and files.\n");
    $installer->createFoldersAndFiles();

    \Hubleto\Terminal::cyan("  -> Creating database.\n");
    $installer->createDatabase();

    if ($smtpHost != '') {
      \Hubleto\Terminal::cyan("  -> Initializing SMTP.\n");
      $installer->initSmtp();
    }

    \Hubleto\Terminal::cyan("  -> Creating base tables.\n");
    $installer->installBaseModels();

    \Hubleto\Terminal::cyan("  -> Installing apps, round #1.\n");
    $installer->installApps(1);

    \Hubleto\Terminal::cyan("  -> Installing apps, round #2.\n");
    $installer->installApps(2);

    \Hubleto\Terminal::cyan("  -> Installing apps, round #3.\n");
    $installer->installApps(3);

    \Hubleto\Terminal::cyan("  -> Adding default company and admin user.\n");
    $installer->addCompanyAndAdminUser();

    \Hubleto\Terminal::cyan("\n");
    \Hubleto\Terminal::cyan("All done! You're a fantastic CRM developer.\n");
    \Hubleto\Terminal::colored("cyan", "black", "Now open " . (string) $rootUrl . "?user={$adminEmail} and use this password: " . (string) $adminPassword);
    \Hubleto\Terminal::cyan("  -> Note for NGINX users: don't forget to configure your locations in nginx.conf.\n");
    \Hubleto\Terminal::cyan("  -> Check the developer's guide at https://developer.hubleto.com.\n");
    \Hubleto\Terminal::cyan("\n");
    \Hubleto\Terminal::cyan("💡 TIP: Run command below to create your new app 'MyFirstApp'.\n");
    \Hubleto\Terminal::cyan("Run: php hubleto app create HubletoApp\\Custom\\MyFirstApp");
  }
}
