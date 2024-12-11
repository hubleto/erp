<?php

namespace CeremonyCrmMod\Core\Customers;

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
    $mPerson = new \CeremonyCrmMod\Core\Customers\Models\Person($this->app);
    $mCompany = new \CeremonyCrmMod\Core\Customers\Models\Company($this->app);
    $mAddress = new \CeremonyCrmMod\Core\Customers\Models\Address($this->app);
    $mContact = new \CeremonyCrmMod\Core\Customers\Models\Contact($this->app);
    $mActivity = new \CeremonyCrmMod\Core\Customers\Models\Activity($this->app);
    $mCompanyActivity = new \CeremonyCrmMod\Core\Customers\Models\CompanyActivity($this->app);
    $mCompanyDocument = new \CeremonyCrmMod\Core\Customers\Models\CompanyDocument($this->app);
    $mCompanyTag = new \CeremonyCrmMod\Core\Customers\Models\CompanyTag($this->app);
    $mPersonTag = new \CeremonyCrmMod\Core\Customers\Models\PersonTag($this->app);

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
    $mPermission = new \CeremonyCrmMod\Core\Settings\Models\Permission($this->app);
    $permissions = [
      "CeremonyCrmMod/Core/Customers/Models/Activity:Create,Read,Update,Delete",
      "CeremonyCrmMod/Core/Customers/Models/Address:Create,Read,Update,Delete",
      "CeremonyCrmMod/Core/Customers/Models/Company:Create,Read,Update,Delete",
      "CeremonyCrmMod/Core/Customers/Models/CompanyTag:Create,Read,Update,Delete",
      "CeremonyCrmMod/Core/Customers/Models/Contact:Create,Read,Update,Delete",
      "CeremonyCrmMod/Core/Customers/Models/Person:Create,Read,Update,Delete",
      "CeremonyCrmMod/Core/Customers/Models/PersonTag:Create,Read,Update,Delete",

      "CeremonyCrmMod/Core/Customers/Controllers/Activity",
      "CeremonyCrmMod/Core/Customers/Controllers/Address",
      "CeremonyCrmMod/Core/Customers/Controllers/Company",
      "CeremonyCrmMod/Core/Customers/Controllers/CompanyTag",
      "CeremonyCrmMod/Core/Customers/Controllers/Contact",
      "CeremonyCrmMod/Core/Customers/Controllers/Person",
      "CeremonyCrmMod/Core/Customers/Controllers/PersonTag",
      "CeremonyCrmMod/Core/Customers/Controllers/Activity",

      "CeremonyCrmMod/Core/Customers/Addresses",
      "CeremonyCrmMod/Core/Customers/Companies",
      "CeremonyCrmMod/Core/Customers/CompanyTag",
      "CeremonyCrmMod/Core/Customers/Contacts",
      "CeremonyCrmMod/Core/Customers/Persons",
      "CeremonyCrmMod/Core/Customers/PersonTag",
      "CeremonyCrmMod/Core/Customers/Activity",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}