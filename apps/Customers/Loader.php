<?php

namespace CeremonyCrmMod\Customers;

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
      //'/^customers\/activities\/get\/?$/' => Controllers\Api\Activity::class,
      '/^customers\/get-company\/?$/' => Controllers\Api\GetCompany::class,
      '/^customers\/get-company-contacts\/?$/' => Controllers\Api\GetCompanyContacts::class,
      '/^customers\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
    ]);

    $this->app->sidebar->addLink(1, 40, 'customers/companies', $this->translate('Customers'), 'fas fa-address-card', str_starts_with($this->app->requestedUri, 'customers'));

    if (str_starts_with($this->app->requestedUri, 'customers')) {
      $this->app->sidebar->addHeading1(2, 310, $this->translate('Customers'));
      $this->app->sidebar->addLink(2, 320, 'customers/companies', $this->translate('Companies'), 'fas fa-building');
      $this->app->sidebar->addLink(2, 330, 'customers/persons', $this->translate('Contact Persons'), 'fas fa-users');
      //$this->app->sidebar->addLink(2, 10203, 'customers/activities', $this->translate('Activities'), 'fas fa-users');
    }

    $this->app->addCalendar(Calendar::class);
  }

  public function installTables() {
    $mPerson = new \CeremonyCrmMod\Customers\Models\Person($this->app);
    $mCompany = new \CeremonyCrmMod\Customers\Models\Company($this->app);
    $mAddress = new \CeremonyCrmMod\Customers\Models\Address($this->app);
    $mContact = new \CeremonyCrmMod\Customers\Models\Contact($this->app);
    //$mActivity = new \CeremonyCrmMod\Customers\Models\Activity($this->app);
    $mCompanyActivity = new \CeremonyCrmMod\Customers\Models\CompanyActivity($this->app);
    $mCompanyDocument = new \CeremonyCrmMod\Customers\Models\CompanyDocument($this->app);
    $mCompanyTag = new \CeremonyCrmMod\Customers\Models\CompanyTag($this->app);
    $mPersonTag = new \CeremonyCrmMod\Customers\Models\PersonTag($this->app);

    $mCompany->dropTableIfExists()->install();
    $mPerson->dropTableIfExists()->install();
    $mAddress->dropTableIfExists()->install();
    $mContact->dropTableIfExists()->install();
    $mCompanyTag->dropTableIfExists()->install();
    $mPersonTag->dropTableIfExists()->install();
    //$mActivity->dropTableIfExists()->install();
    $mCompanyActivity->dropTableIfExists()->install();
    $mCompanyDocument->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \CeremonyCrmMod\Settings\Models\Permission($this->app);
    $permissions = [
      "CeremonyCrmMod/Customers/Models/Activity:Create,Read,Update,Delete",
      "CeremonyCrmMod/Customers/Models/Address:Create,Read,Update,Delete",
      "CeremonyCrmMod/Customers/Models/Company:Create,Read,Update,Delete",
      "CeremonyCrmMod/Customers/Models/CompanyTag:Create,Read,Update,Delete",
      "CeremonyCrmMod/Customers/Models/Contact:Create,Read,Update,Delete",
      "CeremonyCrmMod/Customers/Models/Person:Create,Read,Update,Delete",
      "CeremonyCrmMod/Customers/Models/PersonTag:Create,Read,Update,Delete",

      "CeremonyCrmMod/Customers/Controllers/Activity",
      "CeremonyCrmMod/Customers/Controllers/Address",
      "CeremonyCrmMod/Customers/Controllers/Company",
      "CeremonyCrmMod/Customers/Controllers/CompanyTag",
      "CeremonyCrmMod/Customers/Controllers/Contact",
      "CeremonyCrmMod/Customers/Controllers/Person",
      "CeremonyCrmMod/Customers/Controllers/PersonTag",
      "CeremonyCrmMod/Customers/Controllers/Activity",

      "CeremonyCrmMod/Customers/Addresses",
      "CeremonyCrmMod/Customers/Companies",
      "CeremonyCrmMod/Customers/CompanyTag",
      "CeremonyCrmMod/Customers/Contacts",
      "CeremonyCrmMod/Customers/Persons",
      "CeremonyCrmMod/Customers/PersonTag",
      "CeremonyCrmMod/Customers/Activity",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}