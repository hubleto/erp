<?php

namespace HubletoMain\Core;

class CliAgent {

  public static function installApp($app, $main, &$installed = []) {
    if (in_array($app, $installed)) {
      return;
    }
    $app = new ($app)($main);
    if (file_exists($app->rootFolder . '/manifest.yaml')) {
      $manifestFile = file_get_contents($app->rootFolder . '/manifest.yaml');
      $dependencies = yaml_parse($manifestFile)['requires'] ?? [];
    } else {
      $dependencies = [];
    }

    foreach ($dependencies as $dependency) {
      echo "Installing dependency: " . $dependency  . "\n";
      self::installApp($dependency . '\\Loader', $main, $installed);
    }

    $app->installTables();
    $alreadyInstalled[] = $app;

    $installed[] = $app;
    return true;
  }

}