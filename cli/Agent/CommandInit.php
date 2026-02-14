<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent;

class CommandInit extends \Hubleto\Erp\Cli\Agent\Command
{
  public array $initConfig = [];

  public array $packages = [
    'core' => [
      \Hubleto\App\Community\Auth\Loader::class => ['sidebarOrder' => 0],
      \Hubleto\App\Community\Settings\Loader::class => [ 'sidebarOrder' => 99997 ],
      \Hubleto\App\Community\Crypto\Loader::class => [ ],
      \Hubleto\App\Community\Desktop\Loader::class => [ 'sidebarOrder' => 0 ],
      \Hubleto\App\Community\Usage\Loader::class => [ 'sidebarOrder' => 0 ],
      \Hubleto\App\Community\Mail\Loader::class => [ 'sidebarOrder' => 125 ],
      \Hubleto\App\Community\Notifications\Loader::class => [ 'sidebarOrder' => 125 ],
      \Hubleto\App\Community\Documents\Loader::class => [ 'sidebarOrder' => 120 ],
      \Hubleto\App\Community\Customers\Loader::class => [ 'sidebarOrder' => 101 ],
      \Hubleto\App\Community\Contacts\Loader::class => [ 'sidebarOrder' => 102 ],
      \Hubleto\App\Community\Calendar\Loader::class => [ 'sidebarOrder' => 110 ],
      \Hubleto\App\Community\Dashboards\Loader::class => [ 'sidebarOrder' => 99995 ],
      \Hubleto\App\Community\Workflow\Loader::class => [ 'sidebarOrder' => 220 ],
      \Hubleto\App\Community\Tasks\Loader::class => [ 'sidebarOrder' => 195 ],
      \Hubleto\App\Community\Worksheets\Loader::class => [ 'sidebarOrder' => 200 ],
//      \Hubleto\App\Community\Reports\Loader::class => [ 'sidebarOrder' => 99996 ],
      \Hubleto\App\Community\Help\Loader::class => [ 'sidebarOrder' => 99998 ],
      \Hubleto\App\Community\About\Loader::class => [ 'sidebarOrder' => 99998 ],
    ],
    // 'cloud' => [
    //   \Hubleto\App\Community\Cloud\Loader::class => [ 'sidebarOrder' => 99998 ],
    // ],
    'crm' => [
      \Hubleto\App\Community\Campaigns\Loader::class => [ 'sidebarOrder' => 202 ],
      \Hubleto\App\Community\Suppliers\Loader::class => [ 'sidebarOrder' => 200 ],
      \Hubleto\App\Community\Products\Loader::class => [ 'sidebarOrder' => 200 ],
      \Hubleto\App\Community\Leads\Loader::class => [ 'sidebarOrder' => 210 ],
    ],
    'marketing' => [
      \Hubleto\App\Community\Campaigns\Loader::class => [ 'sidebarOrder' => 202 ],
      \Hubleto\App\Community\Leads\Loader::class => [ 'sidebarOrder' => 200 ],
    ],
    'sales' => [
      \Hubleto\App\Community\Suppliers\Loader::class => [ 'sidebarOrder' => 200 ],
      \Hubleto\App\Community\Products\Loader::class => [ 'sidebarOrder' => 200 ],
      \Hubleto\App\Community\Campaigns\Loader::class => [ 'sidebarOrder' => 202 ],
      \Hubleto\App\Community\Leads\Loader::class => [ 'sidebarOrder' => 210 ],
      \Hubleto\App\Community\Deals\Loader::class => [ 'sidebarOrder' => 210 ],
      \Hubleto\App\Community\Invoices\Loader::class => [ 'sidebarOrder' => 410 ],
      \Hubleto\App\Community\Orders\Loader::class => [ 'sidebarOrder' => 230 ],
    ],
    'projects' => [
      \Hubleto\App\Community\Projects\Loader::class => [ 'sidebarOrder' => 230 ],
      \Hubleto\App\Community\Worksheets\Loader::class => [ 'sidebarOrder' => 200 ],
      \Hubleto\App\Community\Issues\Loader::class => [ 'sidebarOrder' => 240 ],
    ],
    'retail' => [
      \Hubleto\App\Community\Suppliers\Loader::class => [ 'sidebarOrder' => 200 ],
      \Hubleto\App\Community\Products\Loader::class => [ 'sidebarOrder' => 200 ],
      \Hubleto\App\Community\Shops\Loader::class => [ 'sidebarOrder' => 200 ],
      \Hubleto\App\Community\Warehouses\Loader::class => [ 'sidebarOrder' => 210 ],
    ],
    'e-commerce' => [
      \Hubleto\App\Community\Suppliers\Loader::class => [ 'sidebarOrder' => 200 ],
      \Hubleto\App\Community\Products\Loader::class => [ 'sidebarOrder' => 310 ],
      \Hubleto\App\Community\Orders\Loader::class => [ 'sidebarOrder' => 320 ],
      \Hubleto\App\Community\Shops\Loader::class => [ 'sidebarOrder' => 410 ],
    ],
    'finance' => [
      // \Hubleto\App\Community\Billing\Loader::class => [ 'sidebarOrder' => 400 ],
      \Hubleto\App\Community\Invoices\Loader::class => [ 'sidebarOrder' => 410 ],
      \Hubleto\App\Community\Cashdesk\Loader::class => [ 'sidebarOrder' => 410 ],
    ],
    'developer' => [
      \Hubleto\App\Community\Developer\Loader::class => [ 'sidebarOrder' => 410 ],
      \Hubleto\App\Community\Tools\Loader::class => [ 'sidebarOrder' => 99997 ],
      \Hubleto\App\Community\Api\Loader::class => [ 'sidebarOrder' => 99998 ],
    ],
  ];

  public function parseConfigFile(string $configFile, array $extraConfigsInCommandLine = []): array
  {
    $configStr = (string) file_get_contents($configFile);
    $config = (array) (\Symfony\Component\Yaml\Yaml::parse($configStr) ?? []);

    if (count($extraConfigsInCommandLine)) {
      $extraConfig = (array) (\Symfony\Component\Yaml\Yaml::parse(join('\n', $extraConfigsInCommandLine)) ?? []);
      $config = array_merge($config, $extraConfig);
    }

    return $config;
  }

  public function run(): void
  {

    if (is_file($this->env()->projectFolder . '/ConfigEnv.php')) {
      $this->terminal()->red("ConfigEnv.php already exists, project has already been initialized.\n");
      $this->terminal()->red("If you want to re-initialize the project, delte ConfigEnv.php file first.\n");
      exit;
    }

    $rewriteBase = null;
    $projectFolder = null;
    $releaseFolder = null;
    $secureFolder = null;
    $projectUrl = null;
    $assetsUrl = null;
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
    $generateDemoData = null;
    $noPrompt = null;
    $packagesToInstall = null;
    $appsToInstall = null;
    $defaultConfiguration = null;
    $externalAppsRepositories = [];
    $enterpriseAppsRepository = null;

    $configFile = '';
    $extraConfigsInCommandLine = [];
    for ($i = 2; $i < count($this->arguments); $i++) {
      $arg = trim((string) $this->arguments[$i]);
      if (empty($arg)) continue;

      if (str_ends_with($arg, '.yaml') || str_ends_with($arg, '.yml')
      ) {
        $configFile = $arg;
      } else {
        $extraConfigsInCommandLine[] = $arg;
      }
    }

    if (!empty($configFile) && is_file($configFile)) {
      $config = $this->parseConfigFile($configFile, $extraConfigsInCommandLine);
    } else {
      $config = $this->initConfig;
    }

    if (isset($config['rewriteBase'])) {
      $rewriteBase = $config['rewriteBase'];
    }
    if (isset($config['projectFolder'])) {
      $projectFolder = $config['projectFolder'];
    }
    if (isset($config['releaseFolder'])) {
      $releaseFolder = $config['releaseFolder'];
    }
    if (isset($config['secureFolder'])) {
      $secureFolder = $config['secureFolder'];
    }
    if (isset($config['projectUrl'])) {
      $projectUrl = $config['projectUrl'];
    }
    if (isset($config['assetsUrl'])) {
      $assetsUrl = $config['assetsUrl'];
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
    if (isset($config['generateDemoData'])) {
      $generateDemoData = $config['generateDemoData'];
    }
    if (isset($config['noPrompt'])) {
      $noPrompt = $config['noPrompt'];
    }
    if (isset($config['packagesToInstall'])) {
      $packagesToInstall = $config['packagesToInstall'];
    }
    if (isset($config['appsToInstall'])) {
      $appsToInstall = $config['appsToInstall'];
    }
    if (isset($config['defaultConfiguration'])) {
      $defaultConfiguration = $config['defaultConfiguration'];
    }
    if (isset($config['externalAppsRepositories'])) {
      $externalAppsRepositories = $config['externalAppsRepositories'];
    }
    if (isset($config['enterpriseAppsRepository'])) {
      $enterpriseAppsRepository = $config['enterpriseAppsRepository'];
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

    $paths = explode('/', str_replace('\\', '/', $this->env()->projectFolder));
    foreach (array_reverse($paths) as $tmpFolder) {
      $rewriteBases[] = $lastRewriteBase . '/';
      $lastRewriteBase = '/' . $tmpFolder . $lastRewriteBase;
    }

    $this->terminal()->cyan("For more information about the parameters check https://developer.hubleto.com/v0/cli/init\n");

    if ($rewriteBase === null) {
      $rewriteBase = $this->terminal()->choose($rewriteBases, 'ConfigEnv.rewriteBase', '/');
    }
    if ($projectFolder === null) {
      $projectFolder = $this->env()->projectFolder;
    }
    if ($releaseFolder === null) {
      $releaseFolder = $this->env()->releaseFolder;
    }
    if ($secureFolder === null) {
      $secureFolder = $this->env()->secureFolder;
    }
    if ($projectUrl === null) {
      $projectUrl = $this->terminal()->read('ConfigEnv.projectUrl', 'http://localhost/' . trim((string) $rewriteBase, '/'));
    }
    if ($assetsUrl === null) {
      $assetsUrl = $this->terminal()->read('ConfigEnv.assetsUrl', 'http://localhost/' . trim((string) $rewriteBase, '/') . '/vendor/hubleto/assets');
    }
    if ($dbHost === null) {
      $dbHost = $this->terminal()->read('ConfigEnv.dbHost', 'localhost');
    }
    if ($dbUser === null) {
      $dbUser = $this->terminal()->read('ConfigEnv.dbUser (user must exist)', 'root');
    }
    if ($dbPassword === null) {
      $dbPassword = $this->terminal()->read('ConfigEnv.dbPassword');
    }
    if ($dbName === null) {
      $dbName = $this->terminal()->read('ConfigEnv.dbName (database will be created, if it not exists)', 'my_hubleto');
    }
    if ($dbCodepage === null) {
      $dbCodepage = $this->terminal()->read('ConfigEnv.dbCodepage', 'utf8mb4');
    }
    if ($accountFullName === null) {
      $accountFullName = $this->terminal()->read('Account.accountFullName', 'My Company');
    }
    if ($adminName === null) {
      $adminName = $this->terminal()->read('Account.adminName', 'John');
    }
    if ($adminFamilyName === null) {
      $adminFamilyName = $this->terminal()->read('Account.adminFamilyName', 'Smith');
    }
    if ($adminNick === null) {
      $adminNick = $this->terminal()->read('Account.adminNick', 'johny');
    }
    if ($adminEmail === null) {
      $adminEmail = $this->terminal()->read('Account.adminEmail (will be used also for login)', 'john.smith@example.com');
    }
    if ($adminPassword === null) {
      $adminPassword = $this->terminal()->read('Account.adminPassword (leave empty to generate random password)');
    }
    if ($generateDemoData === null) {
      $confirm = '';
      while (!in_array($confirm, ['yes', 'no'])) {
        $confirm = $this->terminal()->read('Account.generateDemoData (type \'yes\' or \'no\')');
      }
      $generateDemoData = $confirm == 'yes';
    }

    if ($this->terminal()->isLaunchedFromTerminal() && $noPrompt !== true) {
      $confirm = '';
      if (isset($config['confirm'])) {
        $confirm = $config['confirm'];
      }
      while ($confirm != 'yes') {
        $confirm = $this->terminal()->read('Hubleto will be installed now. Type \'yes\' to continue or \'exit\' to cancel');
        if ($confirm == 'exit') {
          exit;
        }
      }
    }

    $projectUrl = str_replace('{{ rewriteBase }}', trim($rewriteBase, '/'), $projectUrl);
    $assetsUrl = str_replace('{{ rewriteBase }}', trim($rewriteBase, '/'), $assetsUrl);

    $errors = [];
    $errorColumns = [];
    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
      $errorColumns[] = 'adminEmail';
      $errors[] = 'Invalid admin email.';
    }
    if (!filter_var($projectUrl, FILTER_VALIDATE_URL)) {
      $errorColumns[] = 'projectUrl';
      $errors[] = 'Invalid project URL.';
    }

    if (empty($packagesToInstall)) {
      $packagesToInstall = join(', ', array_keys($this->packages));
    }
    if (empty($adminPassword) && !isset($smtpHost)) {
      $adminPassword = \Hubleto\Framework\Helper::randomPassword();
    }

    $this->terminal()->green("  ###         ###         ###   \n");
    $this->terminal()->green("  ###         ###         ###   \n");
    $this->terminal()->green("  ### #####   ### #####   ###   \n");
    $this->terminal()->green("  ##########  ##########  ###   \n");
    $this->terminal()->green("  ###    ###  ###     ### ###   \n");
    $this->terminal()->green("  ###    ###  ###     ### ###   \n");
    $this->terminal()->green("  ###    ###  ##### ####  ####  \n");
    $this->terminal()->green("  ###    ###  ### #####    ###  \n");
    $this->terminal()->cyan("\n");
    $this->terminal()->green("Hubleto, Business Application Hub & opensource CRM/ERP\n");
    $this->terminal()->cyan("\n");

    if (sizeof($errors) > 0) {
      $this->terminal()->error("Some fields contain incorrect values: " . join(" ", $errorColumns) . "\n");
      $this->terminal()->error(join("\n", $errors));
      $this->terminal()->white("\n");
      throw new \ErrorException("Some fields contain incorrect values: " . join(" ", $errorColumns) . "\n");
    }

    $this->terminal()->cyan("Initializing with following config:\n");
    $this->terminal()->cyan('  -> rewriteBase = ' . (string) $rewriteBase . "\n");
    $this->terminal()->cyan('  -> projectFolder = ' . (string) $projectFolder . "\n");
    $this->terminal()->cyan('  -> releaseFolder = ' . (string) $releaseFolder . "\n");
    $this->terminal()->cyan('  -> projectUrl = ' . (string) $projectUrl . "\n");
    $this->terminal()->cyan('  -> secureFolder = ' . (string) $secureFolder . "\n");
    $this->terminal()->cyan('  -> assetsUrl = ' . (string) $assetsUrl . "\n");
    $this->terminal()->cyan('  -> dbHost = ' . (string) $dbHost . "\n");
    $this->terminal()->cyan('  -> dbUser = ' . (string) $dbUser . "\n");
    $this->terminal()->cyan('  -> dbPassword = ***' . "\n");
    $this->terminal()->cyan('  -> dbName = ' . (string) $dbName . "\n");
    $this->terminal()->cyan('  -> dbCodepage = ' . (string) $dbCodepage . "\n");
    $this->terminal()->cyan('  -> accountFullName = ' . (string) $accountFullName . "\n");
    $this->terminal()->cyan('  -> adminName = ' . (string) $adminName . "\n");
    $this->terminal()->cyan('  -> adminFamilyName = ' . (string) $adminFamilyName . "\n");
    $this->terminal()->cyan('  -> adminNick = ' . (string) $adminNick . "\n");
    $this->terminal()->cyan('  -> adminEmail = ' . (string) $adminEmail . "\n");
    $this->terminal()->cyan('  -> adminPassword = ' . (string) $adminPassword . "\n");
    $this->terminal()->cyan('  -> generateDemoData = ' . ($generateDemoData ? 'yes' : 'no') . "\n");
    $this->terminal()->cyan('  -> packagesToInstall = ' . (string) $packagesToInstall . "\n");
    $this->terminal()->cyan('  -> defaultConfiguration = ' . (string) $defaultConfiguration . "\n");

    $this->config()->set('projectFolder', $projectFolder);
    $this->config()->set('releaseFolder', $releaseFolder);
    $this->config()->set('projectUrl', $projectUrl);
    $this->config()->set('secureFolder', $secureFolder);
    $this->config()->set('assetsUrl', $assetsUrl);

    $this->config()->set('db_host', $dbHost);
    $this->config()->set('db_user', $dbUser);
    $this->config()->set('db_password', $dbPassword);
    $this->config()->set('db_name', $dbName);

    $this->env()->projectFolder = $projectFolder;
    $this->env()->releaseFolder = $releaseFolder;
    $this->env()->secureFolder = $secureFolder;

    $this->terminal()->cyan("\n");
    $this->terminal()->cyan("Hurray. Installing your Hubleto.\n");

    // install
    $installer = new \Hubleto\Erp\Installer\Installer(
      'local-env',
      bin2hex(random_bytes(18)), // uid
      (string) $accountFullName,
      (string) $adminName,
      (string) $adminFamilyName,
      (string) $adminNick,
      (string) $adminEmail,
      (string) $adminPassword,
      (string) $rewriteBase,
      (string) $projectFolder,
      (string) $releaseFolder,
      (string) $secureFolder,
      (string) $projectUrl,
      (string) $assetsUrl,
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

    // 'core' is always installed
    $packagesToInstall = array_merge(['core'], explode(',', (string) $packagesToInstall));

    foreach ($packagesToInstall as $package) {
      $package = trim((string) $package);

      /** @var array<string, array<string, mixed>> */
      $appsInPackage = (is_array($this->packages[$package] ?? null) ? $this->packages[$package] : []);

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

    $installer->enterpriseAppsRepository = (string) ($enterpriseAppsRepository ?? '');
    $installer->externalAppsRepositories = $externalAppsRepositories;

    if (isset($config['extraConfigEnv'])) {
      $installer->extraConfigEnv = $config['extraConfigEnv'];
    }

    $this->terminal()->cyan("  -> Creating folders and files.\n");
    $installer->createFoldersAndFiles();

    $this->terminal()->cyan("  -> Creating database.\n");
    $installer->createDatabase();

    if ($smtpHost != '') {
      $this->terminal()->cyan("  -> Initializing SMTP.\n");
      $installer->initSmtp();
    }

    $this->terminal()->cyan("  -> Creating base tables.\n");
    $installer->installBaseModels();

    $this->terminal()->cyan(
      "  -> Installing "
      . count($installer->appsToInstall) . " apps ("
      . join(', ', array_map(function ($item) { return str_replace('\Loader', '', str_replace('Hubleto\App\\', '', $item)); }, array_keys($installer->appsToInstall)))
      . ").\n"
    );

    $this->terminal()->cyan("    -> Round #1.\n");
    $installer->installApps(1);

    $this->terminal()->cyan("    -> Round #2.\n");
    $installer->installApps(2);

    $this->terminal()->cyan("    -> Round #3.\n");
    $installer->installApps(3);

    $this->terminal()->cyan("    -> Finalizing.\n");
    foreach ($installer->appsToInstall as $appNamespace => $appConfig) {
      $app = $this->appManager()->createAppInstance($appNamespace);
      $mClasses = $app->getAvailableModelClasses();
      foreach ($mClasses as $mClass) {
        $mObj = $this->getService($mClass);
        $mObj->createSqlForeignKeys();
      }
    }

    $this->terminal()->cyan("  -> Adding default company and admin user.\n");
    $installer->addCompanyAndAdminUser();

    $this->terminal()->cyan("  -> Reinitializing Hubleto after installation.\n");
    $this->appManager()->init();

    $this->terminal()->cyan("  -> Applying default configuration: {$defaultConfiguration}\n");
    $installer->applyDefaultConfiguration((string) $defaultConfiguration);

    if ($generateDemoData) {
      $this->terminal()->cyan("  -> Generating demo data.\n");
      $this->getService(\Hubleto\Erp\Cli\Agent\Project\GenerateDemoData::class)->run();
    }

    $this->terminal()->cyan("\n");
    $this->terminal()->yellow("All done! Enjoy Hubleto.\n");

    $this->terminal()->colored("yellow", "black", "Now open " . (string) $projectUrl . "?user={$adminEmail} and use this password: " . (string) $adminPassword . "\n");
    $this->terminal()->yellow("  -> Note for NGINX users: don't forget to configure your locations in nginx.conf.\n");
    $this->terminal()->yellow("  -> Check the developer's guide at https://developer.hubleto.com.\n");
    $this->terminal()->yellow("\n");
    $this->terminal()->yellow("💡 TIP: Run command below to create your new app 'MyFirstApp'.\n");
    $this->terminal()->yellow("Run: php hubleto app create MyFirstApp\n");
  }
}
