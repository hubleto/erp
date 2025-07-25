<?php

namespace HubletoApp\Community\Warehouses\Models;

use HubletoApp\Community\Settings\Models\User;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Image;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Text;

class Warehouse extends \Hubleto\Framework\Models\Model
{
  public string $table = 'warehouses';
  public string $recordManagerClass = RecordManagers\Warehouse::class;
  public ?string $lookupSqlValue = 'concat("Warehouse #", {%TABLE%}.id)';

  public array $relations = [
    'TYPE' => [ self::BELONGS_TO, WarehouseType::class, 'id_type', 'id' ],
    'OPERATION_MANAGER' => [ self::BELONGS_TO, User::class, 'id_operaion_manager', 'id' ],
  ];

  public const OPERATIONAL_STATUS_ACTIVE = 1;
  public const OPERATIONAL_STATUS_INACTIVE = 2;
  public const OPERATIONAL_STATUS_MAINTENANCE = 3;

  public const OPERATIONAL_STATUSES = [
    self::OPERATIONAL_STATUS_ACTIVE => 'Active',
    self::OPERATIONAL_STATUS_INACTIVE => 'Inactive',
    self::OPERATIONAL_STATUS_MAINTENANCE => 'Maintenance',
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setProperty('defaultVisibility', true),
      'id_type' => (new Lookup($this, $this->translate('Warehouse type'), WarehouseType::class))->setProperty('defaultVisibility', true),
      'address' => (new Varchar($this, $this->translate('Address')))->setProperty('defaultVisibility', true),
      'address_plus_code' => (new Varchar($this, $this->translate('Address Plus code'))),
      'contact_person' => (new Varchar($this, $this->translate('Contact person'))),
      'contact_email' => (new Varchar($this, $this->translate('Contact email')))->setProperty('defaultVisibility', true),
      'contact_phone' => (new Varchar($this, $this->translate('Contact phone'))),
      'lng' => (new Decimal($this, $this->translate('Coordinates: longitude'))),
      'lat' => (new Decimal($this, $this->translate('Coordinates: latitude'))),
      'description' => (new Text($this, $this->translate('Description'))),
      'capacity' => (new Decimal($this, $this->translate('Capacity')))->setReadonly()->setProperty('defaultVisibility', true)
        ->setDescription('Automatically calculated as total capacity of all locations in warehouse.')
      ,
      'capacity_unit' => (new Varchar($this, $this->translate('Capacity unit')))->setProperty('defaultVisibility', true),
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
      'photo_1' => (new Image($this, $this->translate('Photo #1'))),
      'photo_2' => (new Image($this, $this->translate('Photo #2'))),
      'photo_3' => (new Image($this, $this->translate('Photo #3'))),
      'id_operation_manager' => (new Lookup($this, $this->translate('Manager of operation'), User::class))->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add warehouse';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    // $description->ui['defaultFilters'] = [
    //   'fArchive' => [ 'title' => 'Archive', 'options' => [ 0 => 'Active', 1 => 'Archived' ] ],
    // ];

    return $description;
  }

}
