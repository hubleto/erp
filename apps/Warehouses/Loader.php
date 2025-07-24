<?php

namespace HubletoApp\Community\Warehouses;

class Loader extends \Hubleto\Framework\App
{
  public bool $hasCustomSettings = true;

  // init
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^warehouses\/?$/' => Controllers\Warehouses::class,
      '/^warehouses\/locations\/?$/' => Controllers\Locations::class,
      '/^warehouses\/settings\/?$/' => Controllers\Settings::class,
      '/^warehouses\/settings\/warehouse-types\/?$/' => Controllers\WarehouseTypes::class,
      '/^warehouses\/settings\/warehouse-location-types\/?$/' => Controllers\LocationTypes::class,
    ]);

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $appMenu->addItem($this, 'warehouses', $this->translate('Warehouses'), 'fas fa-warehouse');
    $appMenu->addItem($this, 'warehouses/locations', $this->translate('Locations'), 'fas fa-pallet');

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\WarehouseType($this->main))->dropTableIfExists()->install();
      (new Models\LocationType($this->main))->dropTableIfExists()->install();
      (new Models\Warehouse($this->main))->dropTableIfExists()->install();
      (new Models\Location($this->main))->dropTableIfExists()->install();
    }
    if ($round == 2) {
      $mLocationType = $this->main->di->create(Models\LocationType::class);
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
    $mWarehouseType = $this->main->di->create(Models\WarehouseType::class);
    $mLocationType = $this->main->di->create(Models\LocationType::class);
    $mWarehouse = $this->main->di->create(Models\Warehouse::class);
    $mLocation = $this->main->di->create(Models\Location::class);

    $idWarehouseTypeMain = $mWarehouseType->record->recordCreate(['name' => 'Main'])['id'];
    $idWarehouseTypeRegional = $mWarehouseType->record->recordCreate(['name' => 'Regional'])['id'];

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
      'current_occupancy' => 1240,
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
      'current_occupancy' => 15,
      'id_operation_manager' => 1,
    ]);

    $mLocation->record->recordCreate([
      'id_warehouse' => $idWarehouseMain,
      'code' => 'A1.2',
      'id_type' => 2,
      'capacity' => 340,
      'current_occupancy' => 156,
      'id_operation_manager' => 1,
    ]);
  }

}
