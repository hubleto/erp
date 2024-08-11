<?php

namespace CeremonyCrmApp\Modules\Core\Customers;

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
      'customers',
      'CeremonyCrmApp/Modules/Core/Customers/Controllers',
      'CeremonyCrmApp/Modules/Core/Customers/Views',
      [],
      [
        '' => 'Dashboard',
        '/companies' => 'Companies',
        '/persons' => 'Persons',
        // '/persons-contact' => 'PersonsTable',
      ]
    );

  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 10100, 'customers', $this->app->translate('Customers'), 'fas fa-user');

    if (str_starts_with($this->app->requestedUri, 'customers')) {
      $sidebar->addHeading1(2, 10200, $this->app->translate('Customers'));
      $sidebar->addLink(2, 10201, 'customers/companies', $this->app->translate('Companies'), 'fas fa-warehouse');
      $sidebar->addLink(2, 10202, 'customers/persons', $this->app->translate('Persons'), 'fas fa-users');
    }
  }

  public function generateTestData()
  {
    $mCompany = new Models\Company($this->app);
    $mCompany->install();
    $idCompany = $mCompany->eloquent->create(['name' => 'Test Company Ltd.'])->id;

    $mPerson = new Models\Person($this->app);
    $mPerson->install();
    $idPerson = $mPerson->eloquent->create([
      'first_name' => 'John',
      'last_name' => 'Smith',
      'id_company' => $idCompany,
    ])->id;

    $mPersonContact = new Models\PersonContact($this->app);
    $mPersonContact->install();
    $mPersonContact->eloquent->create([
      'value' => '+4216489616',
      'type' => 'number',
      'id_person' => $idPerson,
    ]);

    $mPersonAddress = new Models\PersonAddress($this->app);
    $mPersonAddress->install();
    $mPersonAddress->eloquent->create([
      'street' => 'Street 123',
      'city' => 'Pieštany',
      'postal_code' => '919 87',
      'country' => 'Slovakia',
      'id_person' => $idPerson,
    ]);
  }
}