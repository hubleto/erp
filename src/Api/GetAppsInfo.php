<?php declare(strict_types=1);

namespace HubletoMain\Api;

class GetAppsInfo extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): array
  {
    $appsInfo = [];
    foreach ($this->getAppManager()->getInstalledApps() as $app) {
      $appsInfo[$app->namespace] = [
        'manifest' => $app->manifest,
        'permittedForAllUsers' => $app->permittedForAllUsers,
      ];
    }

    return $appsInfo;
  }

}
