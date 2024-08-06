<?php

namespace CeremonyCrmApp\Modules\Core\Customers;

class Loader extends \CeremonyCrmApp\Core\Module {
  public function __construct(\CeremonyCrmApp $app) {
    parent::__construct($app);

    $this->registerModel(Models\Company::class);
  }

  public function addRouting(\ADIOS\Core\Router $router) {
    $router->addRouting([
      '/^companies$/' => [
        'controller' => 'CeremonyCrmApp/Modules/Core/Customers/Controllers/Companies',
        'view' => 'CeremonyCrmApp/Modules/Core/Customers/Views/Companies',
      ],
    ]);
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar) {
    $sidebar->addLink(1, 'customers', $this->app->translate('Customers'), 'fas fa-user');

    $sidebar->addHeading1(2, $this->app->translate('Customers'));
    $sidebar->addLink(2, 'companies', $this->app->translate('Companies'), 'fas fa-warehouse');
  }

  public function generateTestData() {
    $mCompany = new Models\Company($this->app);
    $mCompany->install();
    $mCompany->eloquent->create(['name' => 'Test Company Ltd.']);
  }
}