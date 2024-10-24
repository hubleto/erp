<?php

namespace CeremonyCrmApp\Modules\Core\Customers;

use CeremonyCrmApp\Modules\Core\Settings\Models\Tag;

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
      [
        'idAccount' => '$1',
      ],
      [
        '/companies' => 'Companies',
        '/persons' => 'Persons',
        '/address' => 'Addresses',
        '/contacts' => 'Contacts',
        '/activities' => 'Activity',
        '/activities/get' => 'ActivityApi',
        '/tags' => 'Tag',
        '/documents' => 'Documents',

      ]
    );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 10100, 'customers/companies', $this->app->translate('Customers'), 'fas fa-address-card');

    if (str_starts_with($this->app->requestedUri, 'customers')) {
      $sidebar->addHeading1(2, 10200, $this->app->translate('Customers'));
      $sidebar->addLink(2, 10201, 'customers/companies', $this->app->translate('Companies'), 'fas fa-building');
      $sidebar->addLink(2, 10202, 'customers/persons', $this->app->translate('Contact Persons'), 'fas fa-users');
      //$sidebar->addLink(2, 10203, 'customers/activities', $this->app->translate('Activities'), 'fas fa-users');
    }
  }

  public function install() {
    $mCompany = new Models\Company($this->app);
    $mCompany->install();

    $mPerson = new Models\Person($this->app);
    $mPerson->install();

    $mPersonContact = new Models\Contact($this->app);
    $mPersonContact->install();

    $mAddress = new Models\Address($this->app);
    $mAddress->install();

    $mTag = new Tag($this->app);
    $mTag->install();

    $mPersonTag = new Models\PersonTag($this->app);
    $mPersonTag->install();

    $mCompanyTag = new Models\CompanyTag($this->app);
    $mCompanyTag->install();

    $mAtendance = new Models\Atendance($this->app);
    $mAtendance->install();

    // pridat zaznamy do Core/Settings/Models/UserPermission
    // pridat zaznamy do Core/Settings/Models/UserRolePermission
    // pridat zaznamy do Core/Settings/Models/Setting (default values)
  }

  public function generateTestData()
  {

    $mTag = new Tag($this->app);

    $mTag->eloquent->create([
      'name' => "Category 1",
    ]);
    $mTag->eloquent->create([
      'name' => "Category 2",
    ]);
    $mTag->eloquent->create([
      'name' => "Category 3",
    ]);
  }
}