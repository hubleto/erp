<?php

namespace CeremonyCrmApp\Modules\Core\Customers;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public string $translationContext = 'mod.core.customers.loader';

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
      '/^customers\/get-company\/?$/' => Controllers\GetCompany::class,
      '/^customers\/get-company-contacts\/?$/' => Controllers\GetCompanyContacts::class,
    ]);

    $this->app->sidebar->addLink(1, 10100, 'customers/companies', $this->translate('Customers'), 'fas fa-address-card');

    if (str_starts_with($this->app->requestedUri, 'customers')) {
      $this->app->sidebar->addHeading1(2, 10200, $this->translate('Customers'));
      $this->app->sidebar->addLink(2, 10201, 'customers/companies', $this->translate('Companies'), 'fas fa-building');
      $this->app->sidebar->addLink(2, 10202, 'customers/persons', $this->translate('Contact Persons'), 'fas fa-users');
      //$this->app->sidebar->addLink(2, 10203, 'customers/activities', $this->translate('Activities'), 'fas fa-users');
    }
  }

  public function installTables() {
    $mPerson = new \CeremonyCrmApp\Modules\Core\Customers\Models\Person($this->app);
    $mCompany = new \CeremonyCrmApp\Modules\Core\Customers\Models\Company($this->app);
    $mAddress = new \CeremonyCrmApp\Modules\Core\Customers\Models\Address($this->app);
    $mContact = new \CeremonyCrmApp\Modules\Core\Customers\Models\Contact($this->app);
    $mActivity = new \CeremonyCrmApp\Modules\Core\Customers\Models\Activity($this->app);
    $mCompanyActivity = new \CeremonyCrmApp\Modules\Core\Customers\Models\CompanyActivity($this->app);
    $mCompanyDocument = new \CeremonyCrmApp\Modules\Core\Customers\Models\CompanyDocument($this->app);
    $mCompanyTag = new \CeremonyCrmApp\Modules\Core\Customers\Models\CompanyTag($this->app);
    $mPersonTag = new \CeremonyCrmApp\Modules\Core\Customers\Models\PersonTag($this->app);

    $mCompany->dropTableIfExists()->install();
    $mPerson->dropTableIfExists()->install();
    $mAddress->dropTableIfExists()->install();
    $mContact->dropTableIfExists()->install();
    $mCompanyTag->dropTableIfExists()->install();
    $mPersonTag->dropTableIfExists()->install();
    $mActivity->dropTableIfExists()->install();
    $mCompanyActivity->dropTableIfExists()->install();
    $mCompanyDocument->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \CeremonyCrmApp\Modules\Core\Settings\Models\Permission($this->app);
    $permissions = [
      "CeremonyCrmApp/Modules/Core/Customers/Models/Activity:Create,Read,Update,Delete",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Address:Create,Read,Update,Delete",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Company:Create,Read,Update,Delete",
      "CeremonyCrmApp/Modules/Core/Customers/Models/CompanyTag:Create,Read,Update,Delete",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Contact:Create,Read,Update,Delete",
      "CeremonyCrmApp/Modules/Core/Customers/Models/Person:Create,Read,Update,Delete",
      "CeremonyCrmApp/Modules/Core/Customers/Models/PersonTag:Create,Read,Update,Delete",

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