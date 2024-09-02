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
      [
        'idAccount' => '$1',
      ],
      [
        '' => 'Dashboard',
        '/companies' => 'Companies',
        '/persons' => 'Persons',
        '/address' => 'Addresses',
        '/contacts' => 'Contacts',
        '/activities' => 'Activity',
        '/activities/get' => 'ActivityApi',
        '/tags' => 'Tag',
      ]
    );

    /* $regexAccount = '\\/accounts\\/(\d+)';
    $router->addRoutingGroup(
      'customers' . $regexAccount,
      'CeremonyCrmApp/Modules/Core/Customers/Controllers',
      'CeremonyCrmApp/Modules/Core/Customers/Views',
      [
        'idAccount' => '$1',
      ],
      [
        '' => 'Dashboard',
        '/accounts' => 'Accounts',
        '/companies' => 'Companies',
        '/persons' => 'Persons',
        '/persons-table' => 'Person/PersonsTable',
      ]
    ); */

  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 10100, 'customers', $this->app->translate('Customers'), 'fas fa-user');

    if (str_starts_with($this->app->requestedUri, 'customers')) {
      $sidebar->addHeading1(2, 10200, $this->app->translate('Customers'));
      $sidebar->addLink(2, 10201, 'customers/companies', $this->app->translate('Companies'), 'fas fa-warehouse');
      $sidebar->addLink(2, 10202, 'customers/persons', $this->app->translate('Persons'), 'fas fa-users');
      $sidebar->addLink(2, 10203, 'customers/address', $this->app->translate('Addresses'), 'fas fa-map-pin');
      $sidebar->addLink(2, 10204, 'customers/contacts', $this->app->translate('Contacts'), 'fas fa-address-book');
      $sidebar->addLink(2, 10205, 'customers/activities', $this->app->translate('Activities'), 'fas fa-check');
      $sidebar->addLink(2, 10206, 'customers/tags', $this->app->translate('Tags'), 'fas fa-bars');
    }
  }

  public function generateTestData()
  {
    $mCompany = new Models\Company($this->app);
    $mCompany->install();
    $idCompany = $mCompany->eloquent->create([
      'name' => 'Test Company Ltd.',
      'street_line_1' => 'Street 123',
      'street_line_2' => 'Street 123',
      'region' => 'Trnavský kraj',
      'city' => 'Pieštany',
      'postal_code' => '919 87',
      'id_country' => 6,
      'vat_id' => '123456',
      'company_id' => '987456',
      'tax_id' => '123987',
      'note' => 'My First Company',
      'is_active' => true,
    ])->id;

    $mPerson = new Models\Person($this->app);
    $mPerson->install();
    $idPerson = $mPerson->eloquent->create([
      'first_name' => 'John',
      'last_name' => 'Smith',
      'id_company' => $idCompany,
      'is_primary' => true,
      'is_active' => true,
    ])->id;

    $mPersonContact = new Models\Contact($this->app);
    $mPersonContact->install();
    $mPersonContact->eloquent->create([
      'value' => '+4216489616',
      'type' => 'number',
      'id_person' => $idPerson,
    ]);
    $mPersonContact->eloquent->create([
      'value' => 'john@gmail.com',
      'type' => 'email',
      'id_person' => $idPerson,
    ]);

    $mAddress = new Models\Address($this->app);
    $mAddress->install();
    $mAddress->eloquent->create([
      'street_line_1' => 'Street 123',
      'street_line_2' => 'Street 123',
      'region' => 'Trnavský kraj',
      'city' => 'Pieštany',
      'postal_code' => '919 87',
      'id_country' => 3,
      'id_person' => $idPerson,
    ]);

    $mActivity = new Models\Activity($this->app);
    $mActivity->install();
    $mActivity->eloquent->create([
      'id_company' => $idCompany,
      "id_user" => 1,
      "subject" => "Test Activity",
      "due_date" => "2020-01-22",
      "due_time" => "11:00:00",
      "duration" => "01:00:00",
      "completed" => 0,
    ]);

    $mTag = new Models\Tag($this->app);
    $mTag->install();
    $mTag->eloquent->create([
      'name' => "Category 1",
    ]);
    $mTag->eloquent->create([
      'name' => "Category 2",
    ]);
    $mTag->eloquent->create([
      'name' => "Category 3",
    ]);

    $mActivityTag = new Models\ActivityTag($this->app);
    $mActivityTag->install();
    $mActivityTag->eloquent->create([
      'id_activity' => 1,
      'id_tag' => 1,
    ]);
    $mPersonTag = new Models\PersonTag($this->app);
    $mPersonTag->install();
    $mPersonTag->eloquent->create([
      'id_person' => $idPerson,
      'id_tag' => 1,
    ]);
    $mCompanyTag = new Models\CompanyTag($this->app);
    $mCompanyTag->install();
    $mCompanyTag->eloquent->create([
      'id_company' => $idCompany,
      'id_tag' => 1,
    ]);

    $mAtendance = new Models\Atendance($this->app);
    $mAtendance->install();
    $mAtendance->eloquent->create([
      'id_user' => 1,
      'id_activity' => 1,
    ]);
  }
}