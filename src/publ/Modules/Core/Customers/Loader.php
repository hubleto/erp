<?php

namespace CeremonyCrmApp\Modules\Core\Customers;

use CeremonyCrmApp\Modules\Core\Settings\Models\Country;

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
        '/companies/table-companies' => 'Company/TableCompanies',
        '/persons' => 'Persons',
        '/persons/table-persons' => 'Person/TablePersons',
        '/address' => 'Addresses',
        '/contacts' => 'Contacts',
        '/billing-accounts' => 'BillingAccounts',
        '/activities' => 'Activity',
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
      $sidebar->addLink(2, 10202, 'customers/companies', $this->app->translate('Companies'), 'fas fa-warehouse');
      $sidebar->addLink(2, 10203, 'customers/persons', $this->app->translate('Persons'), 'fas fa-users');
      $sidebar->addLink(2, 10204, 'customers/address', $this->app->translate('Person Addresses'), 'fas fa-map-pin');
      $sidebar->addLink(2, 10205, 'customers/Contacts', $this->app->translate('Persons Contacts'), 'fas fa-address-book');
      $sidebar->addLink(2, 10206, 'customers/billing-accounts', $this->app->translate('Billing Accounts'), 'fas fa-business-time');
      $sidebar->addLink(2, 10207, 'customers/activities', $this->app->translate('Activities'), 'fas fa-check');
      $sidebar->addLink(2, 10208, 'customers/tags', $this->app->translate('Tags'), 'fas fa-bars');
    }
  }

  public function generateTestData()
  {
    $mCountry = new Country($this->app);
    $mCountry->install();
    $countries = [
      ['country' => 'United States', 'code' => 'US'],
      ['country' => 'Canada', 'code' => 'CA'],
      ['country' => 'United Kingdom', 'code' => 'UK'],
      ['country' => 'Australia', 'code' => 'AU'],
      ['country' => 'Germany', 'code' => 'DE'],
      ['country' => 'Slovakia', 'code' => 'SK'],
  ];

  foreach ($countries as $country) {
    $mCountry->eloquent->create($country);
  }

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
    ])->id;

    $mPerson = new Models\Person($this->app);
    $mPerson->install();
    $idPerson = $mPerson->eloquent->create([
      'first_name' => 'John',
      'last_name' => 'Smith',
      'id_company' => $idCompany,
      'is_primary' => true,
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
      'country' => 'Slovakia',
      'id_person' => $idPerson,
    ]);

    $mBillingAccount = new Models\BillingAccount($this->app);
    $mBillingAccount->install();
    $mBillingAccount->eloquent->create([
      'id_company' => $idCompany,
      "name" => "Test Business Account"
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
      'id_activity_tag' => 1,
    ]);

    $mAtendance = new Models\Atendance($this->app);
    $mAtendance->install();
    $mAtendance->eloquent->create([
      'id_user' => 1,
      'id_activity' => 1,
    ]);
  }
}