<?php

namespace CeremonyCrmMod\Services;

use CeremonyCrmMod\Billing\Models\BillingAccount;
use CeremonyCrmMod\Billing\Models\BillingAccountService;
use CeremonyCrmMod\Settings\Models\Permission;

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
      '/^services\/get-service-price\/?$/' => Controllers\Api\GetServicePrice::class,
    ]);

    $this->app->sidebar->addLink(1, 600, 'services', $this->translate('Services'), 'fas fa-network-wired', str_starts_with($this->app->requestedUri, 'services'));

    // if (str_starts_with($this->app->requestedUri, 'services')) {
    //   $this->app->sidebar->addHeading1(2, 40100, $this->translate('Services'));
    //   $this->app->sidebar->addLink(2, 40200, 'services', $this->translate('Services'), 'fas fa-network-wired');
    // }
  }

  public function installTables()
  {
    $mService = new Models\Service($this->app);
    $mService->install();
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \CeremonyCrmMod\Settings\Models\Permission($this->app);
    $permissions = [
      "CeremonyCrmMod/Service/Models/Service:Create,Read,Update,Delete",
      "CeremonyCrmMod/Service/Controllers/Service",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}