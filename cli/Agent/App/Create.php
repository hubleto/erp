<?php

namespace Hubleto\Erp\Cli\Agent\App;

class Create extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->appManager()->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));
    $appNamespaceParts = explode('\\', $appNamespace);
    $appName = $appNamespaceParts[count($appNamespaceParts) - 1];

    $noPrompt = (bool) ($this->arguments[4] ?? false);

    switch ($appNamespaceParts[2]) {
      case 'Community':
        $appRepositoryFolder = realpath(__DIR__ . '/../../../apps');
        break;
      case 'Premium':
        throw new \Exception('Creation of premium apps is not implemented yet.');
        break;
      case 'External':
        $externalAppsRepositories = $this->config()->getAsArray('externalAppsRepositories');
        $appRepositoryFolder = $externalAppsRepositories[$appNamespaceParts[3]];
        break;
      case 'Custom':
        $projectFolder = $this->env()->projectFolder;
        if (empty($projectFolder) || !is_dir($projectFolder)) {
          throw new \Exception('projectFolder is not properly configured. (' . $projectFolder . ')');
        }
        if (!is_dir($projectFolder . '/src')) {
          mkdir($projectFolder . '/src');
        }
        if (!is_dir($projectFolder . '/src/apps')) {
          mkdir($projectFolder . '/src/apps');
        }
        $appRepositoryFolder = realpath($projectFolder . '/src/apps');
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

    $this->appManager()->createApp($appNamespace, $appRepositoryFolder . '/' . $appName);

    \Hubleto\Terminal::cyan("App {$appNamespace} created successfully.\n");

    if ($noPrompt || \Hubleto\Terminal::confirm('Do you want to install the app now?')) {
      $this->getService(\Hubleto\Erp\Cli\Agent\App\Install::class)
        ->setAguments($this->arguments)
        ->run()
      ;
    }

    \Hubleto\Terminal::yellow("💡  TIPS:\n");
    \Hubleto\Terminal::yellow("💡  -> Test the app in browser: {$this->env()->projectUrl}/" . strtolower($appName) . "\n");
    \Hubleto\Terminal::yellow("💡  -> Run command below to add your first model.\n");
    \Hubleto\Terminal::colored("cyan", "black", "Run: php hubleto create model {$appNamespace} {$appName}FirstModel");
  }

}
