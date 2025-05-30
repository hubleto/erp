<?php

namespace HubletoApp\Community\Billing;

use HubletoApp\Community\Settings\Models\Permission;

class Loader extends \HubletoMain\Core\App
{

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^billing\/?$/' => Controllers\BillingAccounts::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mBillingAccount = new \HubletoApp\Community\Billing\Models\BillingAccount($this->main);
      $mBillingAccountService = new \HubletoApp\Community\Billing\Models\BillingAccountService($this->main);

      $mBillingAccount->dropTableIfExists()->install();
      $mBillingAccountService->dropTableIfExists()->install();
    }
  }

  // public function installDefaultPermissions(): void
  // {

  //   $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
  //   $permissions = [
  //     "HubletoApp/Community/Billing/Models/BillingAccount:Create",
  //     "HubletoApp/Community/Billing/Models/BillingAccount:Read",
  //     "HubletoApp/Community/Billing/Models/BillingAccount:Update",
  //     "HubletoApp/Community/Billing/Models/BillingAccount:Delete",

  //     "HubletoApp/Community/Billing/Models/BillingAccountService:Create",
  //     "HubletoApp/Community/Billing/Models/BillingAccountService:Read",
  //     "HubletoApp/Community/Billing/Models/BillingAccountService:Update",
  //     "HubletoApp/Community/Billing/Models/BillingAccountService:Delete",

  //     "HubletoApp/Community/Billing/Controllers/BillingAccount",
  //     "HubletoApp/Community/Billing/Controllers/BillingAccountService",

  //     "HubletoApp/Community/Billing/BillingAccount",
  //   ];

  //   foreach ($permissions as $permission) {
  //     $mPermission->record->recordCreate([
  //       "permission" => $permission
  //     ]);
  //   }
  // }
}