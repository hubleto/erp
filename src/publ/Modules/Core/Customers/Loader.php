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
        '/accounts' => 'Accounts',
        '/companies' => 'Companies',
        '/companies/table-companies' => 'Company/TableCompanies',
        '/persons' => 'Persons',
        '/persons/table-persons' => 'Person/TablePersons',
        '/person-addresses' => 'PersonAddresses',
        '/person-contacts' => 'PersonContacts',
        '/business-accounts' => 'BusinessAccounts',
        '/activities' => 'Activity',
        '/activities/categories' => 'ActivityCategory',
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
      $sidebar->addLink(2, 10201, 'customers/accounts', $this->app->translate('Accounts'), 'fas fa-address-card');
      $sidebar->addLink(2, 10202, 'customers/companies', $this->app->translate('Companies'), 'fas fa-warehouse');
      $sidebar->addLink(2, 10203, 'customers/persons', $this->app->translate('Persons'), 'fas fa-users');
      $sidebar->addLink(2, 10204, 'customers/person-addresses', $this->app->translate('Person Addresses'), 'fas fa-map-pin');
      $sidebar->addLink(2, 10205, 'customers/person-contacts', $this->app->translate('Persons Contacts'), 'fas fa-address-book');
      $sidebar->addLink(2, 10206, 'customers/business-accounts', $this->app->translate('Business Accounts'), 'fas fa-business-time');
      $sidebar->addLink(2, 10207, 'customers/activities', $this->app->translate('Activities'), 'fas fa-check');
      $sidebar->addLink(2, 10208, 'customers/activities/categories', $this->app->translate('Activity Categories'), 'fas fa-bars');
    }
  }

  public function generateTestData()
  {
    $mAccount = new Models\Account($this->app);
    $mAccount->install();
    $idAccount = $mAccount->eloquent->create([
      'name' => 'Test Account',
    ])->id;

    $mCompany = new Models\Company($this->app);
    $mCompany->install();
    $idCompany = $mCompany->eloquent->create([
      'name' => 'Test Company Ltd.',
      'street' => 'Street 123',
      'city' => 'Pieštany',
      'postal_code' => '919 87',
      'country' => 'Slovakia',
      'id_account' => $idAccount,
    ])->id;

    $mPerson = new Models\Person($this->app);
    $mPerson->install();
    $idPerson = $mPerson->eloquent->create([
      'first_name' => 'John',
      'last_name' => 'Smith',
      'id_company' => $idCompany,
      'id_account' => $idAccount,
    ])->id;

    $mPersonContact = new Models\PersonContact($this->app);
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

    $mPersonAddress = new Models\PersonAddress($this->app);
    $mPersonAddress->install();
    $mPersonAddress->eloquent->create([
      'street' => 'Street 123',
      'city' => 'Pieštany',
      'postal_code' => '919 87',
      'country' => 'Slovakia',
      'id_person' => $idPerson,
    ]);

    $mBusinessAccount = new Models\BusinessAccount($this->app);
    $mBusinessAccount->install();
    $mBusinessAccount->eloquent->create([
      'vat_id' => '123456',
      'company_id' => '987456',
      'tax_id' => '123987',
      'id_company' => $idCompany,
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
      "completed" => false,
    ]);

    $mActivityCategories = new Models\ActivityCategory($this->app);
    $mActivityCategories->install();
    $mActivityCategories->eloquent->create([
      'name' => "Category 1",
    ]);
    $mActivityCategories->eloquent->create([
      'name' => "Category 2",
    ]);
    $mActivityCategories->eloquent->create([
      'name' => "Category 3",
    ]);

    $mActivityCategoriesActivity = new Models\ActivityCategoryActivity($this->app);
    $mActivityCategoriesActivity->install();
  }
}