<?php

namespace HubletoApp\Community\Warehouses\Models;

use \HubletoApp\Community\Settings\Models\User;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Lookup;

class Warehouse extends \HubletoMain\Core\Models\Model
{

  public string $table = 'warehouses';
  public string $recordManagerClass = RecordManagers\Warehouse::class;

  public array $relations = [ 
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ]
  ];

  // WarehouseID (Primary Key, INT/UUID)
  // WarehouseName (VARCHAR(255), UNIQUE, NOT NULL)
  // AddressLine1 (VARCHAR(255))
  // AddressLine2 (VARCHAR(255), NULLABLE)
  // City (VARCHAR(100))
  // StateProvince (VARCHAR(100), NULLABLE)
  // PostalCode (VARCHAR(20))
  // Country (VARCHAR(100))
  // ContactPerson (VARCHAR(255), NULLABLE)
  // ContactPhone (VARCHAR(50), NULLABLE)
  // IsActive (BOOLEAN, DEFAULT TRUE)
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
