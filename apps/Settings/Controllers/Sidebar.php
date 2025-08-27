<?php

namespace Hubleto\App\Community\Settings\Controllers;

class Sidebar extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'sidebar', 'content' => $this->translate('Sidebar') ],
    ]);
  }

  public function init(): void
  {
    $appManager = $this->getAppManager();

    $installedApps = array_merge($appManager->getEnabledApps(), $appManager->getDisabledApps());

    if ($this->getRouter()->urlParamAsBool("save")) {
      $appSidebarSettings = $this->getRouter()->urlParamAsArray('app');

      foreach ($appSidebarSettings as $rootUrlSlug => $sidebarOrder) {
        foreach ($installedApps as $appNamespace => $app) {
          if (($app->manifest['rootUrlSlug'] ?? '') == $rootUrlSlug) {
            $app->saveConfig('sidebarOrder', $sidebarOrder);
            $app->setConfigAsString('sidebarOrder', $sidebarOrder);
          }
        }
      }
    }

  }

  public function prepareView(): void
  {
    parent::prepareView();

    $appManager = $this->getAppManager();

    $installedApps = array_merge($appManager->getEnabledApps(), $appManager->getDisabledApps());

    uasort($installedApps, function ($a, $b) {
      $aOrder = $a->configAsInteger('sidebarOrder');
      $bOrder = $b->configAsInteger('sidebarOrder');
      return $aOrder <=> $bOrder;
    });

    $this->viewParams['installedApps'] = $installedApps;

    $this->setView('@Hubleto:App:Community:Settings/Sidebar.twig');
  }

}
