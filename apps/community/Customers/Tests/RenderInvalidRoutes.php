<?php

namespace HubletoApp\Community\Customers\Tests;

class RenderInvalidRoutes extends \HubletoMain\Core\AppTest
{

  public function run(): void
  {
    $routes = [];

    for ($i = 0; $i < 10; $i++) {
      $routes[] = 'customers/customers?recordId=' . rand(1, 9999);
      $routes[] = 'customers/address?recordId=' . rand(1, 9999);
      $routes[] = 'customers/contacts?recordId=' . rand(1, 9999);
      $routes[] = 'customers/activities?recordId=' . rand(1, 9999);

      foreach ($this->sqlInjectionExpressions() as $expr) {
        $routes[] = 'customers/get-customer?search=' . $expr;
        $routes[] = 'customers/get-customer-contacts?search=' . $expr;
        $routes[] = 'customers/get-calendar-events?start=' . $expr;
        $routes[] = 'customers/get-calendar-events?end=' . $expr;
      }

      $routes[] = 'customers/get-customer-contacts?id_customer=' . (string) rand(1, 9999);
      $routes[] = 'customers/get-calendar-events?idCustomer=' . (string) rand(1, 9999);
    }

    foreach ($routes as $route) {
      $this->cli->cyan("Rendering route [{$route}].\n");
      $this->main->render($route);
    }
  }

}
