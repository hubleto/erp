<?php

namespace Hubleto\App\Community\Developer\Controllers;

use Hubleto\Framework\Enums\InstalledMigrationEnum;
use Hubleto\Framework\Interfaces\ModelInterface;

class UpgradeModels extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'developer', 'content' => $this->translate('Developer tools') ],
      [ 'url' => '', 'content' => $this->translate('Upgrade models') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $logs = [];

    $apps = $this->appManager()->getEnabledApps();
    foreach ($apps as $app) {
      $mClasses = $app->getAvailableModelClasses();
      foreach ($mClasses as $mClass) {
        /** @var \Hubleto\Framework\Model */
        $mObj = $this->getService($mClass);
        if (!($mObj instanceof ModelInterface)) throw new \Exception($e);
        $availableMigrations = $mObj->getPendingMigrations(InstalledMigrationEnum::TABLES);
        if (count($availableMigrations) > 0) {
          $logs[] = 'Installing migrations for ' . $mObj->fullName . '.';
          foreach ($availableMigrations as $migration) {
            $logs[] = '  ' . get_class($migration);
          }

          try {
            $mObj->installTables();
            $logs[] = 'Table migrations for ' . $mObj->fullName . ' successfully installed.';
          } catch (\Throwable $e) {
            $logs[] = 'Table migrations for ' . $mObj->fullName . ' failed to install.';
          }
          $logs[] = '--';
        }
      }

      foreach ($mClasses as $mClass) {
        /** @var \Hubleto\Framework\Model */
        $mObj = $this->getService($mClass);
        if (!($mObj instanceof ModelInterface)) throw new \Exception($e);
        $availableMigrations = $mObj->getPendingMigrations(InstalledMigrationEnum::FOREIGN_KEYS);
        if (count($availableMigrations) > 0) {
          $logs[] = 'Installing migrations for ' . $mObj->fullName . '.';
          foreach ($availableMigrations as $migration) {
            $logs[] = '  ' . get_class($migration);
          }

          try {
            $mObj->installForeignKeys();
            $logs[] = 'Foreign key migrations for ' . $mObj->fullName . ' successfully installed.';
          } catch (\Throwable $e) {
            $logs[] = 'Foreign key migrations for ' . $mObj->fullName . ' failed to install.';
          }
          $logs[] = '--';
        }
      }
    }

    $this->viewParams['logs'] = $logs;

    $this->setView('@Hubleto:App:Community:Developer/UpgradeModels.twig');
  }

}
