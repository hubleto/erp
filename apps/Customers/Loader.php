<?php

namespace HubletoApp\Customers;

class Loader extends \HubletoCore\Core\Module
{

  public function __construct(\HubletoCore $app)
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
    $mPerson = new \HubletoApp\Customers\Models\Person($this->app);
    $mCompany = new \HubletoApp\Customers\Models\Company($this->app);
    $mAddress = new \HubletoApp\Customers\Models\Address($this->app);
    $mContact = new \HubletoApp\Customers\Models\Contact($this->app);
    //$mActivity = new \HubletoApp\Customers\Models\Activity($this->app);
    $mCompanyActivity = new \HubletoApp\Customers\Models\CompanyActivity($this->app);
    $mCompanyDocument = new \HubletoApp\Customers\Models\CompanyDocument($this->app);
    $mCompanyTag = new \HubletoApp\Customers\Models\CompanyTag($this->app);
    $mPersonTag = new \HubletoApp\Customers\Models\PersonTag($this->app);

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
    $mPermission = new \HubletoApp\Settings\Models\Permission($this->app);
    $permissions = [
      "HubletoApp/Customers/Models/Activity:Create,Read,Update,Delete",
      "HubletoApp/Customers/Models/Address:Create,Read,Update,Delete",
      "HubletoApp/Customers/Models/Company:Create,Read,Update,Delete",
      "HubletoApp/Customers/Models/CompanyTag:Create,Read,Update,Delete",
      "HubletoApp/Customers/Models/Contact:Create,Read,Update,Delete",
      "HubletoApp/Customers/Models/Person:Create,Read,Update,Delete",
      "HubletoApp/Customers/Models/PersonTag:Create,Read,Update,Delete",

      "HubletoApp/Customers/Controllers/Activity",
      "HubletoApp/Customers/Controllers/Address",
      "HubletoApp/Customers/Controllers/Company",
      "HubletoApp/Customers/Controllers/CompanyTag",
      "HubletoApp/Customers/Controllers/Contact",
      "HubletoApp/Customers/Controllers/Person",
      "HubletoApp/Customers/Controllers/PersonTag",
      "HubletoApp/Customers/Controllers/Activity",

      "HubletoApp/Customers/Addresses",
      "HubletoApp/Customers/Companies",
      "HubletoApp/Customers/CompanyTag",
      "HubletoApp/Customers/Contacts",
      "HubletoApp/Customers/Persons",
      "HubletoApp/Customers/PersonTag",
      "HubletoApp/Customers/Activity",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}