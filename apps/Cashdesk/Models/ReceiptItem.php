<?php

namespace Hubleto\App\Community\Cashdesk\Models;


use Hubleto\App\Community\Products\Models\Product;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\App\Community\Auth\Models\User;

class ReceiptItem extends \Hubleto\Erp\Model
{
  public string $table = 'cashdesk_receipts_items';
  public string $recordManagerClass = RecordManagers\ReceiptItem::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id_receipt';

  public array $relations = [
    'RECEIPT' => [ self::BELONGS_TO, Receipt::class, 'id_receipt', 'id' ],
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
      'id_receipt' => (new Lookup($this, $this->translate('Receipt'), Receipt::class))->setDefaultVisible()->setRequired(),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setDefaultVisible()->setRequired(),
      'vat_percent' => (new Decimal($this, $this->translate('VAT')))->setDefaultVisible()->setUnit('%'),
      'unit_price_excl_vat' => (new Decimal($this, $this->translate('Unit price excl. VAT')))->setDefaultVisible()->setUnit('€'),
      'unit_vat' => (new Decimal($this, $this->translate('Unit VAT')))->setDefaultVisible()->setUnit('€'),
      'unit_price_incl_vat' => (new Decimal($this, $this->translate('Unit price incl. VAT')))->setDefaultVisible()->setUnit('€'),
      'quantity' => (new Decimal($this, $this->translate('Quantity')))->setDefaultVisible(),
      'total_price_excl_vat' => (new Decimal($this, $this->translate('Total price excl. VAT')))->setDefaultVisible()->setUnit('€'),
      'total_vat' => (new Decimal($this, $this->translate('Total VAT')))->setDefaultVisible()->setUnit('€'),
      'total_price_incl_vat' => (new Decimal($this, $this->translate('Total price incl. VAT')))->setDefaultVisible()->setUnit('€'),
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
