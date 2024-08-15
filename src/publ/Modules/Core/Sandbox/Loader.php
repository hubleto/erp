<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);

    $this->registerModel(Models\Company::class);
  }

  public function addRouting(\CeremonyCrmApp\Core\Router $router)
  {
    $router->addRoutingGroup(
      'sandbox',
      'CeremonyCrmApp/Modules/Core/Sandbox/Controllers',
      'CeremonyCrmApp/Modules/Core/Sandbox/Views',
      [],
      [
        '' => 'Dashboard',
        '/companies' => 'Companies',
        '/persons' => 'Persons',
        '/categories' => 'Categories',
        '/companies-categories' => 'CompaniesCategories',
      ]
    );

  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 999100, 'sandbox', $this->app->translate('Sandbox'), 'fas fa-vial');

    if (str_starts_with($this->app->requestedUri, 'sandbox')) {
      $sidebar->addHeading1(2, 999200, $this->app->translate('Sandbox'));
      $sidebar->addLink(2, 999201, 'sandbox/companies', $this->app->translate('Companies'), 'fas fa-warehouse');
      $sidebar->addLink(2, 999202, 'sandbox/persons', $this->app->translate('Persons'), 'fas fa-users');
      $sidebar->addLink(2, 999203, 'sandbox/categories', $this->app->translate('Categories'), 'fas fa-warehouse');
      $sidebar->addLink(2, 999204, 'sandbox/companies-categories', $this->app->translate('Companies - Categories'), 'fas fa-warehouse');
    }
  }

  public function generateTestData()
  {
    $mCategory = new Models\Category($this->app);
    (new Models\Company($this->app))->install();
    (new Models\Person($this->app))->install();
    $mCategory->install();
    (new Models\CompanyCategory($this->app))->install();

    $mCategory->eloquent->create(['category' => 'Bronze', 'color' => '#CE8946']);
    $mCategory->eloquent->create(['category' => 'Silver', 'color' => '#C0C0C0']);
    $mCategory->eloquent->create(['category' => 'Gold', 'color' => '#FFD700']);
    $mCategory->eloquent->create(['category' => 'Platinum', 'color' => '#E5E4E2']);
  }
}