<?php

namespace Hubleto\App\Community\Products\Models;

use Hubleto\App\Community\Suppliers\Models\Supplier;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Image;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class ProductSupplier extends \Hubleto\Erp\Model
{

  public string $table = 'products_suppliers';
  public string $recordManagerClass = RecordManagers\ProductSupplier::class;
  public ?string $lookupSqlValue = '{%TABLE%}.supplier_product_code';

  public array $relations = [
    'PRODUCT' => [ self::HAS_ONE, Product::class, 'id', 'id_product'],
    'SUPPLIER' => [ self::HAS_ONE, Supplier::class, 'id', 'id_supplier'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class)),
      'id_supplier' => (new Lookup($this, $this->translate('Supplier'), Supplier::class))->setDefaultVisible(),
      'supplier_product_name' => new Text($this, $this->translate('Supplier product name'))->setDefaultVisible(),
      'supplier_product_code' => new Text($this, $this->translate('Supplier product code'))->setDefaultVisible(),
      'purchase_price' => (new Decimal($this, $this->translate('Purchase price')))->setDefaultVisible(),
      'notes' => new Text($this, $this->translate('Internal notes'))->setDefaultVisible(),
      'delivery_time' => new Integer($this, $this->translate('Delivery time'))->setUnit('days'),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Product Suppliers';
    $description->ui["addButtonText"] = $this->translate("Add supplier");
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    return $description;
  }
}
