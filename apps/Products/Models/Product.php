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

class Product extends \Hubleto\Framework\Models\Model
{
  public const TYPE_PRODUCT = 1;
  public const TYPE_SERVICE = 2;

  public string $table = 'products';
  public string $recordManagerClass = RecordManagers\Product::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'GROUP' => [ self::HAS_ONE, Group::class, 'id', 'id_product_group'],
    'SUPPLIER' => [ self::HAS_ONE, Supplier::class, 'id', 'id_supplier'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired(),
      'id_product_group' => (new Lookup($this, $this->translate('Product Group'), Group::class)),
      'type' => (new Integer($this, $this->translate('Product Type')))->setRequired()->setEnumValues(
        [$this::TYPE_PRODUCT => "Single Item", $this::TYPE_SERVICE => "Service"]
      ),
      'id_supplier' => (new Lookup($this, $this->translate('Supplier'), Supplier::class)),
      'is_on_sale' => new Boolean($this, $this->translate('On sale')),
      'image' => new Image($this, $this->translate('Image') . ' [540x600px]'),
      'description' => new Text($this, $this->translate('Description')),
      'count_in_package' => new Decimal($this, $this->translate('Number of items in package')),
      'unit_price' => (new Decimal($this, $this->translate('Single unit price')))->setRequired(),
      'unit' => new Varchar($this, $this->translate('Unit')),
      'margin' => (new Decimal($this, $this->translate('Margin')))->setUnit("%")->setColorScale('bg-light-blue-to-dark-blue'),
      'vat' => (new Decimal($this, $this->translate('Vat')))->setUnit("%")->setRequired(),
      'is_single_order_possible' => new Boolean($this, $this->translate('Single unit order possible')),
      'packaging' => new Varchar($this, $this->translate('Packaging')),
      'sale_ended' => new Date($this, $this->translate('Sale ended')),
      'show_price' => new Boolean($this, $this->translate('Show price to customer')),
      'price_after_reweight' => new Boolean($this, $this->translate('Set price after reweight?')),
      'needs_reordering' => new Boolean($this, $this->translate('Needs reordering?')),
      'storage_rules' => new Text($this, $this->translate('Storage rules')),
      'table' => new Text($this, $this->translate('Table')),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Products';
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui["addButtonText"] = $this->translate("Add product");
    $description->ui['title'] = '';

    return $description;
  }
}
