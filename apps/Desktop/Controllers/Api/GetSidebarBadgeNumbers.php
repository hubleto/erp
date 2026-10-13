<?php

namespace Hubleto\App\Community\Desktop\Controllers\Api;

use Hubleto\App\Community\Desktop\Loader;

class GetSidebarBadgeNumbers extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {

    $sbnCache = $this->config()->getAsJson('cache/sidebarBadgeNumbers');
    if (is_array($sbnCache) && isset($sbnCache['expiry']) && $sbnCache['expiry'] > time()) {
      $sidebarBadgeNumbers = $sbnCache['data'];
    } else {

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

      $this->config()->save('cache/sidebarBadgeNumbers', json_encode([
        'expiry' => time() + 60*5,
        'data' => $sidebarBadgeNumbers
      ]));
    }

    return [
      "sidebarBadgeNumbers" => $sidebarBadgeNumbers,
    ];
  }

}
