<?php

namespace HubletoApp\Community\Contacts\Tests;

class RenderAllRoutes extends \HubletoMain\Core\AppTest
{

  public function run(): void
  {
    $routes = [
      'customers/customers',
      'contacts',
      'customers/address',
      'customers/contacts',
      'customers/activities',
      'customers/get-customer',
      'contacts/get-customer-contacts',
      'customers/get-calendar-events',
    ];

    foreach ($routes as $route) {
      $this->cli->cyan("Rendering route '{$route}'.\n");
      $this->main->render($route);
    }
  }

}
