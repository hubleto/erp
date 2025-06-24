<?php

namespace HubletoApp\Community\Warehouses\Models;

use \HubletoApp\Community\Settings\Models\User;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Lookup;

class Location extends \HubletoMain\Core\Models\Model
{

  public string $table = 'warehouses_locations';
  public string $recordManagerClass = RecordManagers\Location::class;

  public array $relations = [ 
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ]
  ];

  // LocationID (Primary Key, INT/UUID)
  // WarehouseID (Foreign Key to Warehouses.WarehouseID, NOT NULL)
  // ParentLocationID (Foreign Key to Locations.LocationID, NULLABLE - for hierarchical structures like racks on an aisle, bins on a shelf)
  // LocationCode (VARCHAR(50), NOT NULL - e.g., "Aisle 1", "Rack B", "Shelf 3", "Bin 01")
  // LocationType (VARCHAR(50), NOT NULL - e.g., 'Warehouse', 'Aisle', 'Rack', 'Shelf', 'Bin', 'Picking Zone', 'Receiving Area', 'Shipping Area')
  // Description (VARCHAR(255), NULLABLE)
  // CapacityUnit (VARCHAR(50), NULLABLE - e.g., 'Cubic Feet', 'Pcs')
  // MaxCapacity (DECIMAL(10, 2), NULLABLE)
  // CurrentOccupancy (DECIMAL(10, 2), NULLABLE)
  // IsAvailable (BOOLEAN, DEFAULT TRUE)
  // CreatedAt (DATETIME, DEFAULT CURRENT_TIMESTAMP)
  // UpdatedAt (DATETIME, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'first_name' => (new Varchar($this, $this->translate('First name')))->setRequired(),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setRequired(),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class)),
    ]);
  }

}
