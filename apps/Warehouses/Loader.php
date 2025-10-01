<?php

namespace Hubleto\App\Community\Warehouses;

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
      '/^warehouses(\/(?<recordId>\d+))?\/?$/' => Controllers\Warehouses::class,
      '/^warehouses\/add?\/?$/' => ['controller' => Controllers\Warehouses::class, 'vars' => [ 'recordId' => -1 ]],

      '/^warehouses\/locations(\/(?<recordId>\d+))?\/?$/' => Controllers\Locations::class,
      '/^warehouses\/locations\/add?\/?$/' => ['controller' => Controllers\Locations::class, 'vars' => [ 'recordId' => -1 ]],

      '/^warehouses\/inventory\/?$/' => Controllers\Inventory::class,

      '/^warehouses\/transactions(\/(?<recordId>\d+))?\/?$/' => Controllers\Transactions::class,
      '/^warehouses\/transactions\/add\/?$/' => [ 'controller' => Controllers\Transactions::class, 'vars' => [ 'recordId' => -1 ] ],

      '/^warehouses\/transactions\/items(\/(?<recordId>\d+))?\/?$/' => Controllers\TransactionItems::class,
      '/^warehouses\/transactions\/items\/add\/?$/' => [ 'controller' => Controllers\TransactionItems::class, 'vars' => [ 'recordId' => -1 ] ],

      '/^warehouses\/settings\/warehouse-types(\/(?<recordId>\d+))?\/?$/' => Controllers\WarehouseTypes::class,
      '/^warehouses\/settings\/warehouse-types\/add?\/?$/' => ['controller' => Controllers\WarehouseTypes::class, 'vars' => [ 'recordId' => -1 ]],

      '/^warehouses\/settings\/location-types(\/(?<recordId>\d+))?\/?$/' => Controllers\LocationTypes::class,
      '/^warehouses\/settings\/location-types\/add?\/?$/' => ['controller' => Controllers\LocationTypes::class, 'vars' => [ 'recordId' => -1 ]],
    ]);

    /** @var \Hubleto\App\Community\Settings\Loader */
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Warehouse types'),
      'icon' => 'fas fa-building',
      'url' => 'warehouses/settings/warehouse-types',
    ]);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Locations types'),
      'icon' => 'fas fa-building',
      'url' => 'warehouses/settings/location-types',
    ]);

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
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/warehouses">
          <span class="icon"><i class="fas fa-warehouse"></i></span>
          <span class="text">' . $this->translate('Warehouses') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/warehouses/locations">
          <span class="icon"><i class="fas fa-pallet"></i></span>
          <span class="text">' . $this->translate('Locations') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/warehouses/inventory">
          <span class="icon"><i class="fas fa-boxes-stacked"></i></span>
          <span class="text">' . $this->translate('Inventory') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/warehouses/transactions">
          <span class="icon"><i class="fas fa-arrows-turn-to-dots"></i></span>
          <span class="text">' . $this->translate('Transactions') . '</span>
        </a>
        <a class="btn btn-transparent btn-small ml-4" href="' . $this->env()->projectUrl . '/warehouses/transactions/add?direction=1">
          <span class="icon"><i class="fas fa-plus"></i></span>
          <span class="text">' . $this->translate('Create inbound') . '</span>
        </a>
        <a class="btn btn-transparent btn-small ml-4" href="' . $this->env()->projectUrl . '/warehouses/transactions/add?direction=2">
          <span class="icon"><i class="fas fa-minus"></i></span>
          <span class="text">' . $this->translate('Create outbound') . '</span>
        </a>
        <a class="btn btn-transparent btn-small ml-4" href="' . $this->env()->projectUrl . '/warehouses/transactions/items">
          <span class="icon"><i class="fas fa-list"></i></span>
          <span class="text">' . $this->translate('All items') . '</span>
        </a>
      </div>
    ';
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
      $this->getModel(Models\WarehouseType::class)->dropTableIfExists()->install();
      $this->getModel(Models\LocationType::class)->dropTableIfExists()->install();
      $this->getModel(Models\Warehouse::class)->dropTableIfExists()->install();
      $this->getModel(Models\Location::class)->dropTableIfExists()->install();
      $this->getModel(Models\Inventory::class)->dropTableIfExists()->install();
      $this->getModel(Models\Transaction::class)->dropTableIfExists()->install();
      $this->getModel(Models\TransactionItem::class)->dropTableIfExists()->install();
    }
    if ($round == 2) {
      $mLocationType = $this->getModel(Models\LocationType::class);
      $mLocationType->record->recordCreate(['name' => 'Area']);
      $mLocationType->record->recordCreate(['name' => 'Aisle']);
      $mLocationType->record->recordCreate(['name' => 'Rack']);
      $mLocationType->record->recordCreate(['name' => 'Shelf']);
      $mLocationType->record->recordCreate(['name' => 'Bin']);
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    $mWarehouseType = $this->getModel(Models\WarehouseType::class);
    $mLocationType = $this->getModel(Models\LocationType::class);
    $mWarehouse = $this->getModel(Models\Warehouse::class);
    $mLocation = $this->getModel(Models\Location::class);
    $mTransaction = $this->getModel(Models\Transaction::class);
    $mTransactionItem = $this->getModel(Models\TransactionItem::class);

    /** @var StockStatus */
    $stockStatus = $this->getService(StockStatus::class);

    $mProduct = $this->getModel(\Hubleto\App\Community\Products\Models\Product::class);
    $idsProduct = $mProduct->record->pluck('id');

    $idWarehouseTypeMain = $mWarehouseType->record->recordCreate(['name' => 'Main'])['id'];
    $idWarehouseTypeRegional = $mWarehouseType->record->recordCreate(['name' => 'Regional'])['id'];
    $idWarehouseTypeExternal = $mWarehouseType->record->recordCreate(['name' => 'External'])['id'];

    $idWarehouseMain = $mWarehouse->record->recordCreate([
      'name' => 'Main Distribution Center',
      'id_type' => $idWarehouseTypeMain,
      'operational_status' => Models\Warehouse::OPERATIONAL_STATUS_ACTIVE,
      'address' => '123 Warehouse St, Anytown, CA, 90210, USA',
      'address_plus_code' => 'JCJF+4CG',
      'contact_person' => 'John Doe',
      'contact_email' => 'john.doe@warehouse.example.com',
      'contacct_phone' => '+1 555-123-4562',
      'description' => 'Main warehouse used for supplying the most important customers.',
      'capacity' => 5400,
      'capacity_unit' => 'm2',
      'current_stock_status' => 1240,
      'id_operation_manager' => 1,
    ])['id'];

    $idWarehouseRegional = $mWarehouse->record->recordCreate([
      'name' => 'Regional Hub East',
      'id_type' => $idWarehouseTypeRegional,
      'operational_status' => Models\Warehouse::OPERATIONAL_STATUS_ACTIVE,
      'address' => '456 Industrial Rd, Eastville, NY, 10001, USA',
      'address_plus_code' => '7XWG+MMR',
      'contact_person' => 'Jane Smith',
      'contact_email' => 'jane.smith@warehouse.example.com',
      'contacct_phone' => '+1 435-332-4332',
      'description' => 'Regional warehouse used for supplying the regional customers.',
      'id_operation_manager' => 1,
    ])['id'];

    $idsLocation = [];

    $idsLocation[0] = $mLocation->record->recordCreate([
      'id_warehouse' => $idWarehouseMain,
      'code' => 'A1.1',
      'id_type' => 2,
      'capacity' => 230,
      'current_stock_status' => 15,
      'id_operation_manager' => 1,
    ])['id'];

    $idsLocation[1] = $mLocation->record->recordCreate([
      'id_warehouse' => $idWarehouseMain,
      'code' => 'A1.2',
      'id_type' => 2,
      'capacity' => 340,
      'current_stock_status' => 156,
      'id_operation_manager' => 1,
    ])['id'];

    for ($i = 1; $i < 100; $i++) {
      $idTransaction = $mTransaction->record->recordCreate([
        'direction' => rand(0, 1) == 0 ? Models\Transaction::DIRECTION_OUTBOUND : Models\Transaction::DIRECTION_INBOUND,
        'batch_number' => 'DEMO-' . $i,
      ])['id'];

      for ($j = 1; $j < rand(3, 5); $j++) {
        $mTransactionItem->record->recordCreate([
          'id_transaction' => $idTransaction,
          'id_product' => $idsProduct[rand(0, count($idsProduct) - 1)],
          'purchase_price' => rand(100, 600) / 2,
          'quantity' => rand(10, 500),
          'id_location_original' => 0,
          'id_location_new' => $idsLocation[rand(0, count($idsLocation) - 1)],
        ]);
      }
    }

    $stockStatus->recalculateCapacityAndStockStatusOfWarehouse($idWarehouseMain);
  }

}
