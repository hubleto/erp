<?php

namespace HubletoApp\Community\Customers;

class Loader extends \HubletoMain\Core\App
{

  // public function __construct(\HubletoMain $main)
  // {
  //   parent::__construct($main);

  //   $this->registerModel(Models\Customer::class);
  // }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^customers\/customers\/?$/' => Controllers\Customers::class,
      '/^customers\/persons\/?$/' => Controllers\Persons::class,
      '/^customers\/address\/?$/' => Controllers\Addresses::class,
      '/^customers\/contacts\/?$/' => Controllers\Contacts::class,
      '/^customers\/activities\/?$/' => Controllers\Activity::class,
      //'/^customers\/activities\/get\/?$/' => Controllers\Api\Activity::class,
      '/^customers\/get-customer\/?$/' => Controllers\Api\GetCustomer::class,
      '/^customers\/get-customer-contacts\/?$/' => Controllers\Api\GetCustomerContacts::class,
      '/^customers\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
    ]);

    $this->main->sidebar->addLink(1, 40, 'customers/customers', $this->translate('Customers'), 'fas fa-address-card', str_starts_with($this->main->requestedUri, 'customers'));

    if (str_starts_with($this->main->requestedUri, 'customers')) {
      $this->main->sidebar->addHeading1(2, 310, $this->translate('Customers'));
      $this->main->sidebar->addLink(2, 320, 'customers/customers', $this->translate('Customers'), 'fas fa-building');
      $this->main->sidebar->addLink(2, 330, 'customers/persons', $this->translate('Contact Persons'), 'fas fa-users');
      //$this->main->sidebar->addLink(2, 10203, 'customers/activities', $this->translate('Activities'), 'fas fa-users');
    }

    $this->main->calendarManager->addCalendar(Calendar::class);
  }

  public function installTables(): void
  {
    $mPerson = new \HubletoApp\Community\Customers\Models\Person($this->main);
    $mCustomer = new \HubletoApp\Community\Customers\Models\Customer($this->main);
    $mAddress = new \HubletoApp\Community\Customers\Models\Address($this->main);
    $mContact = new \HubletoApp\Community\Customers\Models\Contact($this->main);
    //$mActivity = new \HubletoApp\Community\Customers\Models\Activity($this->main);
    $mCustomerActivity = new \HubletoApp\Community\Customers\Models\CustomerActivity($this->main);
    $mCustomerDocument = new \HubletoApp\Community\Customers\Models\CustomerDocument($this->main);
    $mCustomerTag = new \HubletoApp\Community\Customers\Models\CustomerTag($this->main);
    $mPersonTag = new \HubletoApp\Community\Customers\Models\PersonTag($this->main);

    $mCustomer->dropTableIfExists()->install();
    $mPerson->dropTableIfExists()->install();
    $mAddress->dropTableIfExists()->install();
    $mContact->dropTableIfExists()->install();
    $mCustomerTag->dropTableIfExists()->install();
    $mPersonTag->dropTableIfExists()->install();
    //$mActivity->dropTableIfExists()->install();
    $mCustomerActivity->dropTableIfExists()->install();
    $mCustomerDocument->dropTableIfExists()->install();
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Customers/Models/CustomerActivity:Create",
      "HubletoApp/Community/Customers/Models/CustomerActivity:Read",
      "HubletoApp/Community/Customers/Models/CustomerActivity:Update",
      "HubletoApp/Community/Customers/Models/CustomerActivity:Delete",

      "HubletoApp/Community/Customers/Models/Address:Create",
      "HubletoApp/Community/Customers/Models/Address:Read",
      "HubletoApp/Community/Customers/Models/Address:Update",
      "HubletoApp/Community/Customers/Models/Address:Delete",

      "HubletoApp/Community/Customers/Models/Customer:Create",
      "HubletoApp/Community/Customers/Models/Customer:Read",
      "HubletoApp/Community/Customers/Models/Customer:Update",
      "HubletoApp/Community/Customers/Models/Customer:Delete",

      "HubletoApp/Community/Customers/Models/CustomerTag:Create",
      "HubletoApp/Community/Customers/Models/CustomerTag:Read",
      "HubletoApp/Community/Customers/Models/CustomerTag:Update",
      "HubletoApp/Community/Customers/Models/CustomerTag:Delete",

      "HubletoApp/Community/Customers/Models/Contact:Create",
      "HubletoApp/Community/Customers/Models/Contact:Read",
      "HubletoApp/Community/Customers/Models/Contact:Update",
      "HubletoApp/Community/Customers/Models/Contact:Delete",

      "HubletoApp/Community/Customers/Models/Person:Create",
      "HubletoApp/Community/Customers/Models/Person:Read",
      "HubletoApp/Community/Customers/Models/Person:Update",
      "HubletoApp/Community/Customers/Models/Person:Delete",

      "HubletoApp/Community/Customers/Models/PersonTag:Create",
      "HubletoApp/Community/Customers/Models/PersonTag:Read",
      "HubletoApp/Community/Customers/Models/PersonTag:Update",
      "HubletoApp/Community/Customers/Models/PersonTag:Delete",

      "HubletoApp/Community/Customers/Controllers/Customer",
      "HubletoApp/Community/Customers/Controllers/CustomerActivity",
      "HubletoApp/Community/Customers/Controllers/Address",
      "HubletoApp/Community/Customers/Controllers/CustomerTag",
      "HubletoApp/Community/Customers/Controllers/Contact",
      "HubletoApp/Community/Customers/Controllers/Person",
      "HubletoApp/Community/Customers/Controllers/PersonTag",
      "HubletoApp/Community/Customers/Controllers/CustomerActivity",
      "HubletoApp/Community/Customers/Controllers/Customer",

      "HubletoApp/Community/Customers/Api/GetCalendarEvents",
      "HubletoApp/Community/Customers/Api/GetCustomer",
      "HubletoApp/Community/Customers/Api/GetCustomerContacts",

      "HubletoApp/Community/Customers/Customers",
      "HubletoApp/Community/Customers/Persons",
    ];

    foreach ($permissions as $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}