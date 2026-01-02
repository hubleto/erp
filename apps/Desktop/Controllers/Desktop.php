<?php

namespace Hubleto\App\Community\Desktop\Controllers;


use Hubleto\App\Community\Settings\PermissionsManager;

use Hubleto\App\Community\Desktop\Loader;
use Hubleto\App\Community\Notifications\Counter;

class Desktop extends \Hubleto\Erp\Controller
{
  public bool $disableLogUsage = true;

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Loader */
    $desktopApp = $this->getService(Loader::class);

    $appsInSidebar = $this->appManager()->getEnabledApps();
    $activatedApp = null;

    foreach ($appsInSidebar as $appNamespace => $app) {
      if (
        !$this->getService(PermissionsManager::class)->isAppPermittedForActiveUser($app)
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

    $sidebarGroups = $desktopApp->getSidebarGroups();
    $activatedSidebarGroup = [];
    $activatedSidebarGroupUrlSlug = '';
    if ($activatedApp && !empty($activatedApp->manifest['sidebarGroup'])) {
      foreach ($sidebarGroups as $gUrlSlug => $gData) {
        if (in_array($gUrlSlug, explode(",", $activatedApp->manifest['sidebarGroup']))) {
          $activatedSidebarGroup = $gData;
          $activatedSidebarGroupUrlSlug = $gUrlSlug;
          break;
        }
      }
    }

    $this->viewParams['appsInSidebar'] = $appsInSidebar;
    $this->viewParams['activatedApp'] = $activatedApp;
    $this->viewParams['activatedSidebarGroup'] = $activatedSidebarGroup;
    $this->viewParams['activatedSidebarGroupUrlSlug'] = $activatedSidebarGroupUrlSlug;
    $this->viewParams['sidebarGroups'] = $sidebarGroups;
    $this->viewParams['release'] = \Composer\InstalledVersions::getPrettyVersion('hubleto/erp');

    $notificationsCounter = $this->getService(Counter::class);
    $this->viewParams['unreadNotifications'] = $notificationsCounter->myUnread();

    $this->viewParams['availableLanguages'] = $this->locale()->getAvailableLanguages();

    $this->viewParams['appMenu'] = [];
    foreach ($desktopApp->appMenu as $item) {
      if ($item['app'] === $activatedApp) {
        $this->viewParams['appMenu'][] = $item;
      }
    }

    /** @var AuthProvider $authProvider */
    $authProvider = $this->getService(\Hubleto\Framework\AuthProvider::class);
    $this->viewParams['user'] = $authProvider->getUserFromSession();

    $dictionary = $this->translator()->loadFullDictionary($this, $this->authProvider()->getUserLanguage());

    $this->viewParams['dictionaryString'] = base64_encode(json_encode($dictionary));

    $this->setView('@Hubleto:App:Community:Desktop/Desktop.twig');
  }

}
