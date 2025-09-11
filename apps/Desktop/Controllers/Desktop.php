<?php

namespace Hubleto\App\Community\Desktop\Controllers;

use Hubleto\App\Community\Desktop\Loader;

class Desktop extends \Hubleto\Erp\Controller
{
  public bool $disableLogUsage = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $appsInSidebar = $this->appManager()->getEnabledApps();
    $activatedApp = null;

    foreach ($appsInSidebar as $appNamespace => $app) {
      if (
        !$this->permissionsManager()->isAppPermittedForActiveUser($app)
        || $app->configAsInteger('sidebarOrder') <= 0
      ) {
        unset($appsInSidebar[$appNamespace]);
      }
      if ($app->isActivated) {
        $activatedApp = $app;
      }
    }

    if ($activatedApp === null) {
      $activatedApp = $this->appManager()->getApp(\Hubleto\App\Community\Desktop\Loader::class);
    }

    uasort($appsInSidebar, function ($a, $b) {
      $aOrder = $a->configAsInteger('sidebarOrder');
      $bOrder = $b->configAsInteger('sidebarOrder');
      return $aOrder <=> $bOrder;
    });

    $this->viewParams['appsInSidebar'] = $appsInSidebar;
    $this->viewParams['activatedApp'] = $activatedApp;
    $this->viewParams['sidebarGroups'] = $this->getService(Loader::class)->getSidebarGroups();

    $this->viewParams['availableLanguages'] = $this->config()->getAsArray('availableLanguages', [
      "en" => [ "flagImage" => "en.jpg", "name" => "English" ],
      "de" => [ "flagImage" => "de.jpg", "name" => "Deutsch" ],
      "es" => [ "flagImage" => "es.jpg", "name" => "Español" ],
      "fr" => [ "flagImage" => "fr.jpg", "name" => "Francais" ],
      "it" => [ "flagImage" => "it.jpg", "name" => "Italiano" ],
      "pl" => [ "flagImage" => "pl.jpg", "name" => "Polski" ],
      "ro" => [ "flagImage" => "ro.jpg", "name" => "Română" ],
      "cs" => [ "flagImage" => "cs.jpg", "name" => "Česky" ],
      "sk" => [ "flagImage" => "sk.jpg", "name" => "Slovensky" ],
    ]);

    $appMenu = $this->getService(Loader::class)->appMenu;
    $this->viewParams['appMenu'] = [];
    foreach ($appMenu as $item) {
      if ($item['app'] === $activatedApp) {
        $this->viewParams['appMenu'][] = $item;
      }
    }

    $this->setView('@Hubleto:App:Community:Desktop/Desktop.twig');
  }

}
