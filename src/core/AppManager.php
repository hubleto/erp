<?php

namespace HubletoMain\Core;

class AppManager
{

  public \HubletoMain $main;
  public \HubletoMain\Cli\Agent\Loader|null $cli;
  public \HubletoMain\Core\App $activatedApp;

  /** @var array<\HubletoMain\Core\App> */
  protected array $apps = [];

  /** @var array<\HubletoMain\Core\App> */
  protected array $disabledApps = [];

  /** @var array<string> */
  public array $registeredAppNamespaces = [];

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
    $this->cli = null;
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
    array_walk($apps, function($app) {
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
    array_walk($apps, function($app) { $app->onBeforeRender(); });
  }

  public function setCli(\HubletoMain\Cli\Agent\Loader $cli): void
  {
    $this->cli = $cli;
  }

  public function getAppNamespaceForConfig(string $appNamespace): string
  {
    return trim($appNamespace, '\\');
  }

  public function getAvailableApps(): array
  {
    $appNamespaces = [];

    // community apps
    $communityRepoFolder = $this->main->config->getAsString('srcFolder') . '/apps/community';
    if (!empty($communityRepoFolder)) {

      foreach (scandir($communityRepoFolder) as $rootFolder) {
        $manifestFile = $communityRepoFolder . '/' . $rootFolder . '/manifest.yaml';
        if (@is_file($manifestFile)) {
          $manifest = (array) \Symfony\Component\Yaml\Yaml::parse(file_get_contents($manifestFile));
          $manifest['appType'] = \HubletoMain\Core\App::APP_TYPE_COMMUNITY;
          $appNamespaces['HubletoApp\\Community\\' . $rootFolder] = $manifest;
        }
      }
    }

    // premium apps
    $premiumRepoFolder = $this->main->config->getAsString('premiumRepoFolder');
    if (!empty($premiumRepoFolder)) {
      foreach (scandir($premiumRepoFolder) as $rootFolder) {
        $manifestFile = $premiumRepoFolder . '/' . $rootFolder . '/manifest.yaml';
        if (@is_file($manifestFile)) {
          $manifest = (array) \Symfony\Component\Yaml\Yaml::parse(file_get_contents($manifestFile));
          $manifest['appType'] = \HubletoMain\Core\App::APP_TYPE_PREMIUM;
          $appNamespaces['HubletoApp\\Premium\\' . $rootFolder] = $manifest;
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

  public function getAppConfig(string $appNamespace): array
  {
    $appNamespaces = $this->getInstalledAppNamespaces();
    $key = $this->getAppNamespaceForConfig($appNamespace);
    if (isset($apps[$key]) && is_array($appNamespaces[$key])) return $appNamespaces[$key];
    else return [];
  }

  public function createAppInstance(string $appNamespace): \HubletoMain\Core\App
  {
    $appClass = $appNamespace . '\Loader';
    $app = new $appClass($this->main);
    if ($this->cli) $app->setCli($this->cli); // @phpstan-ignore-line
    return $app; // @phpstan-ignore-line
  }

  /**
  * @return array<\HubletoMain\Core\App>
  */
  public function getEnabledApps(): array
  {
    return $this->apps;
  }

  /**
  * @return array<\HubletoMain\Core\App>
  */
  public function getDisabledApps(): array
  {
    return $this->disabledApps;
  }

  /**
  * @return array<\HubletoMain\Core\App>
  */
  public function getInstalledApps(): array
  {
    return array_merge($this->apps, $this->disabledApps);
  }

  public function getActivatedApp(): \HubletoMain\Core\App|null
  {
    $apps = $this->getEnabledApps();
    foreach ($apps as $app) {
      if (str_starts_with($this->main->requestedUri, $app->getRootUrlSlug())) {
        return $app;
      }
    }
    return null;
  }

  public function getAppInstance(string $appNamespace): null|\HubletoMain\Core\App
  {
    if (isset($this->apps[$appNamespace])) return $this->apps[$appNamespace];
    else return null;
  }

  public function isAppInstalled(string $appNamespace): bool
  {
    $apps = $this->getInstalledAppNamespaces();
    return isset($apps[$appNamespace]) && is_array($apps[$appNamespace]) && isset($apps[$appNamespace]['installedOn']);
  }

  public function community(string $appName): null|\HubletoMain\Core\App
  {
    return $this->getAppInstance('HubletoApp\\Community\\' . $appName);
  }

  public function custom(string $appName): null|\HubletoMain\Core\App
  {
    return $this->getAppInstance('HubletoApp\\Custom\\' . $appName);
  }

  /** @param array<string, mixed> $appConfig */
  public function installApp(int $round, string $appNamespace, array $appConfig = [], bool $forceInstall = false): bool
  {

    if (str_ends_with($appNamespace, '\\Loader')) $appNamespace = substr($appNamespace, 0, -7);

    if ($this->cli) $this->cli->cyan("    -> Installing {$appNamespace}, round {$round}.\n");

    if ($this->isAppInstalled($appNamespace) && !$forceInstall) {
      throw new \Exception("{$appNamespace} already installed. Set forceInstall to true if you want to reinstall.");
    }

    if (!class_exists($appNamespace . '\Loader')) throw new \Exception("{$appNamespace} does not exist.");

    $app = $this->createAppInstance($appNamespace);
    if (!file_exists($app->rootFolder . '/manifest.yaml')) throw new \Exception("{$appNamespace} does not provide manifest.yaml file.");

    $manifestFile = (string) file_get_contents($app->rootFolder . '/manifest.yaml');
    $manifest = (array) \Symfony\Component\Yaml\Yaml::parse($manifestFile);
    $dependencies = (array) ($manifest['requires'] ?? []);

    foreach ($dependencies as $dependencyAppNamespace) {
      $dependencyAppNamespace = (string) $dependencyAppNamespace;
      if (!$this->isAppInstalled($dependencyAppNamespace)) {
        if ($this->cli) $this->cli->cyan("    -> Installing dependency {$dependencyAppNamespace}.\n");
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

  public function createApp(string $appNamespace, string $rootFolder): void
  {
    if (empty($rootFolder)) throw new \Exception('App folder for \'' . $appNamespace . '\' not configured.');
    if (!is_dir($rootFolder)) throw new \Exception('App folder for \'' . $appNamespace . '\' is not a folder.');

    $appNamespace = trim($appNamespace, '\\');
    $appNamespaceParts = explode('\\', $appNamespace);
    $appName = $appNamespaceParts[count($appNamespaceParts) - 1];

    $tplVars = [
      'appNamespace' => $appNamespace,
      'appName' => $appName,
      'appRootUrlSlug' => \ADIOS\Core\Helper::str2url($appName),
      'appViewNamespace' => str_replace('\\', ':', $appNamespace),
      'appNamespaceForwardSlash' => str_replace('\\', '/', $appNamespace),
      'now' => date('Y-m-d H:i:s'),
    ];

    $tplFolder = __DIR__ . '/../code_templates/app';

    $this->main->addTwigViewNamespace($tplFolder, 'appTemplate');

    if (!is_dir($rootFolder . '/Controllers')) mkdir($rootFolder . '/Controllers');
    // if (!is_dir($rootFolder . '/Models')) mkdir($rootFolder . '/Models');
    // if (!is_dir($rootFolder . '/Models/RecordManagers')) mkdir($rootFolder . '/Models/RecordManagers');
    if (!is_dir($rootFolder . '/Views')) mkdir($rootFolder . '/Views');

    file_put_contents($rootFolder . '/Loader.php', $this->main->twig->render('@appTemplate/Loader.php.twig', $tplVars));
    file_put_contents($rootFolder . '/Loader.tsx', $this->main->twig->render('@appTemplate/Loader.tsx.twig', $tplVars));
    file_put_contents($rootFolder . '/Calendar.php', $this->main->twig->render('@appTemplate/Calendar.php.twig', $tplVars));
    file_put_contents($rootFolder . '/manifest.yaml', $this->main->twig->render('@appTemplate/manifest.yaml.twig', $tplVars));
    // file_put_contents($rootFolder . '/Models/Contact.php', $this->main->twig->render('@appTemplate/Models/Contact.php.twig', $tplVars));
    // file_put_contents($rootFolder . '/Models/RecordManagers/Contact.php', $this->main->twig->render('@appTemplate/Models/RecordManagers/Contact.php.twig', $tplVars));
    file_put_contents($rootFolder . '/Controllers/Home.php', $this->main->twig->render('@appTemplate/Controllers/Home.php.twig', $tplVars));
    // file_put_contents($rootFolder . '/Controllers/Contacts.php', $this->main->twig->render('@appTemplate/Controllers/Contacts.php.twig', $tplVars));
    file_put_contents($rootFolder . '/Controllers/Settings.php', $this->main->twig->render('@appTemplate/Controllers/Settings.php.twig', $tplVars));
    file_put_contents($rootFolder . '/Views/Home.twig', $this->main->twig->render('@appTemplate/Views/Home.twig.twig', $tplVars));
    // file_put_contents($rootFolder . '/Views/Contacts.twig', $this->main->twig->render('@appTemplate/Views/Contacts.twig.twig', $tplVars));
    file_put_contents($rootFolder . '/Views/Settings.twig', $this->main->twig->render('@appTemplate/Views/Settings.twig.twig', $tplVars));
  }



  public function canAppDangerouslyInjectDesktopHtmlContent(string $appNamespace): bool
  {
    $safeApps = [
      'HubletoApp\\Community\\Cloud',
    ];

    return in_array($appNamespace, $safeApps);
  }

}