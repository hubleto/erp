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

    $appsInSidebar = $desktopApp->getAppsInSidebar();
    $activatedApp = $desktopApp->getActivatedApp();
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

    $sidebarGroupsCollapsed = $this->config()->getAsJson('sidebarGroupsCollapsed') ?? [];
    foreach ($sidebarGroups as $gUrlSlug => $gData) {
      $sidebarGroups[$gUrlSlug]['isCollapsed'] = $sidebarGroupsCollapsed[$gUrlSlug] ?? false;
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

    $authProvider = $this->authProvider();
    $this->viewParams['user'] = $authProvider->getUserFromSession();

    $dictionary = $this->translator()->loadFullDictionary($this, $this->authProvider()->getUserLanguage());

    $this->viewParams['dictionaryString'] = base64_encode(json_encode($dictionary));

    $this->viewParams['secondSidebar'] = $activatedApp ? $activatedApp->renderSecondSidebar() : '';
    $this->viewParams['priorityNotifications'] = $activatedApp ? $activatedApp->renderPriorityNotifications() : '';

    $this->setView('@Hubleto:App:Community:Desktop/Desktop.twig');
  }

}
