<?php

namespace HubletoApp\Community\Inventory\Models;

use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Products\Models\Product;
use HubletoApp\Community\Warehouses\Models\Location;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Decimal;

// This table records all movements of inventory within the warehouse.
class Transaction extends \Hubleto\Framework\Models\Model
{
  public string $table = 'inventory_transactions';
  public string $recordManagerClass = RecordManagers\Transaction::class;
  public ?string $lookupSqlValue = '{%TABLE%}.uid';

  public array $relations = [
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
    'LOCATION_SOURCE' => [ self::BELONGS_TO, Location::class, 'id_location_source', 'id' ],
    'LOCATION_DESTINATION' => [ self::BELONGS_TO, Location::class, 'id_location_destination', 'id' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
  ];

  public const TYPE_RECEIPT = 1;
  public const TYPE_SHIPMENT = 2;
  public const TYPE_TRANSFER_IN = 3;
  public const TYPE_TRANSFER_OUT = 4;
  public const TYPE_ADJUSTMENT_IN = 5;
  public const TYPE_ADJUSTMENT_OUT = 6;
  public const TYPE_RETURN = 7;

  public const TYPES = [
    self::TYPE_RECEIPT => 'Receipt',
    self::TYPE_SHIPMENT => 'Shipment',
    self::TYPE_TRANSFER_IN => 'Transfer In',
    self::TYPE_TRANSFER_OUT => 'Transfer Out',
    self::TYPE_ADJUSTMENT_IN => 'Adjustment In',
    self::TYPE_ADJUSTMENT_OUT => 'Adjustment Out',
    self::TYPE_RETURN => 'Return',
  ];


  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'uid' => (new Varchar($this, $this->translate('Transaction UID')))->setRequired(),
      'type' => (new Integer($this, $this->translate('Type')))->setProperty('defaultVisibility', true)
        ->setEnumValues(self::TYPES)
        ->setDefaultValue(self::TYPE_RECEIPT)
      ,
      'datetime_when' => (new DateTime($this, $this->translate('Date and time of transaction')))->setRequired(),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setProperty('defaultVisibility', true)->setRequired(),
      'quantity' => (new Decimal($this, $this->translate('Quantity')))->setProperty('defaultVisibility', true),
      'id_location_source' => (new Lookup($this, $this->translate('Source location'), Location::class))->setProperty('defaultVisibility', true)->setRequired(),
      'id_location_destination' => (new Lookup($this, $this->translate('Destination location'), Location::class))->setProperty('defaultVisibility', true)->setRequired(),
      'id_user' => (new Lookup($this, $this->translate('Who performed the trancation'), User::class))->setProperty('defaultVisibility', true),
      'document_1' => (new File($this, $this->translate('Reference document #1')))->setProperty('defaultVisibility', true),
      'document_2' => (new File($this, $this->translate('Reference document #2'))),
      'document_3' => (new File($this, $this->translate('Reference document #3'))),
      'notes' => (new Text($this, $this->translate('Notes')))->setProperty('defaultVisibility', true),
    ]);
  }

}
