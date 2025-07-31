<?php declare(strict_types=1);

namespace HubletoMain;

/**
 * @property \HubletoMain\Loader $main
 */
class AppManager
{
  public \Hubleto\Framework\App $activatedApp;

  /** @var array<\Hubleto\Framework\App> */
  protected array $apps = [];

  /** @var array<\Hubleto\Framework\App> */
  protected array $disabledApps = [];

  /** @var array<string> */
  public array $registeredAppNamespaces = [];

  public function __construct(public \Hubleto\Framework\Loader $main)
  {
  }

  public function init(): void
  {
    foreach ($this->getInstalledAppNamespaces() as $appNamespace => $appConfig) {
      $appNamespace = (string) $appNamespace;
      $appClass = $appNamespace . '\\Loader';
      if (
        is_array($appConfig)
        && $appClass::canBeAdded($this->main)
      ) {
        // $this->registerApp($appNamespace);
        if ($appConfig['enabled'] ?? false) {
          $this->apps[$appNamespace] = $this->createAppInstance($appNamespace);
          $this->apps[$appNamespace]->enabled = true;
        } else {
          $this->disabledApps[$appNamespace] = $this->createAppInstance($appNamespace);
        }
      }
    }

    $apps = $this->getEnabledApps();
    array_walk($apps, function ($app) {
      if (
        $this->main->requestedUri == $app->manifest['rootUrlSlug']
        || str_starts_with($this->main->requestedUri, $app->manifest['rootUrlSlug'] . '/')
      ) {
        $app->isActivated = true;
        $this->activatedApp = $app;
      }

      $app->init();
    });

  }

  public function onBeforeRender(): void
  {
    $apps = $this->getEnabledApps();
    array_walk($apps, function ($app) { $app->onBeforeRender(); });
  }

  public function getAppNamespaceForConfig(string $appNamespace): string
  {
    return trim($appNamespace, '\\');
  }

  public function getAvailableApps(): array
  {
    $appNamespaces = [];

    // community apps
    $communityRepoFolder = $this->main->srcFolder . '/../../apps/src';
    if (!is_dir($communityRepoFolder)) {

      foreach (scandir($communityRepoFolder) as $folder) {
        $manifestFile = $communityRepoFolder . '/' . $folder . '/manifest.yaml';
        if (@is_file($manifestFile)) {
          $manifestFileContent = file_get_contents($manifestFile);
          $manifest = (array) \Symfony\Component\Yaml\Yaml::parse((string) $manifestFileContent);
          $manifest['appType'] = \Hubleto\Framework\App::APP_TYPE_COMMUNITY;
          $appNamespaces['HubletoApp\\Community\\' . $folder] = $manifest;
        }
      }
    }

    // premium apps
    $premiumRepoFolder = $this->main->config->getAsString('premiumRepoFolder');
    if (!empty($premiumRepoFolder) && is_dir($premiumRepoFolder)) {
      foreach (scandir($premiumRepoFolder) as $folder) {
        $manifestFile = $premiumRepoFolder . '/' . $folder . '/manifest.yaml';
        if (@is_file($manifestFile)) {
          $manifestFileContent = file_get_contents($manifestFile);
          $manifest = (array) \Symfony\Component\Yaml\Yaml::parse((string) $manifestFileContent);
          $manifest['appType'] = \Hubleto\Framework\App::APP_TYPE_PREMIUM;
          $appNamespaces['HubletoApp\\Premium\\' . $folder] = $manifest;
        }
      }
    }

    return $appNamespaces;
  }

  public function getInstalledAppNamespaces(): array
  {
    $tmp = $this->main->config->getAsArray('apps');
    ksort($tmp);

    $appNamespaces = [];
    foreach ($tmp as $key => $value) {
      $appNamespaces[str_replace('-', '\\', $key)] = $value;
    }

    return $appNamespaces;
  }

  public function createAppInstance(string $appNamespace): \Hubleto\Framework\App
  {
    $appClass = $appNamespace . '\Loader';
    $app = new $appClass($this->main);
    return $app; // @phpstan-ignore-line
  }

  /**
  * @return array<\Hubleto\Framework\App>
  */
  public function getEnabledApps(): array
  {
    return $this->apps;
  }

  /**
  * @return array<\Hubleto\Framework\App>
  */
  public function getDisabledApps(): array
  {
    return $this->disabledApps;
  }

  /**
  * @return array<\Hubleto\Framework\App>
  */
  public function getInstalledApps(): array
  {
    return array_merge($this->apps, $this->disabledApps);
  }

  public function getActivatedApp(): \Hubleto\Framework\App|null
  {
    $apps = $this->getEnabledApps();
    foreach ($apps as $app) {
      if (str_starts_with($this->main->requestedUri, $app->getRootUrlSlug())) {
        return $app;
      }
    }
    return null;
  }

  public function getAppInstance(string $appNamespace): null|\Hubleto\Framework\App
  {
    if (isset($this->apps[$appNamespace])) {
      return $this->apps[$appNamespace];
    } else {
      return null;
    }
  }

  public function isAppInstalled(string $appNamespace): bool
  {
    $apps = $this->getInstalledAppNamespaces();
    return isset($apps[$appNamespace]) && is_array($apps[$appNamespace]) && isset($apps[$appNamespace]['installedOn']);
  }

  public function community(string $appName): null|\Hubleto\Framework\App
  {
    return $this->getAppInstance('HubletoApp\\Community\\' . $appName);
  }

  public function custom(string $appName): null|\Hubleto\Framework\App
  {
    return $this->getAppInstance('HubletoApp\\Custom\\' . $appName);
  }

  /** @param array<string, mixed> $appConfig */
  public function installApp(int $round, string $appNamespace, array $appConfig = [], bool $forceInstall = false): bool
  {

    if (str_ends_with($appNamespace, '\\Loader')) {
      $appNamespace = substr($appNamespace, 0, -7);
    }

    \Hubleto\Terminal::cyan("    -> Installing {$appNamespace}, round {$round}.\n");

    if ($this->isAppInstalled($appNamespace) && !$forceInstall) {
      throw new \Exception("{$appNamespace} already installed. Set forceInstall to true if you want to reinstall.");
    }

    if (!class_exists($appNamespace . '\Loader')) {
      throw new \Exception("{$appNamespace} does not exist.");
    }

    $app = $this->createAppInstance($appNamespace);
    if (!file_exists($app->srcFolder . '/manifest.yaml')) {
      throw new \Exception("{$appNamespace} does not provide manifest.yaml file.");
    }

    $manifestFile = (string) file_get_contents($app->srcFolder . '/manifest.yaml');
    $manifest = (array) \Symfony\Component\Yaml\Yaml::parse($manifestFile);
    $dependencies = (array) ($manifest['requires'] ?? []);

    foreach ($dependencies as $dependencyAppNamespace) {
      $dependencyAppNamespace = (string) $dependencyAppNamespace;
      if (!$this->isAppInstalled($dependencyAppNamespace)) {
        \Hubleto\Terminal::cyan("    -> Installing dependency {$dependencyAppNamespace}.\n");
        $this->installApp($round, $dependencyAppNamespace, [], $forceInstall);
      }
    }

    $app->installTables($round);

    if ($round == 1) {
      $appConfig = array_merge($app::DEFAULT_INSTALLATION_CONFIG, $appConfig);

      $appNameForConfig = $this->getAppNamespaceForConfig($appNamespace);

      if (!in_array($appNamespace, $this->getInstalledAppNamespaces())) {
        $this->main->config->set('apps/' . $appNameForConfig . "/installedOn", date('Y-m-d H:i:s'));
        $this->main->config->set('apps/' . $appNameForConfig . "/enabled", true);
        $this->main->config->save('apps/' . $appNameForConfig . "/installedOn", date('Y-m-d H:i:s'));
        $this->main->config->save('apps/' . $appNameForConfig . "/enabled", '1');
      }

      foreach ($appConfig as $cPath => $cValue) {
        $this->main->config->set('apps/' . $appNameForConfig . "/" . $cPath, (string) $cValue);
        $this->main->config->save('apps/' . $appNameForConfig . "/" . $cPath, (string) $cValue);
      }
    }

    if ($round == 3) {
      $app->installDefaultPermissions();
      $app->assignPermissionsToRoles();
    }

    return true;
  }

  public function disableApp(string $appNamespace): void
  {
    $this->main->config->save('apps/' . $this->getAppNamespaceForConfig($appNamespace) . '/enabled', '0');
  }

  public function enableApp(string $appNamespace): void
  {
    $this->main->config->save('apps/' . $this->getAppNamespaceForConfig($appNamespace) . '/enabled', '1');
  }

  public function testApp(string $appNamespace, string $test): void
  {
    $app = $this->createAppInstance($appNamespace);
    $app->test($test);
  }

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
    $appType = strtolower($appNamespaceParts[1]);

    $tplVars = [
      'appNamespace' => $appNamespace,
      'appType' => $appType,
      'appName' => $appName,
      'appRootUrlSlug' => \Hubleto\Framework\Helper::str2url($appName),
      'appViewNamespace' => str_replace('\\', ':', $appNamespace),
      'appNamespaceForwardSlash' => str_replace('\\', '/', $appNamespace),
      'now' => date('Y-m-d H:i:s'),
    ];

    $tplFolder = __DIR__ . '/../cli/Templates/app';

    $this->main->addTwigViewNamespace($tplFolder, 'appTemplate');

    if (!is_dir($appSrcFolder . '/Controllers')) {
      mkdir($appSrcFolder . '/Controllers');
    }
    if (!is_dir($appSrcFolder . '/Views')) {
      mkdir($appSrcFolder . '/Views');
    }

    file_put_contents($appSrcFolder . '/Loader.php', $this->main->twig->render('@appTemplate/Loader.php.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Loader.tsx', $this->main->twig->render('@appTemplate/Loader.tsx.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Calendar.php', $this->main->twig->render('@appTemplate/Calendar.php.twig', $tplVars));
    file_put_contents($appSrcFolder . '/manifest.yaml', $this->main->twig->render('@appTemplate/manifest.yaml.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Controllers/Home.php', $this->main->twig->render('@appTemplate/Controllers/Home.php.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Controllers/Settings.php', $this->main->twig->render('@appTemplate/Controllers/Settings.php.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Views/Home.twig', $this->main->twig->render('@appTemplate/Views/Home.twig.twig', $tplVars));
    file_put_contents($appSrcFolder . '/Views/Settings.twig', $this->main->twig->render('@appTemplate/Views/Settings.twig.twig', $tplVars));
  }

  public function canAppDangerouslyInjectDesktopHtmlContent(string $appNamespace): bool
  {
    $safeApps = [
      'HubletoApp\\Community\\Cloud',
    ];

    return in_array($appNamespace, $safeApps);
  }

}
