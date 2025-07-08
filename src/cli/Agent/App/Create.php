<?php

namespace HubletoMain\Cli\Agent\App;

class Create extends \HubletoMain\Cli\Agent\Command
{

  public function run(): void
  {
    $appNamespace = (string) ($this->arguments[3] ?? '');
    $appNamespace = trim($appNamespace, '\\');
    $appNamespaceParts = explode('\\', $appNamespace);

    $this->validateAppNamespace($appNamespace);

    $appName = $appNamespaceParts[count($appNamespaceParts) - 1];

    switch ($appNamespaceParts[1]) {
      case 'Community':
        $appRepositoryFolder = realpath(__DIR__ . '/../../../../apps/community');
      break;
      case 'Premium':
        throw new \Exception('Creation of premium apps is not implemented yet.');
      break;
      case 'External':
        $externalAppsRepositories = $this->main->config->getAsArray('externalAppsRepositories');
        $appRepositoryFolder = $externalAppsRepositories[$appNamespaceParts[2]];
      break;
      case 'Custom':
        $rootFolder = $this->main->config->getAsString('rootFolder');
        if (empty($rootFolder) || !is_dir($rootFolder)) throw new \Exception('rootFolder is not properly configured. (' . $rootFolder . ')');
        if (!is_dir($rootFolder . '/apps')) mkdir($rootFolder . '/apps');
        $appRepositoryFolder = realpath($rootFolder . '/apps');
      break;
    }

    if (empty($appRepositoryFolder)) throw new \Exception('App repository for \'' . $appNamespace . '\' not configured.');
    if (!is_dir($appRepositoryFolder)) throw new \Exception('App repository for \'' . $appNamespace . '\' is not a folder.');

    if (!is_dir($appRepositoryFolder . '/' . $appName)) mkdir($appRepositoryFolder . '/' . $appName);

    $this->main->apps->createApp($appNamespace, $appRepositoryFolder . '/' . $appName);

    $this->cli->cyan("App {$appNamespace} created successfully.\n");

    if ($this->cli->confirm('Do you want to install the app now?')) {
      (new \HubletoMain\Cli\Agent\App\Install($this->cli, $this->arguments))->run();
    }

    $this->cli->yellow("💡  TIPS:\n");
    $this->cli->yellow("💡  -> Test the app in browser: {$this->main->config->getAsString('rootUrl')}/" . strtolower($appName) . "\n");
    $this->cli->yellow("💡  -> Run command below to add your first model.\n");
    $this->cli->colored("cyan", "black", "Run: php hubleto create model {$appNamespace} {$appName}FirstModel");
  }

  public function validateAppNamespace(string $appNamespace): void
  {
    $appNamespace = trim($appNamespace, '\\');
    $appNamespaceParts = explode('\\', $appNamespace);

    if ($appNamespaceParts[0] != 'HubletoApp') throw new \Exception('Application namespace must start with \'HubletoApp\'. See https://developer.hubleto.com/apps for more details.');

    switch ($appNamespaceParts[1]) {
      case 'Community':
        if (count($appNamespaceParts) != 3) throw new \Exception('Community app namespace must have exactly 3 parts');
      break;
      case 'Premium':
        if (count($appNamespaceParts) != 3) throw new \Exception('Premium app namespace must have exactly 3 parts');
      break;
      case 'External':
        if (count($appNamespaceParts) != 4) throw new \Exception('External app namespace must have exactly 4 parts');

        $externalAppsRepositories = $this->main->config->getAsArray('externalAppsRepositories');
        if (!isset($externalAppsRepositories[$appNamespaceParts[2]])) {
          throw new \Exception('No repository found for vendor \'' . $appNamespaceParts[2] . '\'. Run \'php hubleto app add repository\' to add the repository.');
        }
      break;
      case 'Custom':
        if (count($appNamespaceParts) != 3) throw new \Exception('Custom app namespace must have exactly 3 parts');
      break;
      default:
        throw new \Exception('Only following types of apps are available: Community, Premium, External or Custom.');
      break;
    }

  }

}