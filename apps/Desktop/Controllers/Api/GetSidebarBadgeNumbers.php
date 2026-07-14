<?php

namespace Hubleto\App\Community\Desktop\Controllers\Api;

use Hubleto\App\Community\Desktop\Loader;

class GetSidebarBadgeNumbers extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {

    /** @var Loader */
    $desktopApp = $this->getService(Loader::class);

    $appsInSidebar = $desktopApp->getAppsInSidebar();

    $sidebarBadgeNumbers = [];
    foreach ($appsInSidebar as $appNamespace => $app) {
      try {
        $sidebarBadgeNumbers[$appNamespace] = $app->getSidebarBadgeNumber();
      } catch (\Throwable $e) {
        //
      }
    }

    return [
      "sidebarBadgeNumbers" => $sidebarBadgeNumbers,
    ];
  }

}
