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

  public function addRouting(\CeremonyCrmApp\Core\Router $router)
  {
    $router->addRoutingGroup(
      'billing',
      'CeremonyCrmApp/Modules/Core/Billing/Controllers',
      '@app/Modules/Core/Billing/Views',
      [
        'idAccount' => '$1',
      ],
      [
        '' => 'BillingAccounts',
      ]
    );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 30100, 'billing', $this->app->translate('Billing'), 'fas fa-file-invoice-dollar');

    if (str_starts_with($this->app->requestedUri, 'billing')) {
      $sidebar->addHeading1(2, 30100, $this->app->translate('Billing'));
      $sidebar->addLink(2, 30200, 'billing', $this->app->translate('Billing Accounts'), 'fas fa-file-invoice-dollar');
    }
  }

  public function generateTestData()
  {
    $mBillingAccount = new Models\BillingAccount($this->app);
    $mBillingAccount->install();
   /*  $idBillingAccount = $mBillingAccount->eloquent->create([
      'id_company' => 1,
      "description" => "Test Billing Account"
    ])->id; */
  }

  public function createPermissions()
  {
    $mPermission = new Permission($this->app);
    $permissions = [
      "CeremonyCrmApp/Modules/Core/Billing/Models/BillingAccount:Create",
      "CeremonyCrmApp/Modules/Core/Billing/Models/BillingAccount:Read",
      "CeremonyCrmApp/Modules/Core/Billing/Models/BillingAccount:Update",
      "CeremonyCrmApp/Modules/Core/Billing/Models/BillingAccount:Delete",
      "CeremonyCrmApp/Modules/Core/Billing/Models/BillingAccountService:Create",
      "CeremonyCrmApp/Modules/Core/Billing/Models/BillingAccountService:Read",
      "CeremonyCrmApp/Modules/Core/Billing/Models/BillingAccountService:Update",
      "CeremonyCrmApp/Modules/Core/Billing/Models/BillingAccountService:Delete",
      "CeremonyCrmApp/Modules/Core/Billing/Controllers/BillingAccount",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}