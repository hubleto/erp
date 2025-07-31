<?php

namespace HubletoMain\Cli\Agent\App;

class Create extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));

    $appNamespaceParts = explode('\\', $appNamespace);


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
        if (empty($rootFolder) || !is_dir($rootFolder)) {
          throw new \Exception('rootFolder is not properly configured. (' . $rootFolder . ')');
        }
        if (!is_dir($rootFolder . '/src')) {
          mkdir($rootFolder . '/src');
        }
        if (!is_dir($rootFolder . '/src/apps')) {
          mkdir($rootFolder . '/src/apps');
        }
        $appRepositoryFolder = realpath($rootFolder . '/src/apps');
        break;
    }

    if (empty($appRepositoryFolder)) {
      throw new \Exception('App repository for \'' . $appNamespace . '\' not configured.');
    }
    if (!is_dir($appRepositoryFolder)) {
      throw new \Exception('App repository for \'' . $appNamespace . '\' is not a folder.');
    }

    if (!is_dir($appRepositoryFolder . '/' . $appName)) {
      mkdir($appRepositoryFolder . '/' . $appName);
    }

    $this->main->apps->createApp($appNamespace, $appRepositoryFolder . '/' . $appName);

    \Hubleto\Terminal::cyan("App {$appNamespace} created successfully.\n");

    if (\Hubleto\Terminal::confirm('Do you want to install the app now?')) {
      (new \HubletoMain\Cli\Agent\App\Install($this->main, $this->arguments))->run();
    }

    \Hubleto\Terminal::yellow("💡  TIPS:\n");
    \Hubleto\Terminal::yellow("💡  -> Test the app in browser: {$this->main->config->getAsString('rootUrl')}/" . strtolower($appName) . "\n");
    \Hubleto\Terminal::yellow("💡  -> Run command below to add your first model.\n");
    \Hubleto\Terminal::colored("cyan", "black", "Run: php hubleto create model {$appNamespace} {$appName}FirstModel");
  }

}
