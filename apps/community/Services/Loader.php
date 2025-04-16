<?php

namespace HubletoApp\Community\Services;

use HubletoApp\Community\Billing\Models\BillingAccount;
use HubletoApp\Community\Billing\Models\BillingAccountService;
use HubletoApp\Community\Settings\Models\Permission;

class Loader extends \HubletoMain\Core\App
{

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^services\/?$/' => Controllers\Services::class,
      '/^services\/get-service-price\/?$/' => Controllers\Api\GetServicePrice::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mService = new Models\Service($this->main);
      $mService->install();
    }
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Services/Models/Service:Create",
      "HubletoApp/Community/Services/Models/Service:Read",
      "HubletoApp/Community/Services/Models/Service:Update",
      "HubletoApp/Community/Services/Models/Service:Delete",

      "HubletoApp/Community/Services/Controllers/Services",

      "HubletoApp/Community/Services/Api/GetServicePrice",

      "HubletoApp/Community/Services/Services",
    ];

    foreach ($permissions as $permission) {
      $mPermission->record->recordCreate([
        "permission" => $permission
      ]);
    }
  }
}