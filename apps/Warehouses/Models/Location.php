<?php

namespace HubletoApp\Community\Warehouses\Models;

use HubletoApp\Community\Settings\Models\User;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Image;
use Hubleto\Framework\Db\Column\Text;

class Location extends \Hubleto\Framework\Models\Model
{
  public string $table = 'warehouses_locations';
  public string $recordManagerClass = RecordManagers\Location::class;
  public ?string $lookupSqlValue = '{%TABLE%}.code';

  public const OPERATIONAL_STATUS_ACTIVE = 1;
  public const OPERATIONAL_STATUS_INACTIVE = 2;
  public const OPERATIONAL_STATUS_MAINTENANCE = 3;

  public const OPERATIONAL_STATUSES = [
    self::OPERATIONAL_STATUS_ACTIVE => 'Active',
    self::OPERATIONAL_STATUS_INACTIVE => 'Inactive',
    self::OPERATIONAL_STATUS_MAINTENANCE => 'Maintenance',
  ];

  public array $relations = [
    'TYPE' => [ self::BELONGS_TO, LocationType::class, 'id_type', 'id' ],
    'OPERATION_MANAGER' => [ self::BELONGS_TO, User::class, 'id_operaion_manager', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_warehouse' => (new Lookup($this, $this->translate('Warehouse'), Warehouse::class)),
      'code' => (new Varchar($this, $this->translate('Location code')))->setExamples(['Aisle 1', 'Rack B', 'Shelf 2.3', 'Bin A1'])->setProperty('defaultVisibility', true),
      'id_type' => (new Lookup($this, $this->translate('Location type'), LocationType::class))->setProperty('defaultVisibility', true),
      'description' => (new Text($this, $this->translate('Description'))),
      'capacity' => (new Decimal($this, $this->translate('Capacity')))->setProperty('defaultVisibility', true),
      'current_occupancy' => (new Decimal($this, $this->translate('Current occupancy')))->setProperty('defaultVisibility', true),
      'operational_status' => (new Integer($this, $this->translate('Operational status')))->setProperty('defaultVisibility', true)
        ->setEnumValues(self::OPERATIONAL_STATUSES)
        ->setDefaultValue(self::OPERATIONAL_STATUS_ACTIVE)
        ->setEnumCssClasses([
          self::OPERATIONAL_STATUS_ACTIVE => 'bg-green-100 text-green-800',
          self::OPERATIONAL_STATUS_INACTIVE => 'bg-red-100 text-red-800',
          self::OPERATIONAL_STATUS_MAINTENANCE => 'bg-yellow-100 text-yellow-800',
        ])
      ,
      'placement' => (new Json($this, $this->translate('Placement')))
        ->setDescription('JSON-formatted information about placement of location inside the warehouse.')
      ,
      'photo_1' => (new Image($this, $this->translate('Photo #1'))),
      'photo_2' => (new Image($this, $this->translate('Photo #2'))),
      'photo_3' => (new Image($this, $this->translate('Photo #3'))),
      'id_operation_manager' => (new Lookup($this, $this->translate('Manager of operation'), User::class)),
    ]);
  }

  public function recalculateWarehouseData(int $idWarehouse): void
  {
    $this->main->pdo->execute("
      update `warehouses` set
        `capacity` = ifnull((select sum(ifnull(`capacity`, 0)) from `warehouses_locations` where `id_warehouse` = :idWarehouse), 0),
        `current_occupancy` = ifnull((select sum(ifnull(`current_occupancy`, 0)) from `warehouses_locations` where `id_warehouse` = :idWarehouse), 0)
      where `id` = :idWarehouse
    ", ["idWarehouse" => $idWarehouse]);
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);
    $this->recalculateWarehouseData((int) $savedRecord['id_warehouse']);
    return $savedRecord;
  }

  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);
    $this->recalculateWarehouseData((int) $savedRecord['id_warehouse']);
    return $savedRecord;
  }

}
