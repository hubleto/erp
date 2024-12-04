<?php

namespace CeremonyCrmApp\Modules\Core\Customers;

use CeremonyCrmApp\Modules\Core\Settings\Models\Permission;
use CeremonyCrmApp\Modules\Core\Settings\Models\Tag;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);

    $this->registerModel(Models\Company::class);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^customers\/companies\/?$/' => Controllers\Companies::class,
      '/^customers\/persons\/?$/' => Controllers\Persons::class,
      '/^customers\/address\/?$/' => Controllers\Addresses::class,
      '/^customers\/contacts\/?$/' => Controllers\Contacts::class,
      '/^customers\/activities\/?$/' => Controllers\Activity::class,
      '/^customers\/activities\/get\/?$/' => Controllers\ActivityApi::class,
      '/^customers\/tags\/?$/' => Controllers\Tag::class,
      '/^customers\/documents\/?$/' => Controllers\Documents::class,
      '/^customers\/get-company\/?$/' => Controllers\GetCompany::class,
      '/^customers\/get-company-contacts\/?$/' => Controllers\GetCompanyContacts::class,
    ]);

    // $router(
      // 'customers',
      // 'CeremonyCrmApp/Modules/Core/Customers/Controllers',
      // '@app/Modules/Core/Customers/Views',
      // [
      //   'idAccount' => '$1',
      // ],
      // [
      //   '/companies' => 'Companies',
      //   '/persons' => 'Persons',
      //   '/address' => 'Addresses',
      //   '/contacts' => 'Contacts',
      //   '/activities' => 'Activity',
      //   '/activities/get' => 'ActivityApi',
      //   '/tags' => 'Tag',
      //   '/documents' => 'Documents',
      //   '/get-company' => 'GetCompany',
      //   '/get-company-contacts' => 'GetCompanyContacts',

      // ]
    // );
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

  public function createPermissions()
  {
    $mPermission = new Permission($this->app);
    $permissions = [
      "CeremonyCrmApp/Modules/Core/Customers/Models/Activity:Create",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Activity:Read",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Activity:Update",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Activity:Delete",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Address:Create",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Address:Read",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Address:Update",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Address:Delete",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Company:Create",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Company:Read",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Company:Update",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Company:Delete",
      "CeremonyCrmApp/Modules/Core/Customers/Models/CompanyTag:Create",
      "CeremonyCrmApp/Modules/Core/Customers/Models/CompanyTag:Read",
      "CeremonyCrmApp/Modules/Core/Customers/Models/CompanyTag:Update",
      "CeremonyCrmApp/Modules/Core/Customers/Models/CompanyTag:Delete",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Contact:Create",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Contact:Read",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Contact:Update",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Contact:Delete",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Person:Create",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Person:Read",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Person:Update",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Person:Delete",
      "CeremonyCrmApp/Modules/Core/Customers/Models/PersonTag:Create",
      "CeremonyCrmApp/Modules/Core/Customers/Models/PersonTag:Read",
      "CeremonyCrmApp/Modules/Core/Customers/Models/PersonTag:Update",
      "CeremonyCrmApp/Modules/Core/Customers/Models/PersonTag:Delete",

      "CeremonyCrmApp/Modules/Core/Customers/Controllers/Activity",
      "CeremonyCrmApp/Modules/Core/Customers/Controllers/Address",
      "CeremonyCrmApp/Modules/Core/Customers/Controllers/Company",
      "CeremonyCrmApp/Modules/Core/Customers/Controllers/CompanyTag",
      "CeremonyCrmApp/Modules/Core/Customers/Controllers/Contact",
      "CeremonyCrmApp/Modules/Core/Customers/Controllers/Person",
      "CeremonyCrmApp/Modules/Core/Customers/Controllers/PersonTag",
      "CeremonyCrmApp/Modules/Core/Customers/Controllers/Activity",

      "CeremonyCrmApp/Modules/Core/Customers/Addresses",
      "CeremonyCrmApp/Modules/Core/Customers/Companies",
      "CeremonyCrmApp/Modules/Core/Customers/CompanyTag",
      "CeremonyCrmApp/Modules/Core/Customers/Contacts",
      "CeremonyCrmApp/Modules/Core/Customers/Persons",
      "CeremonyCrmApp/Modules/Core/Customers/PersonTag",
      "CeremonyCrmApp/Modules/Core/Customers/Activity",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}