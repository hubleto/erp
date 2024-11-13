<?php

namespace CeremonyCrmApp\Modules\Core\Services;

use CeremonyCrmApp\Modules\Core\Billing\Models\BillingAccount;
use CeremonyCrmApp\Modules\Core\Billing\Models\BillingAccountService;
use CeremonyCrmApp\Modules\Core\Settings\Models\Permission;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);

    $this->registerModel(Models\Service::class);
  }

  public function addRouting(\CeremonyCrmApp\Core\Router $router)
  {
    $router->addRoutingGroup(
      'services',
      'CeremonyCrmApp/Modules/Core/Services/Controllers',
      '@app/Modules/Core/Services/Views',
      [
        'idAccount' => '$1',
      ],
      [
        '' => 'Services',
        '/get-service-price' => 'GetServicePrice',
      ]
    );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 40100, 'services', $this->app->translate('Services'), 'fas fa-network-wired');

    if (str_starts_with($this->app->requestedUri, 'services')) {
      $sidebar->addHeading1(2, 40100, $this->app->translate('Services'));
      $sidebar->addLink(2, 40200, 'services', $this->app->translate('Services'), 'fas fa-network-wired');
    }
  }

  public function generateTestData()
  {
    $mService = new Models\Service($this->app);
    $mService->install();
    $idService = $mService->eloquent->create([
      "name" => "Test Service 1"
    ])->id;

    $mBillingAccountService = new BillingAccountService($this->app);
    $mBillingAccountService->install();
    /* $mBillingAccountService->eloquent->create([
      'id_billing_account' => 1,
      'id_service' => $idService
    ]); */
  }

  public function createPermissions()
  {
    $mPermission = new Permission($this->app);
    $permissions = [
      "CeremonyCrmApp/Modules/Core/Service/Models/Service:Create",
      "CeremonyCrmApp/Modules/Core/Service/Models/Service:Read",
      "CeremonyCrmApp/Modules/Core/Service/Models/Service:Update",
      "CeremonyCrmApp/Modules/Core/Service/Models/Service:Delete",
      "CeremonyCrmApp/Modules/Core/Service/Controllers/Service",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}