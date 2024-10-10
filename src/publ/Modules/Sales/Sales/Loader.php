<?php

namespace CeremonyCrmApp\Modules\Sales\Sales;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function addRouting(\CeremonyCrmApp\Core\Router $router)
  {
    $router->addRoutingGroup(
      'sales',
      'CeremonyCrmApp/Modules/Sales/Sales/Controllers',
      'CeremonyCrmApp/Modules/Sales/Sales/Views',
      [
        'idAccount' => '$1',
      ],
      [
        '/leads' => 'Leads',
        '/deals' => 'Deals',
      ]
    );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 80100, 'sales/leads', $this->app->translate('Leads'), 'fas fa-trash-alt');

    if (str_starts_with($this->app->requestedUri, 'sales')) {
      $sidebar->addHeading1(2, 10200, $this->app->translate('Sales'));
      $sidebar->addLink(2, 10201, 'sales/leads', $this->app->translate('Leads'), 'fas fa-warehouse');
      $sidebar->addLink(2, 10202, 'sales/deals', $this->app->translate('Deals'), 'fas fa-users');
    }
  }

  public function generateTestData()
  {}
}