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
use Hubleto\App\Community\Warehouses\StockStatus;

// This table records all movements of inventory within the warehouse.
class Transaction extends \Hubleto\Erp\Model
{
  public string $table = 'warehouses_transactions';
  public string $recordManagerClass = RecordManagers\Transaction::class;
  public ?string $lookupSqlValue = '{%TABLE%}.uid';
  public ?string $lookupUrlAdd = 'warehouses/transactions/add';
  public ?string $lookupUrlDetail = 'warehouses/transactions/{%ID%}';

  public array $relations = [
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
    'LOCATION_OLD' => [ self::BELONGS_TO, Location::class, 'id_location_old' ],
    'LOCATION_NEW' => [ self::BELONGS_TO, Location::class, 'id_location_new', 'id' ],

    'ITEMS' => [ self::HAS_MANY, TransactionItem::class, 'id_transaction', 'id' ],
  ];

  public const TYPE_INBOUND = 1;
  public const TYPE_OUTBOUND = 2;
  public const TYPE_INTERNAL = 3;
  public const TYPE_ADJUSTMENT = 4;

  public const TYPES = [
    self::TYPE_INBOUND => 'Inbound',
    self::TYPE_OUTBOUND => 'Outbound',
    self::TYPE_INTERNAL => 'Internal transfer',
    self::TYPE_ADJUSTMENT => 'Stock adjustment',
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'uid' => (new Varchar($this, $this->translate('Transaction UID')))->setRequired()->setReadonly()->setDefaultValue(\Hubleto\Framework\Helper::generateUuidV4())->addIndex('INDEX `uid` (`uid`)'),
      'type' => (new Integer($this, $this->translate('Type')))->setDefaultVisible()
        ->setEnumValues(array_map(fn($v) => $this->translate($v), self::TYPES))
        ->setDefaultValue(self::TYPE_INBOUND)
      ,
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class)),
      // 'id_supplier' => (new Lookup($this, $this->translate('Supplier'), Supplier::class)),
      // 'supplier_invoice_number' => (new Varchar($this, $this->translate('Supplier invoice number'))),
      // 'supplier_order_number' => (new Varchar($this, $this->translate('Supplier order number'))),
      'batch_number' => (new Varchar($this, $this->translate('Batch number')))->setDefaultVisible(),
      'serial_number' => (new Varchar($this, $this->translate('Serial number')))->setDefaultVisible(),
      'id_location_old' => (new Lookup($this, $this->translate('Shipped from'), Location::class))->setDefaultVisible(),
      'id_location_new' => (new Lookup($this, $this->translate('Recevied at'), Location::class))->setDefaultVisible(),
      'document_1' => (new File($this, $this->translate('Reference document #1'))),
      'document_2' => (new File($this, $this->translate('Reference document #2'))),
      'document_3' => (new File($this, $this->translate('Reference document #3'))),
      'notes' => (new Text($this, $this->translate('Notes')))->setDefaultVisible(),
      'created_on' => (new DateTime($this, $this->translate('Date and time of transaction')))->setRequired()->setDefaultValue(date('Y-m-d H:i:s')),
      'id_created_by' => (new Lookup($this, $this->translate('Who performed the transaction'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
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
    $description->ui['addButtonText'] = $this->translate('Add transaction');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->addFilter('fTransactionType', [
      'title' => $this->translate('Type'),
      'options' => array_map(fn($v) => $this->translate($v), self::TYPES)
    ]);

    return $description;
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
    if (empty($savedRecord['uid'])) $savedRecord['uid'] = \Hubleto\Framework\Helper::generateUuidV4();
    $this->record->recordUpdate($savedRecord);

    /** @var StockStatus */
    $stockStatus = $this->getService(StockStatus::class);
    $stockStatus->recalculateCapacityAndStockStatusForTransaction((int) $savedRecord['id']);

    return parent::onAfterCreate($savedRecord);
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
    $stockStatus->recalculateCapacityAndStockStatusForTransaction((int) $savedRecord['id']);

    return $savedRecord;
  }

}
