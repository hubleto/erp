<?php

namespace Hubleto\App\Community\Orders;

use Hubleto\App\Community\Documents\Models\Template;

class Loader extends \Hubleto\Framework\App
{

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^orders\/api\/generate-pdf\/?$/' => Controllers\Api\GeneratePdf::class,
      '/^orders\/api\/generate-invoice\/?$/' => Controllers\Api\GenerateInvoice::class,
      '/^orders\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,
      '/^orders\/api\/create-from-deal\/?$/' => Controllers\Api\CreateFromDeal::class,
      '/^orders\/api\/set-parent-deal\/?$/' => Controllers\Api\SetParentDeal::class,
      '/^orders\/api\/get-product\/?$/' => Controllers\Api\GetProduct::class,
      '/^orders\/api\/prepare-payment-for-invoice\/?$/' => Controllers\Api\PreparePaymentForInvoice::class,

      '/^orders\/boards\/order-warnings\/?$/' => Controllers\Boards\OrderWarnings::class,

      '/^orders\/?$/' => Controllers\Orders::class,
      '/^orders\/(?<recordId>\d+)\/?$/' => Controllers\Orders::class,

      '/^orders\/states\/?$/' => Controllers\States::class,
    ]);

    $this->addSearchSwitch('o', 'orders');

    /** @var \Hubleto\App\Community\Workflow\Manager $workflowManager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflow($this, 'orders', Workflow::class);

    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Order states'),
      'icon' => 'fas fa-file-lines',
      'url' => 'orders/states',
    ]);

    /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'orders', $this->configAsString('calendarColor'), Calendar::class);

    /** @var \Hubleto\App\Community\Dashboards\Manager $dashboardManager */
    $dashboardManager = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
    $dashboardManager->addBoard($this, $this->translate('Order warnings'), 'orders/boards/order-warnings');
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
      $this->getModel(Models\State::class)->dropTableIfExists()->install();
      $this->getModel(Models\Order::class)->dropTableIfExists()->install();
      $this->getModel(Models\OrderProduct::class)->dropTableIfExists()->install();
      $this->getModel(Models\OrderDeal::class)->dropTableIfExists()->install();
      $this->getModel(Models\OrderDocument::class)->dropTableIfExists()->install();
      $this->getModel(Models\OrderActivity::class)->dropTableIfExists()->install();
      $this->getModel(Models\Payment::class)->dropTableIfExists()->install();
      $this->getModel(Models\History::class)->dropTableIfExists()->install();
    }

    if ($round == 2) {
      $mState = $this->getModel(Models\State::class);
      $mState->record->recordCreate(['title' => 'New', 'code' => 'N', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Sent to customer', 'code' => 'S', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Requires modification', 'code' => 'M', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Accepted', 'code' => 'A', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Modified', 'code' => 'M', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Order created', 'code' => 'O', 'color' => '#444444']);
      $mState->record->recordCreate(['title' => 'Rejected', 'code' => 'R', 'color' => '#444444']);
    }
  }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   * 
   */
  public function renderSecondSidebar(): string
  {
    return '
      <div class="flex flex-col gap-2">
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/orders">
          <span class="icon"><i class="fas fa-money-check-dollar"></i></span>
          <span class="text">' . $this->translate('All orders') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/orders?view=purchaseOrders">
          <span class="icon"><i class="fas fa-cart-shopping"></i></span>
          <span class="text">' . $this->translate('Purchase orders') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/orders?view=salesOrders">
          <span class="icon"><i class="fas fa-euro-sign"></i></span>
          <span class="text">' . $this->translate('Sales orders') . '</span>
        </a>
      </div>
    ';
  }

  /**
   * [Description for generateDemoData]
   *
   * @return void
   * 
   */
  public function generateDemoData(): void
  {
    $mCustomer = $this->getModel(\Hubleto\App\Community\Customers\Models\Customer::class);
    $customerCount = $mCustomer->record->count();

    $mState = $this->getModel(Models\State::class);
    $stateCount = $mState->record->count();

    $mOrder = $this->getModel(Models\Order::class);
    $mHistory = $this->getModel(Models\History::class);
    $mOrderProduct = $this->getModel(Models\OrderProduct::class);
    $mTemplate = $this->getModel(Template::class);

    $idTemplate = $mTemplate->record->recordCreate([
      'name' => 'Demo template for order PDF',
      'content' => '
        <div>1 {{ identifier }}</div>
        <div>2 {{ title }}</div>
        <div>3 {{ CUSTOMER.first_name }}</div>
      '
    ])['id'];

    for ($i = 1; $i <= 9; $i++) {

      $idOrder = $mOrder->record->recordCreate([
        'id_customer' => rand(1, $customerCount),
        'id_state' => rand(1, $stateCount),
        'identifier' => 'O' . date('Y') . '-00' . $i,
        'title' => 'This is a test bid #' . $i,
        'price' => rand(1000, 2000) / rand(3, 5),
        'id_currency' => 1,
        'date_order' => date('Y-m-d', strtotime('-' . rand(0, 10) . ' days')),
        'id_template' => $idTemplate,
      ])['id'];

      $mHistory->record->recordCreate([ 'id_order' => $idOrder, 'short_description' => 'Order created', 'date_time' => date('Y-m-d H:i:s') ]);

      // for ($j = 0; $j <= rand(0, 3); $j++) {
      //   $mOrderProduct->record->recordCreate([
      //     'id_order' => $idOrder,
      //     'id_product' => rand(1, 5),
      //     'title' => 'Item #' . $i . '.' . $j,
      //     'amount' => rand(100, 200) / rand(3, 7),
      //     'sales_price' => rand(50, 80) / rand(2, 5),
      //   ]);
      // }
    }

  }

  /**
   * Implements fulltext search functionality for orders
   *
   * @param array $expressions List of expressions to be searched and glued with logical 'or'.
   * 
   * @return array
   * 
   */
  public function search(array $expressions): array
  {
    $mOrder = $this->getModel(Models\Order::class);
    $qOrders = $mOrder->record->prepareReadQuery();
    
    foreach ($expressions as $e) {
      $qOrders = $qOrders->where(function($q) use ($e) {
        $q->orWhere('orders.identifier', 'like', '%' . $e . '%');
        $q->orWhere('orders.title', 'like', '%' . $e . '%');
      })
      ->where('orders.is_closed', false);
    }

    $orders = $qOrders->get()->toArray();

    $results = [];

    foreach ($orders as $order) {
      $results[] = [
        "id" => $order['id'],
        "label" => $order['identifier'] . ' ' . $order['title'],
        "url" => 'orders/' . $order['id'],
      ];
    }

    return $results;
  }

}
