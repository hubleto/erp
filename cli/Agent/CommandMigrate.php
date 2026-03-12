<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent;

use Hubleto\Framework\Db\ModelSQLCommandsGenerator;
use Hubleto\Framework\Enums\InstalledMigrationEnum;
use Hubleto\Framework\Interfaces\ModelInterface;

class CommandMigrate extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {

    $appNamespace = (string) ($this->arguments[2] ?? '');
    if (!empty($appNamespace)) {
      $this->appManager()->sanitizeAppNamespace($appNamespace);
    }
    $model = (string) ($this->arguments[3] ?? '');
    $dryRun = (bool) ($this->arguments[4] ?? '') == 'dry-run';

    if ($appNamespace == 'dry-run' || $model == 'dry-run') {
      // This is to overcome the dry-run argument problem.
      // If you run `php hubleto migrate dry-run`,
      // it shall not run the migration for the model `dry-run`.
      $appNamespace = '';
      $model = '';
      $dryRun = true;
    }

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

    if ($dryRun) {
      $this->terminal()->yellow("\nThis is dry run. I will not install any migration.\n");
    }

    $tplFolder = __DIR__ . '/../../Templates/snippets';
    $this->renderer()->addNamespace($tplFolder, 'snippets');

    for ($round = 1; $round <= ($dryRun ? 1 : 2); $round++) {
      if (!$dryRun) {
        if ($round == 1) {
          $this->terminal()->yellow("\nInstalling tables...\n");
        } else {
          $this->terminal()->yellow("\nInstalling foreign keys...\n");
        }
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
            $pendingMigrations = $classObject->getPendingMigrations(InstalledMigrationEnum::TABLES);
            $pendingMigrationsCount = sizeof($pendingMigrations);
          } else {
            $pendingMigrations = $classObject->getPendingMigrations(InstalledMigrationEnum::FOREIGN_KEYS);
            $pendingMigrationsCount = sizeof($pendingMigrations);
          }

          $plural = $pendingMigrationsCount > 1 ? 's' : '';

          if ($pendingMigrationsCount > 0) {
            $this->terminal()->cyan("{$class} has {$pendingMigrationsCount} pending migration{$plural}\n");
            foreach ($pendingMigrations as $migration) {
              $this->terminal()->cyan("  -> " . get_class($migration) . "\n");
            }
 
            if ($dryRun) {
            } else {
              if ($round == 1) {
                $classObject->upgradeSchema();
              } else {
                $classObject->upgradeForeignKeys();
              }
            }
          }
        }
      }
    }

    $this->terminal()->green("\nPending migrations successfully applied!\n");
  }

}
