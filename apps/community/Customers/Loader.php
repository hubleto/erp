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
      "HubletoApp/Community/Customers/Models/CompanyActivity:Create" => "CompanyActivity/Create",
      "HubletoApp/Community/Customers/Models/CompanyActivity:Read" => "CompanyActivity/Read",
      "HubletoApp/Community/Customers/Models/CompanyActivity:Update" => "CompanyActivity/Update",
      "HubletoApp/Community/Customers/Models/CompanyActivity:Delete" => "CompanyActivity/Delete",

      "HubletoApp/Community/Customers/Models/Address:Create" => "Address/Create",
      "HubletoApp/Community/Customers/Models/Address:Read" => "Address/Read",
      "HubletoApp/Community/Customers/Models/Address:Update" => "Address/Update",
      "HubletoApp/Community/Customers/Models/Address:Delete" => "Address/Delete",

      "HubletoApp/Community/Customers/Models/Company:Create" => "Company/Create",
      "HubletoApp/Community/Customers/Models/Company:Read" => "Company/Read",
      "HubletoApp/Community/Customers/Models/Company:Update" => "Company/Update",
      "HubletoApp/Community/Customers/Models/Company:Delete" => "Company/Delete",

      "HubletoApp/Community/Customers/Models/CompanyTag:Create" => "CompanyTag/Create",
      "HubletoApp/Community/Customers/Models/CompanyTag:Read" => "CompanyTag/Read",
      "HubletoApp/Community/Customers/Models/CompanyTag:Update" => "CompanyTag/Update",
      "HubletoApp/Community/Customers/Models/CompanyTag:Delete" => "CompanyTag/Delete",

      "HubletoApp/Community/Customers/Models/Contact:Create" => "Contact/Create",
      "HubletoApp/Community/Customers/Models/Contact:Read" => "Contact/Read",
      "HubletoApp/Community/Customers/Models/Contact:Update" => "Contact/Update",
      "HubletoApp/Community/Customers/Models/Contact:Delete" => "Contact/Delete",

      "HubletoApp/Community/Customers/Models/Person:Create" => "Person/Create",
      "HubletoApp/Community/Customers/Models/Person:Read" => "Person/Read",
      "HubletoApp/Community/Customers/Models/Person:Update" => "Person/Update",
      "HubletoApp/Community/Customers/Models/Person:Delete" => "Person/Delete",

      "HubletoApp/Community/Customers/Models/PersonTag:Create" => "PersonTag/Create",
      "HubletoApp/Community/Customers/Models/PersonTag:Read" => "PersonTag/Read",
      "HubletoApp/Community/Customers/Models/PersonTag:Update" => "PersonTag/Update",
      "HubletoApp/Community/Customers/Models/PersonTag:Delete" => "PersonTag/Delete",

      "HubletoApp/Community/Customers/Controllers/Company" => "Company/Controller",
      "HubletoApp/Community/Customers/Controllers/CompanyActivity" => "CompanyActivity/Controller",
      "HubletoApp/Community/Customers/Controllers/Address" => "Address/Controller",
      "HubletoApp/Community/Customers/Controllers/CompanyTag" => "CompanyTag/Controller",
      "HubletoApp/Community/Customers/Controllers/Contact" => "Contact/Controller",
      "HubletoApp/Community/Customers/Controllers/Person" => "Person/Controller",
      "HubletoApp/Community/Customers/Controllers/PersonTag" => "PersonTag/Controller",
      "HubletoApp/Community/Customers/Controllers/CompanyActivity" => "CompanyActivity/Controller",
      "HubletoApp/Community/Customers/Controllers/Company" => "Company/Controller",

      "HubletoApp/Community/Customers/Api/GetCalendarEvents" => "Company/Api/GetCalendarEvents",
      "HubletoApp/Community/Customers/Api/GetCompany" => "Company/Api/GetCompany",
      "HubletoApp/Community/Customers/Api/GetCompanyContacts" => "Company/Api/GetCompanyContacts",

      "HubletoApp/Community/Customers/Companies" => "Company",
      "HubletoApp/Community/Customers/Addresses" => "Address",
      "HubletoApp/Community/Customers/CompanyTag" => "CompanyTag",
      "HubletoApp/Community/Customers/Contacts" => "Contact",
      "HubletoApp/Community/Customers/Persons" => "Person",
      "HubletoApp/Community/Customers/PersonTag" => "PersonTag",
      "HubletoApp/Community/Customers/CompanyActivity" => "CompanyActivity",
    ];

    foreach ($permissions as $permission => $allias) {
      $mPermission->eloquent->create([
        "permission" => $permission,
        "allias" => $allias,
      ]);
    }
  }
}