<?php

namespace Hubleto\App\Community\Warehouses\Models;


use Hubleto\App\Community\Products\Models\Product;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\App\Community\Auth\Models\User;

class TransactionItem extends \Hubleto\Erp\Model
{
  public string $table = 'warehouses_transactions_items';
  public string $recordManagerClass = RecordManagers\TransactionItem::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id_transaction';

  public array $relations = [
    'TRANSACTION' => [ self::BELONGS_TO, Transaction::class, 'id_transaction', 'id' ],
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
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
      'id_transaction' => (new Lookup($this, $this->translate('Transaction'), Transaction::class))->setDefaultVisible()->setRequired(),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setDefaultVisible()->setRequired(),
      'purchase_price' => (new Decimal($this, $this->translate('Purchase price')))->setDefaultVisible(),
      'quantity' => (new Decimal($this, $this->translate('Quantity')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->permissions['canCreate'] = false;
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton', 'footer']);
    return $description;
  }

}
