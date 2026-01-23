<?php

namespace Hubleto\App\Community\Customers;

class Loader extends \Hubleto\Framework\App
{

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^customers\/api\/get-customer\/?$/' => Controllers\Api\GetCustomer::class,
      '/^customers\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,

      '/^customers(\/(?<recordId>\d+))?\/?$/' => Controllers\Customers::class,
      '/^customers\/add\/?$/' => ['controller' => Controllers\Customers::class, 'vars' => ['recordId' => -1]],
      '/^customers\/tags\/?$/' => Controllers\Tags::class,
    ]);

    $this->addSearchSwitch('c', 'customers');

    /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'customers', $this->configAsString('calendarColor'), Calendar::class);

    /** @var \Hubleto\App\Community\Settings\Loader $settingsApp */
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Customer Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'customers/tags',
    ]);
  }

  /**
   * [Description for installTables]
   *
   * @param int $round
   * 
   * @return void
   * 
   */
  public function installTables(int $round): void
  {

    if ($round == 1) {
      $mCustomer = $this->getModel(Models\Customer::class);
      $mCustomerDocument = $this->getModel(Models\CustomerDocument::class);
      $mCustomerTag = $this->getModel(Models\Tag::class);
      $mCrossCustomerTag = $this->getModel(Models\CustomerTag::class);

      $mCustomer->dropTableIfExists()->install();
      $mCustomerTag->dropTableIfExists()->install();
      $mCrossCustomerTag->dropTableIfExists()->install();
      $mCustomerDocument->dropTableIfExists()->install();

      $mCustomerTag->record->recordCreate([ 'name' => "VIP", 'color' => '#D33115' ]);
      $mCustomerTag->record->recordCreate([ 'name' => "Partner", 'color' => '#4caf50' ]);
      $mCustomerTag->record->recordCreate([ 'name' => "Public", 'color' => '#2196f3' ]);
    }

    if ($round == 2) {
      $mCustomerActivity = $this->getModel(\Hubleto\App\Community\Customers\Models\CustomerActivity::class);
      $mCustomerActivity->dropTableIfExists()->install();
    }
  }

  /**
   * Implements fulltext search functionality for customers
   *
   * @param array $expressions List of expressions to be searched and glued with logical 'or'.
   * 
   * @return array
   * 
   */
  public function search(array $expressions): array
  {
    $mCustomer = $this->getModel(Models\Customer::class);
    $qCustomers = $mCustomer->record->prepareReadQuery();
    
    foreach ($expressions as $e) {
      $qCustomers = $qCustomers->where(function($q) use ($e) {
        $q->orWhere('customers.name', 'like', '%' . $e . '%');
        $q->orWhere('customers.city', 'like', '%' . $e . '%');
        $q->orWhere('customers.vat_id', 'like', '%' . $e . '%');
        $q->orWhere('customers.tax_id', 'like', '%' . $e . '%');
        $q->orWhere('customers.customer_id', 'like', '%' . $e . '%');
      });
    }

    $customers = $qCustomers->get()->toArray();

    $results = [];

    foreach ($customers as $customer) {
      $results[] = [
        "id" => $customer['id'],
        "label" => $customer['name'],
        "url" => 'customers/' . $customer['id'],
        "description" => $customer['customer_id'] . ' ' . $customer['city'],
      ];
    }

    return $results;
  }

}
