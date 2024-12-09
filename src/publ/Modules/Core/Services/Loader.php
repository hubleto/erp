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

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^services\/?$/' => Controllers\Services::class,
      '/^services\/get-service-price\/?$/' => Controllers\GetServicePrice::class,
    ]);

    // $router(
    //   'services',
    //   'CeremonyCrmApp/Modules/Core/Services/Controllers',
    //   '@app/Modules/Core/Services/Views',
    //   [
    //     'idAccount' => '$1',
    //   ],
    //   [
    //     '' => 'Services',
    //     '/get-service-price' => 'GetServicePrice',
    //   ]
    // );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 40100, 'services', $this->app->translate('Services'), 'fas fa-network-wired');

    if (str_starts_with($this->app->requestedUri, 'services')) {
      $sidebar->addHeading1(2, 40100, $this->app->translate('Services'));
      $sidebar->addLink(2, 40200, 'services', $this->app->translate('Services'), 'fas fa-network-wired');
    }
  }

  public function installTables()
  {
    $mService = new Models\Service($this->app);
    $mService->install();
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \CeremonyCrmApp\Modules\Core\Settings\Models\Permission($this->app);
    $permissions = [
      "CeremonyCrmApp/Modules/Core/Service/Models/Service:Create,Read,Update,Delete",
      "CeremonyCrmApp/Modules/Core/Service/Controllers/Service",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}