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
      '/^warehouses\/?$/' => Controllers\Warehouses::class,
      '/^warehouses\/locations\/?$/' => Controllers\Locations::class,
      '/^warehouses\/inventory\/?$/' => Controllers\Inventory::class,
      '/^warehouses\/transactions\/?$/' => Controllers\Transactions::class,
      '/^warehouses\/settings\/warehouse-types\/?$/' => Controllers\WarehouseTypes::class,
      '/^warehouses\/settings\/warehouse-location-types\/?$/' => Controllers\LocationTypes::class,
    ]);

    /** @var \Hubleto\App\Community\Settings\Loader */
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Warehouse types'),
      'icon' => 'fas fa-building',
      'url' => 'warehouses/settings/warehouse-types',
    ]);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Warehouse locations types'),
      'icon' => 'fas fa-building',
      'url' => 'warehouses/settings/warehouse-location-types',
    ]);

    /** @var \Hubleto\App\Community\Desktop\AppMenuManager */
    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'warehouses', $this->translate('Warehouses'), 'fas fa-warehouse');
    $appMenu->addItem($this, 'warehouses/locations', $this->translate('Locations'), 'fas fa-pallet');
    $appMenu->addItem($this, 'warehouses/transactions', $this->translate('Transactions'), 'fas fa-arrows-turn-to-dots');

  }

  // installTables
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

    $mLocation->record->recordCreate([
      'id_warehouse' => $idWarehouseMain,
      'code' => 'A1.1',
      'id_type' => 2,
      'capacity' => 230,
      'current_stock_status' => 15,
      'id_operation_manager' => 1,
    ]);

    $mLocation->record->recordCreate([
      'id_warehouse' => $idWarehouseMain,
      'code' => 'A1.2',
      'id_type' => 2,
      'capacity' => 340,
      'current_stock_status' => 156,
      'id_operation_manager' => 1,
    ]);
  }

}
