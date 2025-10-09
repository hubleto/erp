<?php

namespace Hubleto\Erp\Cli\Agent\App;

use Hubleto\Framework\Helper;

class Create extends \Hubleto\Erp\Cli\Agent\Command
{

  /**
   * [Description for createApp]
   *
   * @param string $appNamespace
   * @param string $appSrcFolder
   * 
   * @return void
   * 
   */
  public function createApp(string $appNamespace, string $appSrcFolder): void
  {
    if (empty($appSrcFolder)) {
      throw new \Exception('App folder for \'' . $appNamespace . '\' not configured.');
    }
    if (!is_dir($appSrcFolder)) {
      throw new \Exception('App folder for \'' . $appNamespace . '\' is not a folder.');
    }

    $appNamespace = trim($appNamespace, '\\');
    $appNamespaceParts = explode('\\', $appNamespace);
    $appName = $appNamespaceParts[count($appNamespaceParts) - 1];
    $appType = strtolower($appNamespaceParts[2]);

    $tplVars = [
      'appNamespace' => $appNamespace,
      'appType' => $appType,
      'appName' => $appName,
      'appRootUrlSlug' => Helper::str2url($appName),
      'appViewNamespace' => str_replace('\\', ':', $appNamespace),
      'appNamespaceForwardSlash' => str_replace('\\', '/', $appNamespace),
      'now' => date('Y-m-d H:i:s'),
    ];

    $tplFolder = __DIR__ . '/../cli/Templates/app';

    $this->renderer()->addNamespace($this->env()->srcFolder . '/../cli/Templates/app', 'appTemplate');

    if (!is_dir($appSrcFolder . '/Controllers')) {
      mkdir($appSrcFolder . '/Controllers');
    }
    if (!is_dir($appSrcFolder . '/Views')) {
      mkdir($appSrcFolder . '/Views');
    }
    if (!is_dir($appSrcFolder . '/Extendibles')) {
      mkdir($appSrcFolder . '/Extendibles');
    }

    file_put_contents($appSrcFolder . '/Loader.php', $this->renderer()->renderView('@appTemplate/Loader.php.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Loader.tsx', $this->renderer()->renderView('@appTemplate/Loader.tsx.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Calendar.php', $this->renderer()->renderView('@appTemplate/Calendar.php.twig', $tplVars));
    file_put_contents($appSrcFolder . '/manifest.yaml', $this->renderer()->renderView('@appTemplate/manifest.yaml.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Controllers/Home.php', $this->renderer()->renderView('@appTemplate/Controllers/Home.php.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Controllers/Settings.php', $this->renderer()->renderView('@appTemplate/Controllers/Settings.php.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Views/Home.twig', $this->renderer()->renderView('@appTemplate/Views/Home.twig.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Views/Settings.twig', $this->renderer()->renderView('@appTemplate/Views/Settings.twig.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Extendibles/AppMenu.php', $this->renderer()->renderView('@appTemplate/Extendibles/AppMenu.php.twig', $tplVars));
  }

  /**
   * [Description for run]
   *
   * @return void
   * 
   */
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

    $this->createApp($appNamespace, $appRepositoryFolder . '/' . $appName);

    $this->terminal()->cyan("App {$appNamespace} created successfully.\n");

    if ($noPrompt || $this->terminal()->confirm('Do you want to install the app now?')) {
      $this->getService(\Hubleto\Erp\Cli\Agent\App\Install::class)
        ->setTerminalOutput($this->terminal()->output)
        ->setArguments($this->arguments)
        ->run()
      ;
    }

    $this->terminal()->yellow("💡  TIPS:\n");
    $this->terminal()->yellow("💡  -> Test the app in browser: {$this->env()->projectUrl}/" . strtolower($appName) . "\n");
    $this->terminal()->yellow("💡  -> Run command below to add your first model.\n");
    $this->terminal()->colored("cyan", "black", "Run: php hubleto create model {$appNamespace} {$appName}FirstModel\n");
  }

}
