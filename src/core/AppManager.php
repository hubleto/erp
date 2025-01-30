<?php

namespace HubletoMain\Core;

class AppManager extends \ADIOS\Auth\Providers\DefaultProvider {
  public \HubletoMain $main;
  public \HubletoMain\Cli\Agent\Loader|null $cli;

  /** @var array<\HubletoMain\Core\App> */
  public array $apps = [];

  /** @var array<string> */
  public array $registeredAppClasses = [];

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
    $this->cli = null;
  }

  public function setCli(\HubletoMain\Cli\Agent\Loader $cli): void
  {
    $this->cli = $cli;
  }

  public function getAppNameForConfig(string $appClass): string
  {
    return str_replace('\\', '-', trim($appClass, '\\'));
  }

  public function getApps(): array
  {
    $apps = $this->main->configAsArray('apps');
    ksort($apps);
    return $apps;
  }

  public function getAppConfig(string $appClass): array
  {
    $apps = $this->getApps();
    $key = $this->getAppNameForConfig($appClass);
    if (isset($apps[$key]) && is_array($apps[$key])) return $apps[$key];
    else return [];
  }

  public function createAppInstance(string $appClass): \HubletoMain\Core\App
  {
    $app = new $appClass($this->main);
    if ($this->cli) $app->setCli($this->cli); // @phpstan-ignore-line
    return $app; // @phpstan-ignore-line
  }

  public function isAppRegistered(string $appClass): bool
  {
    return in_array($appClass, $this->registeredAppClasses);
  }

  public function registerApp(string $appClass): void
  {
    if (!$this->isAppRegistered($appClass)) {
      $this->apps[$appClass] = $this->createAppInstance($appClass);
    }
  }

  /**
  * @return array<\HubletoMain\Core\App>
  */
  public function getRegisteredApps(): array
  {
    return $this->apps;
  }

  public function isAppInstalled(string $appClass): bool
  {
    $apps = $this->getApps();
    $key = $this->getAppNameForConfig($appClass);
    return isset($apps[$key]) && is_array($apps[$key]) && isset($apps[$key]['installedOn']);
  }

  public function installApp(string $appClass, bool $forceInstall = false): bool
  {

    if ($this->cli) $this->cli->cyan("Installing {$appClass}.\n");

    if ($this->isAppInstalled($appClass) && !$forceInstall) {
      throw new \Exception("{$appClass} already installed. Set forceInstall to true if you want to reinstall.");
    }

    if (!class_exists($appClass)) throw new \Exception("{$appClass} does not exist.");

    $app = $this->createAppInstance($appClass);

    if (file_exists($app->rootFolder . '/manifest.yaml')) {
      $manifestFile = (string) file_get_contents($app->rootFolder . '/manifest.yaml');
      $manifest = (array) \Symfony\Component\Yaml\Yaml::parse($manifestFile);
      $dependencies = (array) ($manifest['requires'] ?? []);
    } else {
      $dependencies = [];
    }

    foreach ($dependencies as $dependencyAppClass) {
      $dependencyAppClass = (string) $dependencyAppClass;
      if (!$this->isAppInstalled($dependencyAppClass)) {
        if ($this->cli) $this->cli->cyan("Installing dependency {$dependencyAppClass}.\n");
        $this->installApp($dependencyAppClass, $forceInstall);
      }
    }

    $app->installTables();
    $app->installDefaultPermissions();

    if (!in_array($appClass, $this->getApps())) {
      $this->main->setConfig('apps/' . $this->getAppNameForConfig($appClass) . "/installedOn", date('Y-m-d H:i:s'));
      $this->main->saveConfigByPath('apps/' . $this->getAppNameForConfig($appClass) . "/installedOn", date('Y-m-d H:i:s'));
    }

    return true;
  }

  public function disableApp(string $appClass): void
  {
    $this->main->saveConfigByPath('apps/' . $this->getAppNameForConfig($appClass) . '/enabled', '0');
  }

  public function testApp(string $appClass, string $test): void
  {
    $app = $this->createAppInstance($appClass);
    $app->test($test);
  }

}