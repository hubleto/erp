<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent;

use Hubleto\Framework\Db\ModelSQLCommandsGenerator;
use Hubleto\Framework\Enums\InstalledMigrationEnum;
use Hubleto\Framework\Interfaces\ModelInterface;

class Migrate extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {

    $appNamespace = (string) ($this->arguments[2] ?? '');
    if (!empty($appNamespace)) {
      $this->appManager()->sanitizeAppNamespace($appNamespace);
    }
    $model = (string) ($this->arguments[3] ?? '');

    $this->appManager()->init();

    $appQueue = [];

    if (!empty($model) && empty($appNamespace)) {
      throw new \Exception("<model> provided without <appNamespace>. Please provide the app namespace to migrate for a specific model.");
    }

    if (!empty($appNamespace)) {
      $app = $this->getService($appNamespace . '\\Loader');

      if (!$app) {
        throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");
      }

      $appQueue[] = $app;
    } else {
      $appQueue = $this->appManager()->getEnabledApps();
    }

    $tplFolder = __DIR__ . '/../../Templates/snippets';
    $this->renderer()->addNamespace($tplFolder, 'snippets');

    for ($round = 1; $round <= 2; $round++) {
      if ($round == 1) {
        $this->terminal()->yellow("\nInstalling tables...\n");
      } else {
        $this->terminal()->yellow("\nInstalling foreign keys...\n");
      }

      foreach ($appQueue as $app) {
        if (empty($model)) {
          $queue = $app->getAvailableModelClasses();
        } else {
          $queue = [$appNamespace . '\\Models\\' . $model];
        }

        foreach ($queue as $class) {
          $classObject = new $class;

          if (!($classObject instanceof ModelInterface)) {
            throw new \Exception("Class '{$class}' does not implement ModelInterface.");
          }

          $className = basename(str_replace('\\', '/', $class));

          if (!is_file($app->srcFolder . '/Models/' . $className . '.php')) {
            throw new \Exception("Model '{$class}' does not exist in app '{$appNamespace}'.");
          }

          if ($round == 1) {
            $pendingMigrations = sizeof($classObject->getPendingMigrations(InstalledMigrationEnum::TABLES));
          } else {
            $pendingMigrations = sizeof($classObject->getPendingMigrations(InstalledMigrationEnum::FOREIGN_KEYS));
          }

          $plural = $pendingMigrations > 1 ? 's' : '';

          if ($pendingMigrations > 0)
            $this->terminal()->cyan("'{$class}' - {$pendingMigrations} pending migration{$plural}...\n");

          if ($round == 1) {
            $classObject->installTables();
          } else {
            $classObject->installForeignKeys();
          }
        }
      }
    }

    $this->terminal()->green("\n\nLatest migrations successfully applied!\n");
  }

}
