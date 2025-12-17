<?php

namespace Hubleto\App\Community\Developer\Controllers;

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
        $mObj = $this->getService($mClass);
        $availableUpgrades = $mObj->getAvailableUpgrades();
        if (count($availableUpgrades) > 0) {
          $logs[] = 'Installing upgrades for ' . $mObj->fullName . '.';
          foreach ($availableUpgrades as $upgrade) {
            $logs[] = '  ' . $upgrade;
          }

          try {
            $mObj->installUpgrades();
            $logs[] = 'Upgrades for ' . $mObj->fullName . ' successfully installed.';
          } catch (\Throwable $e) {
            $logs[] = 'Upgrades for ' . $mObj->fullName . ' failed to install.';
          }
          $logs[] = '--';
        }
      }
    }

    $this->viewParams['logs'] = $logs;

    $this->setView('@Hubleto:App:Community:Developer/UpgradeModels.twig');
  }

}
