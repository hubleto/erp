<?php

namespace Hubleto\App\Community\Warehouses\Models;


use Hubleto\App\Community\Products\Models\Product;
use Hubleto\App\Community\Warehouses\Models\Location;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\App\Community\Auth\Models\User;

use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\App\Community\Suppliers\Models\Supplier;

// This table records all movements of inventory within the warehouse.
class Transaction extends \Hubleto\Erp\Model
{
  public string $table = 'warehouses_transactions';
  public string $recordManagerClass = RecordManagers\Transaction::class;
  public ?string $lookupSqlValue = '{%TABLE%}.uid';

  public array $relations = [
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
    'LOCATION_SOURCE' => [ self::BELONGS_TO, Location::class, 'id_location_source', 'id' ],
    'LOCATION_DESTINATION' => [ self::BELONGS_TO, Location::class, 'id_location_destination', 'id' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
  ];

  public const DIRECTION_INBOUND = 1;
  public const DIRECTION_OUTBOUND = 2;

  public const DIRECTIONS = [
    self::DIRECTION_INBOUND => 'Inbound',
    self::DIRECTION_OUTBOUND => 'Outbound',
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
      'direction' => (new Integer($this, $this->translate('Direction')))->setDefaultVisible()
        ->setEnumValues(self::DIRECTIONS)
        ->setDefaultValue(self::DIRECTION_INBOUND)
      ,
      'type' => (new Integer($this, $this->translate('Type')))->setDefaultVisible()
        ->setEnumValues(self::TYPES)
        ->setDefaultValue(self::TYPE_RECEIPT)
      ,
      'id_order' => (new Lookup($this, $this->translate("Order"), Order::class))->setRequired()->setReadonly(),
      'id_supplier' => (new Lookup($this, $this->translate('Supplier'), Supplier::class)),
      'supplier_invoice_number' => (new Varchar($this, $this->translate('Supplier invoice number'))),
      'supplier_order_number' => (new Varchar($this, $this->translate('Supplier order number'))),
      'batch_number' => (new Varchar($this, $this->translate('Batch number'))),
      'serial_number' => (new Varchar($this, $this->translate('Serial number'))),
      'document_1' => (new File($this, $this->translate('Reference document #1')))->setDefaultVisible(),
      'document_2' => (new File($this, $this->translate('Reference document #2'))),
      'document_3' => (new File($this, $this->translate('Reference document #3'))),
      'notes' => (new Text($this, $this->translate('Notes')))->setDefaultVisible(),
      'created_on' => (new DateTime($this, $this->translate('Date and time of transaction')))->setRequired(),
      'id_created_by' => (new Lookup($this, $this->translate('Who performed the transaction'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible(),
    ]);
  }

}
