<?php

namespace HubletoMain\Core;

class AppManager
{

  public \HubletoMain $main;
  public \HubletoMain\Cli\Agent\Loader|null $cli;

  /** @var array<\HubletoMain\Core\App> */
  public array $apps = [];

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
      if (is_array($appConfig) && $appClass::canBeAdded($this->main)) {
        $this->registerApp($appNamespace);
      }
    }

    $apps = $this->getRegisteredApps();
    array_walk($apps, function($app) {
      $app->init();
    });

  }

  public function setCli(\HubletoMain\Cli\Agent\Loader $cli): void
  {
    $this->cli = $cli;
  }

  public function getAppNameForConfig(string $appNamespace): string
  {
    return trim($appNamespace, '\\');
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
    $key = $this->getAppNameForConfig($appNamespace);
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

  public function isAppRegistered(string $appNamespace): bool
  {
    return in_array($appNamespace, $this->registeredAppNamespaces);
  }

  public function registerApp(string $appNamespace, bool $force = true): void
  {
    if ($force || !$this->isAppRegistered($appNamespace)) {
      $this->apps[$appNamespace] = $this->createAppInstance($appNamespace);
    }
  }

  /**
  * @return array<\HubletoMain\Core\App>
  */
  public function getRegisteredApps(): array
  {
    return $this->apps;
  }

  public function getActivatedApp(): \HubletoMain\Core\App|null
  {
    $apps = $this->getRegisteredApps();
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

  /** @param array<string, mixed> $appConfig */
  public function installApp(int $round, string $appNamespace, array $appConfig, bool $forceInstall = false): bool
  {

    if (str_ends_with($appNamespace, '\\Loader')) $appNamespace = substr($appNamespace, 0, -7);

    // if ($this->cli) $this->cli->cyan("Installing {$appNamespace}, round {$round}.\n");

    if ($this->isAppInstalled($appNamespace) && !$forceInstall) {
      throw new \Exception("{$appNamespace} already installed. Set forceInstall to true if you want to reinstall.");
    }

    if (!class_exists($appNamespace . '\Loader')) throw new \Exception("{$appNamespace} does not exist.");

    $app = $this->createAppInstance($appNamespace);
    if (!file_exists($app->rootFolder . '/manifest.yaml')) throw new \Exception("{$appNamespace} does not provide manifest.yaml file.");

    // $manifestFile = (string) file_get_contents($app->rootFolder . '/manifest.yaml');
    // $manifest = (array) \Symfony\Component\Yaml\Yaml::parse($manifestFile);
    // $dependencies = (array) ($manifest['requires'] ?? []);

    // foreach ($dependencies as $dependencyAppNamespace) {
    //   $dependencyAppNamespace = (string) $dependencyAppNamespace;
    //   if (!$this->isAppInstalled($dependencyAppNamespace)) {
    //     if ($this->cli) $this->cli->cyan("Installing dependency {$dependencyAppNamespace}.\n");
    //     $this->installApp($dependencyAppNamespace, [], $forceInstall);
    //   }
    // }

    $app->installTables($round);

    if ($round == 1) {
      $appConfig = array_merge($app::DEFAULT_INSTALLATION_CONFIG, $appConfig);

      $app->installDefaultPermissions();

      $appNameForConfig = $this->getAppNameForConfig($appNamespace);

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

    return true;
  }

  public function disableApp(string $appNamespace): void
  {
    $this->main->config->save('apps/' . $this->getAppNameForConfig($appNamespace) . '/enabled', '0');
  }

  public function testApp(string $appNamespace, string $test): void
  {
    $app = $this->createAppInstance($appNamespace);
    $app->test($test);
  }

  public function createApp(string $appNamespace, string $appFolder): void
  {
    if (empty($appFolder)) throw new \Exception('App folder for \'' . $appNamespace . '\' not configured.');
    if (!is_dir($appFolder)) throw new \Exception('App folder for \'' . $appNamespace . '\' is not a folder.');

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

    if (!is_dir($appFolder . '/Controllers')) mkdir($appFolder . '/Controllers');
    if (!is_dir($appFolder . '/Models')) mkdir($appFolder . '/Models');
    if (!is_dir($appFolder . '/Models/RecordManagers')) mkdir($appFolder . '/Models/RecordManagers');
    if (!is_dir($appFolder . '/Views')) mkdir($appFolder . '/Views');

    file_put_contents($appFolder . '/Loader.php', $this->main->twig->render('@appTemplate/Loader.php.twig', $tplVars));
    file_put_contents($appFolder . '/manifest.yaml', $this->main->twig->render('@appTemplate/manifest.yaml.twig', $tplVars));
    file_put_contents($appFolder . '/Models/Contact.php', $this->main->twig->render('@appTemplate/Models/Contact.php.twig', $tplVars));
    file_put_contents($appFolder . '/Models/RecordManagers/Contact.php', $this->main->twig->render('@appTemplate/Models/RecordManagers/Contact.php.twig', $tplVars));
    file_put_contents($appFolder . '/Controllers/Contacts.php', $this->main->twig->render('@appTemplate/Controllers/Contacts.php.twig', $tplVars));
    file_put_contents($appFolder . '/Controllers/Dashboard.php', $this->main->twig->render('@appTemplate/Controllers/Dashboard.php.twig', $tplVars));
    file_put_contents($appFolder . '/Views/Contacts.twig', $this->main->twig->render('@appTemplate/Views/Contacts.twig.twig', $tplVars));
    file_put_contents($appFolder . '/Views/Dashboard.twig', $this->main->twig->render('@appTemplate/Views/Dashboard.twig.twig', $tplVars));
  }

}