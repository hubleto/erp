<?php

namespace HubletoApp\Community\Customers\Tests;

class RenderAllRoutes extends \Hubleto\Framework\AppTest
{
  public function run(): void
  {
    $routes = [
      'customers/customers',
      'contacts',
      'customers/address',
      'customers/contacts',
      'customers/activities',
      'customers/api/get-customer',
      'contacts/get-customer-contacts',
      // 'customers/api/get-calendar-events',
    ];

    foreach ($routes as $route) {
      \Hubleto\Terminal::cyan("Rendering route '{$route}'.\n");
      $this->main->render($route);
    }
  }

}
