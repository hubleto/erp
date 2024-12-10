<?php

namespace CeremonyCrmApp\Modules\Core\Billing;

use CeremonyCrmApp\Modules\Core\Settings\Models\Permission;

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

    $this->app->sidebar->addLink(1, 30100, 'billing', $this->app->translate('Billing'), 'fas fa-file-invoice-dollar');

    if (str_starts_with($this->app->requestedUri, 'billing')) {
      $this->app->sidebar->addHeading1(2, 30100, $this->app->translate('Billing'));
      $this->app->sidebar->addLink(2, 30200, 'billing', $this->app->translate('Billing Accounts'), 'fas fa-file-invoice-dollar');
    }
  }

  public function installTables() {
    $mBillingAccount = new \CeremonyCrmApp\Modules\Core\Billing\Models\BillingAccount($this->app);
    $mBillingAccountService = new \CeremonyCrmApp\Modules\Core\Billing\Models\BillingAccountService($this->app);

    $mBillingAccount->dropTableIfExists()->install();
    $mBillingAccountService->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
  
    $mPermission = new \CeremonyCrmApp\Modules\Core\Settings\Models\Permission($this->app);
    $permissions = [
      "CeremonyCrmApp/Modules/Core/Billing/Models/BillingAccount:Create,Read,Update,Delete",
      "CeremonyCrmApp/Modules/Core/Billing/Models/BillingAccountService:Create,Read,Update,Delete",
      "CeremonyCrmApp/Modules/Core/Billing/Controllers/BillingAccount",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}