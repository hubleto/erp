<?php

namespace HubletoApp\Community\Contacts\Tests;

class RenderInvalidRoutes extends \Hubleto\Framework\AppTest
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
        $routes[] = 'customers/api/get-customer?search=' . $expr;
        $routes[] = 'contacts/get-customer-contacts?search=' . $expr;
        // $routes[] = 'customers/api/get-calendar-events?start=' . $expr;
        // $routes[] = 'customers/api/get-calendar-events?end=' . $expr;
      }

      $routes[] = 'contacts/get-customer-contacts?id_customer=' . (string) rand(1, 9999);
      // $routes[] = 'customers/api/get-calendar-events?idCustomer=' . (string) rand(1, 9999);
    }

    foreach ($routes as $route) {
      \Hubleto\Terminal::cyan("Rendering route [{$route}].\n");
      $this->main->render($route);
    }
  }

}
