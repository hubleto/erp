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
    
    switch ($appNamespaceParts[1]) {
      case 'Community': $appRepositoryFolder = realpath(__DIR__ . '/../../../..'); break;
      case 'Enterprise':
        throw new \Exception('Creation of enterprise apps is not implemented yet.');
      break;
      case 'External':
        $externalAppsRepositories = $this->main->configAsArray('externalAppsRepositories');
        $appRepositoryFolder = $externalAppsRepositories[$appNamespaceParts[2]];
      break;
    }

    $appManager->createApp($appNamespace, $appRepositoryFolder);

    $this->cli->cyan("App {$appNamespace} created successfully.\n");
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