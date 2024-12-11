<?php

namespace CeremonyCrmMod\Core\Services;

use CeremonyCrmMod\Core\Billing\Models\BillingAccount;
use CeremonyCrmMod\Core\Billing\Models\BillingAccountService;
use CeremonyCrmMod\Core\Settings\Models\Permission;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public string $translationContext = 'mod.core.services.loader';

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

    $this->app->sidebar->addLink(1, 40100, 'services', $this->translate('Services'), 'fas fa-network-wired');

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
    $mPermission = new \CeremonyCrmMod\Core\Settings\Models\Permission($this->app);
    $permissions = [
      "CeremonyCrmMod/Core/Service/Models/Service:Create,Read,Update,Delete",
      "CeremonyCrmMod/Core/Service/Controllers/Service",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}