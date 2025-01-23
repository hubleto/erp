<?php

namespace HubletoMain\Core;

class AppManager extends \ADIOS\Auth\Providers\DefaultProvider {
  public \HubletoMain $main;
  public \HubletoMain\Cli\Agent\Loader|null $cli;

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
    $this->cli = null;
  }

  public function setCli(\HubletoMain\Cli\Agent\Loader $cli)
  {
    $this->cli = $cli;
  }

  public function getAppNameForConfig(string $appClass): string
  {
    return str_replace('\\', '-', trim($appClass, '\\'));
  }

  public function getAppConfig(string $appClass): array
  {
    return $this->main->config['apps'][$this->getAppNameForConfig($appClass)] ?? [];
  }

  public function isAppInstalled(string $appClass): bool
  {
    return isset($this->main->config['apps'][$this->getAppNameForConfig($appClass)]);
  }

  public function installApp(string $appClass, bool $forceInstall = false)
  {

    if ($this->cli) $this->cli->cyan("Installing {$appClass}.\n");

    if ($this->isAppInstalled($appClass) && !$forceInstall) {
      throw new \Exception("{$appClass} already installed. Set forceInstall to true if you want to reinstall.");
    }

    if (!class_exists($appClass)) throw new \Exception("{$appClass} does not exist.");

    $app = new $appClass($this->main);

    if (file_exists($app->rootFolder . '/manifest.yaml')) {
      $manifestFile = file_get_contents($app->rootFolder . '/manifest.yaml');
      $dependencies = \Symfony\Component\Yaml\Yaml::parse($manifestFile)['requires'] ?? [];
    } else {
      $dependencies = [];
    }

    foreach ($dependencies as $dependencyAppClass) {
      if (!$this->isAppInstalled($dependencyAppClass)) {
        if ($this->cli) $this->cli->cyan("Installing dependency {$dependencyAppClass}.\n");
        $this->installApp($dependencyAppClass, $forceInstall);
      }
    }

    $app->installTables();
    $app->installDefaultPermissions();

    if (!in_array($appClass, $this->main->config['apps'])) {
      $this->main->config['apps'][$this->getAppNameForConfig($appClass)] = [];
      $this->main->saveConfigByPath('apps/' . $this->getAppNameForConfig($appClass), date('Y-m-d H:i:s'));
    }

    return true;
  }

  public function disableApp(string $appClass)
  {
    $this->main->saveConfigByPath('apps/' . $this->getAppNameForConfig($appClass) . '/enabled', '0');
  }

  public function testApp(string $appClass, string $test)
  {
    $app = new $appClass($this->main);
    $app->test($this->cli, $test);
  }

}