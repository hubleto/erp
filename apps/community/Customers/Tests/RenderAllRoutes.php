<?php

namespace HubletoApp\Community\Customers\Tests;

class RenderAllRoutes extends \HubletoMain\Core\AppTest
{

  public function run(): void
  {
    $routes = [
      'customers/companies',
      'customers/persons',
      'customers/address',
      'customers/contacts',
      'customers/activities',
      'customers/get-company',
      'customers/get-company-contacts',
      'customers/get-calendar-events',
    ];

    foreach ($routes as $route) {
      $this->cli->cyan("Rendering route '{$route}'.\n");
      $this->main->render($route);
    }
  }

}
