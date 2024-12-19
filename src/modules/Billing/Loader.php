<?php

namespace CeremonyCrmMod\Billing;

use CeremonyCrmMod\Settings\Models\Permission;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
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
    $mBillingAccount = new \CeremonyCrmMod\Billing\Models\BillingAccount($this->app);
    $mBillingAccountService = new \CeremonyCrmMod\Billing\Models\BillingAccountService($this->app);

    $mBillingAccount->dropTableIfExists()->install();
    $mBillingAccountService->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
  
    $mPermission = new \CeremonyCrmMod\Settings\Models\Permission($this->app);
    $permissions = [
      "CeremonyCrmMod/Billing/Models/BillingAccount:Create,Read,Update,Delete",
      "CeremonyCrmMod/Billing/Models/BillingAccountService:Create,Read,Update,Delete",
      "CeremonyCrmMod/Billing/Controllers/BillingAccount",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}