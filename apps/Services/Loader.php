<?php

namespace HubletoApp\Services;

use HubletoApp\Billing\Models\BillingAccount;
use HubletoApp\Billing\Models\BillingAccountService;
use HubletoApp\Settings\Models\Permission;

class Loader extends \HubletoMain\Core\App
{


  public function __construct(\HubletoMain $app)
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
    $mPermission = new \HubletoApp\Settings\Models\Permission($this->app);
    $permissions = [
      "HubletoApp/Service/Models/Service:Create,Read,Update,Delete",
      "HubletoApp/Service/Controllers/Service",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}