<?php

namespace HubletoApp\Billing;

use HubletoApp\Settings\Models\Permission;

class Loader extends \HubletoMain\Core\App
{

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);

    $this->registerModel(Models\BillingAccount::class);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^billing\/?$/' => Controllers\BillingAccounts::class,
    ]);

    $this->main->sidebar->addLink(1, 810, 'billing', $this->translate('Billing'), 'fas fa-file-invoice-dollar', str_starts_with($this->main->requestedUri, 'billing'));

  }

  public function installTables() {
    $mBillingAccount = new \HubletoApp\Billing\Models\BillingAccount($this->main);
    $mBillingAccountService = new \HubletoApp\Billing\Models\BillingAccountService($this->main);

    $mBillingAccount->dropTableIfExists()->install();
    $mBillingAccountService->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
  
    $mPermission = new \HubletoApp\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Billing/Models/BillingAccount:Create,Read,Update,Delete",
      "HubletoApp/Billing/Models/BillingAccountService:Create,Read,Update,Delete",
      "HubletoApp/Billing/Controllers/BillingAccount",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}