<?php

namespace HubletoApp\Billing;

use HubletoApp\Settings\Models\Permission;

class Loader extends \HubletoCore\Core\Module
{

  public function __construct(\HubletoCore $app)
  {
    parent::__construct($app);

    $this->registerModel(Models\BillingAccount::class);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^billing\/?$/' => Controllers\BillingAccounts::class,
    ]);

    $this->app->sidebar->addLink(1, 810, 'billing', $this->translate('Billing'), 'fas fa-file-invoice-dollar', str_starts_with($this->app->requestedUri, 'billing'));

  }

  public function installTables() {
    $mBillingAccount = new \HubletoApp\Billing\Models\BillingAccount($this->app);
    $mBillingAccountService = new \HubletoApp\Billing\Models\BillingAccountService($this->app);

    $mBillingAccount->dropTableIfExists()->install();
    $mBillingAccountService->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
  
    $mPermission = new \HubletoApp\Settings\Models\Permission($this->app);
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