<?php

namespace Hubleto\Framework\Api;

class GetAppsInfo extends \Hubleto\Framework\Controllers\ApiController
{
  public function renderJson(): array
  {
    $appsInfo = [];
    foreach ($this->main->apps->getInstalledApps() as $app) {
      $appsInfo[$app->namespace] = [
        'manifest' => $app->manifest,
        'permittedForAllUsers' => $app->permittedForAllUsers,
        // 'permittedForActiveUser' => $this->main->permissions->isAppPermittedForActiveUser($app),
      ];
    }

    return $appsInfo;
  }

}
