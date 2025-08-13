<?php declare(strict_types=1);

namespace HubletoMain\Cli\Agent;

class CommandInit extends \HubletoMain\Cli\Agent\Command
{
  public array $initConfig = [];

  public array $packages = [
    'core' => [
      \HubletoApp\Community\Settings\Loader::class => [ 'sidebarOrder' => 99997 ],
      \HubletoApp\Community\Tools\Loader::class => [ 'sidebarOrder' => 99997 ],
      \HubletoApp\Community\Crypto\Loader::class => [ ],
      \HubletoApp\Community\Desktop\Loader::class => [ 'sidebarOrder' => 0 ],
      \HubletoApp\Community\Usage\Loader::class => [ 'sidebarOrder' => 0 ],
      \HubletoApp\Community\Mail\Loader::class => [ 'sidebarOrder' => 125 ],
      \HubletoApp\Community\Notifications\Loader::class => [ 'sidebarOrder' => 125 ],
      \HubletoApp\Community\Documents\Loader::class => [ 'sidebarOrder' => 120 ],
      \HubletoApp\Community\Customers\Loader::class => [ 'sidebarOrder' => 102 ],
      \HubletoApp\Community\Contacts\Loader::class => [ 'sidebarOrder' => 101 ],
      \HubletoApp\Community\Calendar\Loader::class => [ 'sidebarOrder' => 110 ],
      \HubletoApp\Community\Dashboards\Loader::class => [ 'sidebarOrder' => 99995 ],
      \HubletoApp\Community\Pipeline\Loader::class => [ 'sidebarOrder' => 220 ],
      \HubletoApp\Community\Tasks\Loader::class => [ 'sidebarOrder' => 99995 ],
      \HubletoApp\Community\Reports\Loader::class => [ 'sidebarOrder' => 99996 ],
      \HubletoApp\Community\Help\Loader::class => [ 'sidebarOrder' => 99998 ],
      \HubletoApp\Community\About\Loader::class => [ 'sidebarOrder' => 99998 ],
    ],
    'cloud' => [
      \HubletoApp\Community\Cloud\Loader::class => [ 'sidebarOrder' => 99998 ],
    ],
    'crm' => [
      \HubletoApp\Community\Campaigns\Loader::class => [ 'sidebarOrder' => 202 ],
      \HubletoApp\Community\Suppliers\Loader::class => [ 'sidebarOrder' => 200 ],
      \HubletoApp\Community\Products\Loader::class => [ 'sidebarOrder' => 200 ],
      \HubletoApp\Community\Leads\Loader::class => [ 'sidebarOrder' => 210 ],
      \HubletoApp\Community\Mail\Loader::class => [ 'sidebarOrder' => 230 ],
    ],
    'marketing' => [
      \HubletoApp\Community\Campaigns\Loader::class => [ 'sidebarOrder' => 202 ],
      \HubletoApp\Community\Leads\Loader::class => [ 'sidebarOrder' => 200 ],
      \HubletoApp\Community\Mail\Loader::class => [ 'sidebarOrder' => 230 ],
    ],
    'sales' => [
      \HubletoApp\Community\Mail\Loader::class => [ 'sidebarOrder' => 230 ],
      \HubletoApp\Community\Suppliers\Loader::class => [ 'sidebarOrder' => 200 ],
      \HubletoApp\Community\Products\Loader::class => [ 'sidebarOrder' => 200 ],
      \HubletoApp\Community\Campaigns\Loader::class => [ 'sidebarOrder' => 202 ],
      \HubletoApp\Community\Leads\Loader::class => [ 'sidebarOrder' => 210 ],
      \HubletoApp\Community\Deals\Loader::class => [ 'sidebarOrder' => 210 ],
      \HubletoApp\Community\Orders\Loader::class => [ 'sidebarOrder' => 230 ],
    ],
    'projects' => [
      \HubletoApp\Community\Projects\Loader::class => [ 'sidebarOrder' => 230 ],
      \HubletoApp\Community\Worksheets\Loader::class => [ 'sidebarOrder' => 200 ],
      \HubletoApp\Community\Issues\Loader::class => [ 'sidebarOrder' => 230 ],
    ],
    'supply-chain' => [
      \HubletoApp\Community\Suppliers\Loader::class => [ 'sidebarOrder' => 200 ],
      \HubletoApp\Community\Products\Loader::class => [ 'sidebarOrder' => 200 ],
      \HubletoApp\Community\Warehouses\Loader::class => [ 'sidebarOrder' => 210 ],
      \HubletoApp\Community\Inventory\Loader::class => [ 'sidebarOrder' => 220 ],
    ],
    'e-commerce' => [
      \HubletoApp\Community\Suppliers\Loader::class => [ 'sidebarOrder' => 200 ],
      \HubletoApp\Community\Products\Loader::class => [ 'sidebarOrder' => 310 ],
      \HubletoApp\Community\Orders\Loader::class => [ 'sidebarOrder' => 320 ],
    ],
    'finance' => [
      // \HubletoApp\Community\Billing\Loader::class => [ 'sidebarOrder' => 400 ],
      \HubletoApp\Community\Invoices\Loader::class => [ 'sidebarOrder' => 410 ],
    ],
    'developer' => [
      \HubletoApp\Community\Developer\Loader::class => [ 'sidebarOrder' => 410 ],
    ],
  ];

  public function parseConfigFile(string $configFile): array
  {
    $configStr = (string) file_get_contents($configFile);
    $config = (array) (\Symfony\Component\Yaml\Yaml::parse($configStr) ?? []);
    return $config;
  }

  public function run(): void
  {

    if (is_file($this->main->projectFolder . '/ConfigEnv.php')) {
      \Hubleto\Terminal::red("ConfigEnv.php already exists, project has already been initialized.\n");
      \Hubleto\Terminal::red("If you want to re-initialize the project, delte ConfigEnv.php file first.\n");
      exit;
    }

    $rewriteBase = null;
    $projectFolder = null;
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
    if (isset($config['projectFolder'])) {
      $projectFolder = $config['projectFolder'];
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

    $paths = explode('/', str_replace('\\', '/', $this->main->projectFolder));
    foreach (array_reverse($paths) as $tmpFolder) {
      $rewriteBases[] = $lastRewriteBase . '/';
      $lastRewriteBase = '/' . $tmpFolder . $lastRewriteBase;
    }

    \Hubleto\Terminal::cyan("For more information about the parameters check https://developer.hubleto.com/v0/cli/init\n");

    if ($rewriteBase === null) {
      $rewriteBase = \Hubleto\Terminal::choose($rewriteBases, 'ConfigEnv.rewriteBase', '/');
    }
    if ($projectFolder === null) {
      $projectFolder = $this->main->projectFolder;
    }
    if ($secureFolder === null) {
      $secureFolder = $this->main->secureFolder;
    }
    if ($projectUrl === null) {
      $projectUrl = \Hubleto\Terminal::read('ConfigEnv.projectUrl', 'http://localhost/' . trim((string) $rewriteBase, '/'));
    }
    if ($assetsUrl === null) {
      $assetsUrl = \Hubleto\Terminal::read('ConfigEnv.assetsUrl', 'http://localhost/' . trim((string) $rewriteBase, '/') . '/assets');
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
    if ($generateDemoData === null) {
      $confirm = '';
      while (!in_array($confirm, ['yes', 'no'])) {
        $confirm = \Hubleto\Terminal::read('Account.generateDemoData (type \'yes\' or \'no\')');
      }
      $generateDemoData = $confirm == 'yes';
    }

    if (\Hubleto\Terminal::isLaunchedFromTerminal() && $noPrompt !== true) {
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
      $packagesToInstall = 'sales';
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
    \Hubleto\Terminal::cyan('  -> projectFolder = ' . (string) $projectFolder . "\n");
    \Hubleto\Terminal::cyan('  -> projectUrl = ' . (string) $projectUrl . "\n");
    \Hubleto\Terminal::cyan('  -> secureFolder = ' . (string) $secureFolder . "\n");
    \Hubleto\Terminal::cyan('  -> assetsUrl = ' . (string) $assetsUrl . "\n");
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
    \Hubleto\Terminal::cyan('  -> generateDemoData = ' . ($generateDemoData ? 'yes' : 'no') . "\n");
    \Hubleto\Terminal::cyan('  -> packagesToInstall = ' . (string) $packagesToInstall . "\n");

    $this->main->config->set('projectFolder', $projectFolder);
    $this->main->config->set('projectUrl', $projectUrl);
    $this->main->config->set('secureFolder', $secureFolder);
    $this->main->config->set('assetsUrl', $assetsUrl);

    $this->main->config->set('db_host', $dbHost);
    $this->main->config->set('db_user', $dbUser);
    $this->main->config->set('db_password', $dbPassword);
    $this->main->config->set('db_name', $dbName);

    $this->main->projectFolder = $projectFolder;
    $this->main->secureFolder = $secureFolder;

    \Hubleto\Terminal::cyan("\n");
    \Hubleto\Terminal::cyan("Hurray. Installing your Hubleto with following packages: " . join(", ", explode(",", (string) $packagesToInstall)) . "\n");

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
      (string) $projectFolder,
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

    if ($generateDemoData) {
      (new \HubletoMain\Cli\Agent\Project\GenerateDemoData($this->main, []))->run();
    }

    \Hubleto\Terminal::cyan("\n");
    \Hubleto\Terminal::cyan("All done! You're a fantastic CRM developer.\n");
    \Hubleto\Terminal::colored("cyan", "black", "Now open " . (string) $projectUrl . "?user={$adminEmail} and use this password: " . (string) $adminPassword);
    \Hubleto\Terminal::cyan("  -> Note for NGINX users: don't forget to configure your locations in nginx.conf.\n");
    \Hubleto\Terminal::cyan("  -> Check the developer's guide at https://developer.hubleto.com.\n");
    \Hubleto\Terminal::cyan("\n");
    \Hubleto\Terminal::cyan("💡 TIP: Run command below to create your new app 'MyFirstApp'.\n");
    \Hubleto\Terminal::colored("cyan", "black", "Run: php hubleto app create MyFirstApp");
  }
}
