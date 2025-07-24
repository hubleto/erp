<?php

namespace HubletoApp\Community\Customers;

class Loader extends \Hubleto\Framework\App
{
  public bool $hasCustomSettings = true;

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^customers(\/(?<recordId>\d+))?\/?$/' => Controllers\Customers::class,
      '/^customers\/add\/?$/' => ['controller' => Controllers\Customers::class, 'vars' => ['recordId' => -1]],
      '/^customers\/settings\/?$/' => Controllers\Settings::class,
      '/^customers\/activities\/?$/' => Controllers\Activity::class,
      '/^settings\/customer-tags\/?$/' => Controllers\Tags::class,

      '/^customers\/api\/get-customer\/?$/' => Controllers\Api\GetCustomer::class,
      // '/^customers\/api\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^customers\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,
    ]);

    $calendarManager = $this->main->apps->community('Calendar')?->calendarManager?->addCalendar(
      'customers',
      $this->configAsString('calendarColor'),
      Calendar::class
    );

    $this->main->apps->community('Help')?->addContextHelpUrls('/^customers\/?$/', [
      'en' => 'en/apps/community/customers',
    ]);

    $this->main->apps->community('Settings')?->addSetting($this, [
      'title' => $this->translate('Customer Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/customer-tags',
    ]);
  }

  public function installTables(int $round): void
  {

    if ($round == 1) {
      $mCustomer = $this->main->di->create(\HubletoApp\Community\Customers\Models\Customer::class);
      $mCustomerDocument = $this->main->di->create(\HubletoApp\Community\Customers\Models\CustomerDocument::class);
      $mCustomerTag = $this->main->di->create(\HubletoApp\Community\Customers\Models\Tag::class);
      $mCrossCustomerTag = $this->main->di->create(\HubletoApp\Community\Customers\Models\CustomerTag::class);

      $mCustomer->dropTableIfExists()->install();
      $mCustomerTag->dropTableIfExists()->install();
      $mCrossCustomerTag->dropTableIfExists()->install();
      $mCustomerDocument->dropTableIfExists()->install();

      $mCustomerTag->record->recordCreate([ 'name' => "VIP", 'color' => '#D33115' ]);
      $mCustomerTag->record->recordCreate([ 'name' => "Partner", 'color' => '#4caf50' ]);
      $mCustomerTag->record->recordCreate([ 'name' => "Public", 'color' => '#2196f3' ]);
    }

    if ($round == 2) {
      $mCustomerActivity = $this->main->di->create(\HubletoApp\Community\Customers\Models\CustomerActivity::class);
      $mCustomerActivity->dropTableIfExists()->install();
    }
  }

  // public function installDefaultPermissions(): void
  // {
  //   $mPermission = $this->main->di->create(\HubletoApp\Community\Settings\Models\Permission::class);
  //   $permissions = [
  //     "HubletoApp/Community/Customers/Models/CustomerActivity:Create",
  //     "HubletoApp/Community/Customers/Models/CustomerActivity:Read",
  //     "HubletoApp/Community/Customers/Models/CustomerActivity:Update",
  //     "HubletoApp/Community/Customers/Models/CustomerActivity:Delete",

  //     "HubletoApp/Community/Customers/Models/Customer:Create",
  //     "HubletoApp/Community/Customers/Models/Customer:Read",
  //     "HubletoApp/Community/Customers/Models/Customer:Update",
  //     "HubletoApp/Community/Customers/Models/Customer:Delete",

  //     "HubletoApp/Community/Customers/Models/CustomerTag:Create",
  //     "HubletoApp/Community/Customers/Models/CustomerTag:Read",
  //     "HubletoApp/Community/Customers/Models/CustomerTag:Update",
  //     "HubletoApp/Community/Customers/Models/CustomerTag:Delete",

  //     "HubletoApp/Community/Customers/Models/Value:Create",
  //     "HubletoApp/Community/Customers/Models/Value:Read",
  //     "HubletoApp/Community/Customers/Models/Value:Update",
  //     "HubletoApp/Community/Customers/Models/Value:Delete",

  //     "HubletoApp/Community/Customers/Models/Contact:Create",
  //     "HubletoApp/Community/Customers/Models/Contact:Read",
  //     "HubletoApp/Community/Customers/Models/Contact:Update",
  //     "HubletoApp/Community/Customers/Models/Contact:Delete",

  //     "HubletoApp/Community/Customers/Models/Tag:Create",
  //     "HubletoApp/Community/Customers/Models/Tag:Read",
  //     "HubletoApp/Community/Customers/Models/Tag:Update",
  //     "HubletoApp/Community/Customers/Models/Tag:Delete",

  //     "HubletoApp/Community/Customers/Controllers/Customer",
  //     "HubletoApp/Community/Customers/Controllers/CustomerActivity",
  //     "HubletoApp/Community/Customers/Controllers/Address",
  //     "HubletoApp/Community/Customers/Controllers/CustomerTag",
  //     "HubletoApp/Community/Customers/Controllers/Contact",
  //     "HubletoApp/Community/Customers/Controllers/ContactTag",
  //     "HubletoApp/Community/Customers/Controllers/CustomerActivity",
  //     "HubletoApp/Community/Customers/Controllers/Customer",

  //     "HubletoApp/Community/Customers/Api/GetCalendarEvents",
  //     "HubletoApp/Community/Customers/Api/GetCustomer",
  //     "HubletoApp/Community/Customers/Api/GetCustomerContacts",

  //     "HubletoApp/Community/Customers/Customers",
  //     "HubletoApp/Community/Customers/Contacts",
  //   ];

  //   foreach ($permissions as $permission) {
  //     $mPermission->record->recordCreate([
  //       "permission" => $permission
  //     ]);
  //   }
  // }
}
