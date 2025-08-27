<?php

namespace HubletoApp\Community\Products\Models;

use HubletoApp\Community\Suppliers\Models\Supplier;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Image;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class ProductSupplier extends \HubletoMain\Model
{

  public string $table = 'products_suppliers';
  public string $recordManagerClass = RecordManagers\ProductSupplier::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'PRODUCT' => [ self::HAS_ONE, Product::class, 'id', 'id_product'],
    'SUPPLIER' => [ self::HAS_ONE, Supplier::class, 'id', 'id_supplier'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class)),
      'id_supplier' => (new Lookup($this, $this->translate('Supplier'), Supplier::class)),
      'supplier_product_name' => new Text($this, $this->translate('Supplier product name')),
      'supplier_product_code' => new Text($this, $this->translate('Supplier product code')),
      'purchase_price' => (new Decimal($this, $this->translate('Purchase price')))->setRequired(),
      'notes' => new Text($this, $this->translate('Internal notes')),
      'delivery_time' => new Integer($this, $this->translate('Delivery time'))->setUnit('days'),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Product Suppliers';
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui["addButtonText"] = $this->translate("Add supplier");
    $description->ui['title'] = '';

    return $description;
  }
}
