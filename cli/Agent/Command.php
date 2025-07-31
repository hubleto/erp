<?php

// wrapper for `php hubleto` command

namespace HubletoMain\Cli\Agent;

class Command
{
  public \HubletoMain\Loader $main;

  public array $arguments = [];

  public function __construct(\HubletoMain\Loader $main, array $arguments)
  {
    $this->main = $main;
    $this->arguments = $arguments;
  }

  public function sanitizeAppNamespace(string $appNamespace): string
  {
    $appNamespace = trim($appNamespace, '\\');
    if (strpos($appNamespace, '\\') === false) {
      $appNamespace = 'HubletoApp\\Custom\\' . $appNamespace;
    }
    $this->validateAppNamespace($appNamespace);
    return $appNamespace;
  }

  public function validateAppNamespace(string $appNamespace): void
  {
    $appNamespace = trim($appNamespace, '\\');
    $appNamespaceParts = explode('\\', $appNamespace);

    if ($appNamespaceParts[0] != 'HubletoApp') {
      throw new \Exception('Application namespace must start with \'HubletoApp\'. See https://developer.hubleto.com/apps for more details.');
    }

    switch ($appNamespaceParts[1]) {
      case 'Community':
        if (count($appNamespaceParts) != 3) {
          throw new \Exception('Community app namespace must have exactly 3 parts');
        }
        break;
      case 'Premium':
        if (count($appNamespaceParts) != 3) {
          throw new \Exception('Premium app namespace must have exactly 3 parts');
        }
        break;
      case 'External':
        if (count($appNamespaceParts) != 4) {
          throw new \Exception('External app namespace must have exactly 4 parts');
        }

        $externalAppsRepositories = $this->main->config->getAsArray('externalAppsRepositories');
        if (!isset($externalAppsRepositories[$appNamespaceParts[2]])) {
          throw new \Exception('No repository found for vendor \'' . $appNamespaceParts[2] . '\'. Run \'php hubleto app add repository\' to add the repository.');
        }
        break;
      case 'Custom':
        if (count($appNamespaceParts) != 3) {
          throw new \Exception('Custom app namespace must have exactly 3 parts');
        }
        break;
      default:
        throw new \Exception('Only following types of apps are available: Community, Premium, External or Custom.');
        break;
    }

  }

  public function run(): void
  {
    // to be implemented in sub-classes
  }

}
