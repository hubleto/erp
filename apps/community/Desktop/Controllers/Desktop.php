<?php

namespace HubletoApp\Community\Desktop\Controllers;

class Desktop extends \HubletoMain\Core\Controllers\Controller
{

  public bool $disableLogUsage = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $appsInSidebar = $this->main->apps->getEnabledApps();
    $activatedApp = null;

    foreach ($appsInSidebar as $appNamespace => $app) {
      if ($app->configAsInteger('sidebarOrder') <= 0) {
        unset($appsInSidebar[$appNamespace]);
      }
    }

    uasort($appsInSidebar, function($a, $b) {
      $aOrder = $a->configAsInteger('sidebarOrder');
      $bOrder = $b->configAsInteger('sidebarOrder');
      return $aOrder <=> $bOrder;
    });

    $this->viewParams['appsInSidebar'] = $appsInSidebar;
    $this->viewParams['activatedApp'] = $activatedApp;

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $this->viewParams['appMenu'] = $appMenu->getItems();

    $this->setView('@HubletoApp:Community:Desktop/Desktop.twig');
  }

}