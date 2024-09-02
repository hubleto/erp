<?php

namespace CeremonyCrmApp\Modules\Core\Billing;

use CeremonyCrmApp\Modules\Core\Settings\Models\Country;

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
      'CeremonyCrmApp/Modules/Core/Billing/Views',
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
    $sidebar->addLink(1, 20100, 'billing', $this->app->translate('Billing'), 'fas fa-money-bill');

    if (str_starts_with($this->app->requestedUri, 'billing')) {
      $sidebar->addHeading1(2, 20200, $this->app->translate('Billing'));
      $sidebar->addLink(2, 20201, 'billing', $this->app->translate('Billing Accounts'), 'fas fa-warehouse');
    }
  }

  public function generateTestData()
  {
    $mBillingAccount = new Models\BillingAccount($this->app);
    $mBillingAccount->install();
    $idBillingAccount = $mBillingAccount->eloquent->create([
      'id_company' => 1,
      "description" => "Test Business Account"
    ])->id;


  }
}