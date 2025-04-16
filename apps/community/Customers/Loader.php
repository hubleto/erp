<?php

namespace HubletoApp\Community\Customers;

class Loader extends \HubletoMain\Core\App
{

  public bool $hasCustomSettings = true;

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^customers(\/(?<recordId>\d+))?\/?$/' => Controllers\Customers::class,
      '/^customers\/settings\/?$/' => Controllers\Settings::class,
      '/^customers\/activities\/?$/' => Controllers\Activity::class,
      '/^customers\/get-customer\/?$/' => Controllers\Api\GetCustomer::class,
      '/^customers\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^settings\/customer-tags\/?$/' => Controllers\Tags::class,
    ]);

    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;
    $calendarManager->addCalendar(Calendar::class);

    $this->main->apps->community('Help')->addContextHelpUrls('/^customers\/?$/', [
      'en' => 'en/apps/community/customers',
    ]);

    $this->main->addSetting($this, [
      'title' => $this->translate('Customer Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/customer-tags',
    ]);
  }

  public function installTables(int $round): void
  {

    if ($round == 1) {
      $mCustomer = new \HubletoApp\Community\Customers\Models\Customer($this->main);
      $mCustomerDocument = new \HubletoApp\Community\Customers\Models\CustomerDocument($this->main);
      $mCustomerTag = new \HubletoApp\Community\Customers\Models\Tag($this->main);
      $mCrossCustomerTag = new \HubletoApp\Community\Customers\Models\CustomerTag($this->main);

      $mCustomer->dropTableIfExists()->install();
      $mCustomerTag->dropTableIfExists()->install();
      $mCrossCustomerTag->dropTableIfExists()->install();
      $mCustomerDocument->dropTableIfExists()->install();

      $mCustomerTag->record->recordCreate([ 'name' => "VIP", 'color' => '#fc2c03' ]);
      $mCustomerTag->record->recordCreate([ 'name' => "Partner", 'color' => '#62fc03' ]);
      $mCustomerTag->record->recordCreate([ 'name' => "Public", 'color' => '#033dfc' ]);
    }

    if ($round == 2) {
      $mCustomerActivity = new \HubletoApp\Community\Customers\Models\CustomerActivity($this->main);
      $mCustomerActivity->dropTableIfExists()->install();
    }
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Customers/Models/CustomerActivity:Create",
      "HubletoApp/Community/Customers/Models/CustomerActivity:Read",
      "HubletoApp/Community/Customers/Models/CustomerActivity:Update",
      "HubletoApp/Community/Customers/Models/CustomerActivity:Delete",

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
      $mPermission->record->recordCreate([
        "permission" => $permission
      ]);
    }
  }
}