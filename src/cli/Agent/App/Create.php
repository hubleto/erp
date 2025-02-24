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

    $appManager = new \HubletoMain\Core\AppManager($this->main);
    $appName = $appNamespaceParts[2];

    switch ($appNamespaceParts[1]) {
      case 'Community':
        $appRepositoryFolder = realpath(__DIR__ . '/../../../../apps/community');
      break;
      case 'Enterprise':
        throw new \Exception('Creation of enterprise apps is not implemented yet.');
      break;
      case 'External':
        $externalAppsRepositories = $this->main->configAsArray('externalAppsRepositories');
        $appRepositoryFolder = $externalAppsRepositories[$appNamespaceParts[2]];
      break;
    }

    if (empty($appRepositoryFolder)) throw new \Exception('App repository for \'' . $appNamespace . '\' not configured.');
    if (!is_dir($appRepositoryFolder)) throw new \Exception('App repository for \'' . $appNamespace . '\' is not a folder.');

    if (!is_dir($appRepositoryFolder . '/' . $appName)) mkdir($appRepositoryFolder . '/' . $appName);

    $appManager->createApp($appNamespace, $appRepositoryFolder . '/' . $appName);

    $this->cli->cyan("App {$appNamespace} created successfully.\n");
    $this->cli->cyan("Run 'php hubleto app install {$appNamespace}\Loader force' to install your new app.\n");
  }

  public function validateAppNamespace(string $appNamespace): void
  {
    $appNamespace = trim($appNamespace, '\\');
    $appNamespaceParts = explode('\\', $appNamespace);

    if ($appNamespaceParts[0] != 'HubletoApp') throw new \Exception('Application namespace must start with \'HubletoApp\'.');

    switch ($appNamespaceParts[1]) {
      case 'Community':
        if (count($appNamespaceParts) != 3) throw new \Exception('Community app namespace must have exactly 3 parts');
      break;
      case 'Enterprise':
        if (count($appNamespaceParts) != 3) throw new \Exception('Enterprise app namespace must have exactly 3 parts');
      break;
      case 'External':
        if (count($appNamespaceParts) != 4) throw new \Exception('External app namespace must have exactly 4 parts');

        $externalAppsRepositories = $this->main->configAsArray('externalAppsRepositories');
        if (!isset($externalAppsRepositories[$appNamespaceParts[2]])) {
          throw new \Exception('No repository found for vendor \'' . $appNamespaceParts[2] . '\'. Run \'php hubleto app add repository\' to add the repository.');
        }
      break;
      default:
        throw new \Exception('Only following types of apps are available: Community, Enterprise or External.');
      break;
    }

  }

}