<?php

namespace HubletoApp\Community\Services;

use HubletoApp\Community\Billing\Models\BillingAccount;
use HubletoApp\Community\Billing\Models\BillingAccountService;
use HubletoApp\Community\Settings\Models\Permission;

class Loader extends \HubletoMain\Core\App
{


  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);

    $this->registerModel(Models\Service::class);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^services\/?$/' => Controllers\Services::class,
      '/^services\/get-service-price\/?$/' => Controllers\Api\GetServicePrice::class,
    ]);

    $this->main->sidebar->addLink(1, 600, 'services', $this->translate('Services'), 'fas fa-network-wired', str_starts_with($this->main->requestedUri, 'services'));

    // if (str_starts_with($this->main->requestedUri, 'services')) {
    //   $this->main->sidebar->addHeading1(2, 40100, $this->translate('Services'));
    //   $this->main->sidebar->addLink(2, 40200, 'services', $this->translate('Services'), 'fas fa-network-wired');
    // }
  }

  public function installTables(): void
  {
    $mService = new Models\Service($this->main);
    $mService->install();
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
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