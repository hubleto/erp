<?php

namespace HubletoMain\Core;

class AppManager extends \ADIOS\Auth\Providers\DefaultProvider {
  public \HubletoMain $main;
  public \HubletoMain\Core\CliAgent|null $cli;

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
    $this->cli = null;
  }

  public function setCli(\HubletoMain\Core\CliAgent $cli)
  {
    $this->cli = $cli;
  }

  public function isAppInstalled(string $appClass): bool
  {
    return isset($this->main->config['enabledApps'][trim($appClass, '\\')]);
  }

  public function installApp(string $appClass, bool $forceInstall = false)
  {
    if ($this->cli) $this->cli->green("Installing {$appClass}.\n");

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

    foreach ($dependencies as $dependency) {
      $dependencyAppClass = $dependency . '\\Loader';
      if (!$this->isAppInstalled($dependencyAppClass)) {
        if ($this->cli) $this->cli->green("Installing dependency {$dependency}.\n");
        $this->installApp(new $dependencyAppClass($this->main), $forceInstall);
      }
    }

    $app->installTables();

    if (!in_array($appClass, $this->main->config['enabledApps'])) {
      $this->main->config['enabledApps'][$appClass] = [];
      $this->main->saveConfigByPath('enabledApps/' . trim($appClass, '\\'), '');
    }

    return true;
  }

}