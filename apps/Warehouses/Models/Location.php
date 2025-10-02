<?php

namespace Hubleto\App\Community\Warehouses\Models;


use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Image;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\App\Community\Auth\Models\User;

use Hubleto\App\Community\Warehouses\StockStatus;

class Location extends \Hubleto\Erp\Model
{
  public string $table = 'warehouses_locations';
  public string $recordManagerClass = RecordManagers\Location::class;
  public ?string $lookupSqlValue = '{%TABLE%}.code';
  // public ?string $lookupUrlAdd = 'warehouses/locations/add';
  public ?string $lookupUrlDetail = 'warehouses/locations/{%ID%}';

  public const OPERATIONAL_STATUS_ACTIVE = 1;
  public const OPERATIONAL_STATUS_INACTIVE = 2;
  public const OPERATIONAL_STATUS_MAINTENANCE = 3;

  public const OPERATIONAL_STATUSES = [
    self::OPERATIONAL_STATUS_ACTIVE => 'Active',
    self::OPERATIONAL_STATUS_INACTIVE => 'Inactive',
    self::OPERATIONAL_STATUS_MAINTENANCE => 'Maintenance',
  ];

  public array $relations = [
    'WAREHOUSE' => [ self::BELONGS_TO, Warehouse::class, 'id_warehouse', 'id' ],
    'TYPE' => [ self::BELONGS_TO, LocationType::class, 'id_type', 'id' ],
    'OPERATION_MANAGER' => [ self::BELONGS_TO, User::class, 'id_operaion_manager', 'id' ],
  ];

  public function getLookupValue(array $dataRaw): string
  {
    return $dataRaw['code'] . ' @' . $dataRaw['WAREHOUSE']['name'];
  }

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_warehouse' => (new Lookup($this, $this->translate('Warehouse'), Warehouse::class))->setDefaultVisible(),
      'code' => (new Varchar($this, $this->translate('Location code')))->setExamples(['Aisle 1', 'Rack B', 'Shelf 2.3', 'Bin A1'])->setDefaultVisible(),
      'id_type' => (new Lookup($this, $this->translate('Location type'), LocationType::class))->setDefaultVisible(),
      'description' => (new Text($this, $this->translate('Description'))),
      'capacity' => (new Decimal($this, $this->translate('Capacity')))->setDefaultVisible(),
      'current_stock_status' => (new Decimal($this, $this->translate('Current stock status')))->setDefaultVisible(),
      'operational_status' => (new Integer($this, $this->translate('Operational status')))->setDefaultVisible()
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
      'id_operation_manager' => (new Lookup($this, $this->translate('Manager of operation'), User::class))->setReactComponent('InputUserSelect'),
    ]);
  }

  /**
   * [Description for describeTable]
   *
   * @return \Hubleto\Framework\Description\Table
   * 
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Add location');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->addFilter('fLocationOperationalStatus', [
      'title' => $this->translate('Operational status'),
      'type' => 'multipleSelectButtons',
      'options' => self::OPERATIONAL_STATUSES
    ]);

    $fLocationTypeOptions = [];
    foreach ($this->getModel(LocationType::class)->record->get() as $value) {
      $fLocationTypeOptions[$value->id] = $value->name;
    }
    $description->addFilter('fLocationType', [
      'title' => $this->translate('Type'),
      'type' => 'multipleSelectButtons',
      'options' => $fLocationTypeOptions,
    ]);

    return $description;
  }

  /**
   * [Description for onAfterUpdate]
   *
   * @param array $originalRecord
   * @param array $savedRecord
   * 
   * @return array
   * 
   */
  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    /** @var StockStatus */
    $stockStatus = $this->getService(StockStatus::class);
    $stockStatus->recalculateCapacityAndStockStatusOfWarehouse((int) $savedRecord['id_warehouse']);

    return $savedRecord;
  }

  /**
   * [Description for onAfterCreate]
   *
   * @param array $savedRecord
   * 
   * @return array
   * 
   */
  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);

    /** @var StockStatus */
    $stockStatus = $this->getService(StockStatus::class);
    $stockStatus->recalculateCapacityAndStockStatusOfWarehouse((int) $savedRecord['id_warehouse']);

    return $savedRecord;
  }

}
