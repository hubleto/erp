<?php

namespace HubletoMain\Core\Api;

class GetAppsInfo extends \HubletoMain\Core\Controllers\ApiController
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
