<?php

namespace HubletoApp\Community\Inventory\Models;

use \HubletoApp\Community\Settings\Models\User;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Lookup;

// This table records all movements of inventory within the warehouse.
class Transaction extends \HubletoMain\Core\Models\Model
{

  public string $table = 'inventory_transaction';
  public string $recordManagerClass = RecordManagers\Inventory::class;

  public array $relations = [ 
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ]
  ];


  // TransactionID (Primary Key, INT/UUID)
  // TransactionType (VARCHAR(50), NOT NULL - e.g., 'Receipt', 'Shipment', 'Transfer In', 'Transfer Out', 'Adjustment In', 'Adjustment Out', 'Return')
  // TransactionDate (DATETIME, DEFAULT CURRENT_TIMESTAMP)
  // ProductID (Foreign Key to Products.ProductID, NOT NULL)
  // Quantity (DECIMAL(10, 2), NOT NULL)
  // SourceLocationID (Foreign Key to Locations.LocationID, NULLABLE - for transfers, adjustments, shipments)
  // DestinationLocationID (Foreign Key to Locations.LocationID, NULLABLE - for receipts, transfers, adjustments)
  // UserID (Foreign Key to Users.UserID, NOT NULL - who performed the transaction)
  // ReferenceDocumentID (VARCHAR(100), NULLABLE - e.g., Purchase Order ID, Sales Order ID)
  // Notes (TEXT, NULLABLE)

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'first_name' => (new Varchar($this, $this->translate('First name')))->setRequired(),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setRequired(),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class)),
    ]);
  }

}
