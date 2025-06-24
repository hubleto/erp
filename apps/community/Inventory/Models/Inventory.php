<?php

namespace HubletoApp\Community\Inventory\Models;

use \HubletoApp\Community\Settings\Models\User;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Lookup;

// This is a crucial table that links products to their specific locations and quantities.
class Inventory extends \HubletoMain\Core\Models\Model
{

  public string $table = 'inventory';
  public string $recordManagerClass = RecordManagers\Inventory::class;

  public array $relations = [ 
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ]
  ];

  // InventoryID (Primary Key, INT/UUID)
  // ProductID (Foreign Key to Products.ProductID, NOT NULL)
  // LocationID (Foreign Key to Locations.LocationID, NOT NULL)
  // Quantity (DECIMAL(10, 2), NOT NULL, DEFAULT 0)
  // BatchNumber (VARCHAR(100), NULLABLE - for batch-tracked items)
  // SerialNumber (VARCHAR(100), UNIQUE, NULLABLE - for individually tracked items)
  // ExpirationDate (DATE, NULLABLE)
  // ReceivedDate (DATETIME, DEFAULT CURRENT_TIMESTAMP)
  // LastMovedDate (DATETIME, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)
  // Status (VARCHAR(50), DEFAULT 'Available' - e.g., 'Available', 'Quarantined', 'Damaged', 'Reserved')

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'first_name' => (new Varchar($this, $this->translate('First name')))->setRequired(),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setRequired(),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class)),
    ]);
  }

}
