<?php

namespace HubletoApp\Community\Customers;

class Loader extends \HubletoMain\Core\App
{

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);

    $this->registerModel(Models\Company::class);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
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

    $this->main->sidebar->addLink(1, 40, 'customers/companies', $this->translate('Customers'), 'fas fa-address-card', str_starts_with($this->main->requestedUri, 'customers'));

    if (str_starts_with($this->main->requestedUri, 'customers')) {
      $this->main->sidebar->addHeading1(2, 310, $this->translate('Customers'));
      $this->main->sidebar->addLink(2, 320, 'customers/companies', $this->translate('Companies'), 'fas fa-building');
      $this->main->sidebar->addLink(2, 330, 'customers/persons', $this->translate('Contact Persons'), 'fas fa-users');
      //$this->main->sidebar->addLink(2, 10203, 'customers/activities', $this->translate('Activities'), 'fas fa-users');
    }

    $this->main->addCalendar(Calendar::class);
  }

  public function installTables() {
    $mPerson = new \HubletoApp\Community\Customers\Models\Person($this->main);
    $mCompany = new \HubletoApp\Community\Customers\Models\Company($this->main);
    $mAddress = new \HubletoApp\Community\Customers\Models\Address($this->main);
    $mContact = new \HubletoApp\Community\Customers\Models\Contact($this->main);
    //$mActivity = new \HubletoApp\Community\Customers\Models\Activity($this->main);
    $mCompanyActivity = new \HubletoApp\Community\Customers\Models\CompanyActivity($this->main);
    $mCompanyDocument = new \HubletoApp\Community\Customers\Models\CompanyDocument($this->main);
    $mCompanyTag = new \HubletoApp\Community\Customers\Models\CompanyTag($this->main);
    $mPersonTag = new \HubletoApp\Community\Customers\Models\PersonTag($this->main);

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
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Customers/Models/Activity:Create,Read,Update,Delete",
      "HubletoApp/Community/Customers/Models/Address:Create,Read,Update,Delete",
      "HubletoApp/Community/Customers/Models/Company:Create,Read,Update,Delete",
      "HubletoApp/Community/Customers/Models/CompanyTag:Create,Read,Update,Delete",
      "HubletoApp/Community/Customers/Models/Contact:Create,Read,Update,Delete",
      "HubletoApp/Community/Customers/Models/Person:Create,Read,Update,Delete",
      "HubletoApp/Community/Customers/Models/PersonTag:Create,Read,Update,Delete",

      "HubletoApp/Community/Customers/Controllers/Activity",
      "HubletoApp/Community/Customers/Controllers/Address",
      "HubletoApp/Community/Customers/Controllers/Company",
      "HubletoApp/Community/Customers/Controllers/CompanyTag",
      "HubletoApp/Community/Customers/Controllers/Contact",
      "HubletoApp/Community/Customers/Controllers/Person",
      "HubletoApp/Community/Customers/Controllers/PersonTag",
      "HubletoApp/Community/Customers/Controllers/Activity",

      "HubletoApp/Community/Customers/Addresses",
      "HubletoApp/Community/Customers/Companies",
      "HubletoApp/Community/Customers/CompanyTag",
      "HubletoApp/Community/Customers/Contacts",
      "HubletoApp/Community/Customers/Persons",
      "HubletoApp/Community/Customers/PersonTag",
      "HubletoApp/Community/Customers/Activity",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}