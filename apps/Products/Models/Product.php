<?php

namespace Hubleto\App\Community\Products\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Image;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class Product extends \Hubleto\Erp\Model
{
  public const TYPE_CONSUMABLE = 1;
  public const TYPE_STORABLE = 2;
  public const TYPE_SERVICE = 3;

  public const INVOICING_POLICY_ORDER = 1;
  public const INVOICING_POLICY_DELIVERY = 2;
  public const INVOICING_POLICY_MANUAL = 99;

  const TYPE_ENUM_VALUES = [
    self::TYPE_CONSUMABLE => "Consumable",
    self::TYPE_STORABLE => "Storable",
    self::TYPE_SERVICE => "Service",
  ];

  const INVOICING_POLICY_ENUM_VALUES = [
    self::INVOICING_POLICY_ORDER => "Order",
    self::INVOICING_POLICY_DELIVERY => "Delivery",
    self::INVOICING_POLICY_MANUAL => "Manual",
  ];

  public string $table = 'products';
  public string $recordManagerClass = RecordManagers\Product::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.ean, ""), " ", ifnull({%TABLE%}.name, ""))';
  public ?string $lookupUrlAdd = 'products/add';
  public ?string $lookupUrlDetail = 'products/{%ID%}';

  public array $relations = [
    'GROUP' => [ self::HAS_ONE, Group::class, 'id', 'id_group'],
  ];

  public function describeColumns(): array
  {
    $typeEnumValues = array_merge(
      self::TYPE_ENUM_VALUES,
      $this->getService(\Hubleto\App\Community\Products\Loader::class)->productTypes
    );

    $typeDescription = 
      'Consumables are physical products for which you do not manage inventory levels - they are always available. '
      . 'Storable products are physical items for which you manage inventory levels.'
    ;

    $invoicingPolicyDescription = 
      'Order: An invoice is generated immediately after a sales order is confirmed. '
      . 'Delivery: An invoice is generated immediately after the delivery is completed. '
      . 'Manual: Invoice is not automatically generated.'
    ;

    return array_merge(parent::describeColumns(), [
      'ean' => (new Varchar($this, $this->translate('EAN')))->setRequired()->setDefaultVisible(),
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setDefaultVisible(),
      'id_group' => (new Lookup($this, $this->translate('Group'), Group::class)),
      'type' => (new Integer($this, $this->translate('Product Type')))->setEnumValues($typeEnumValues)->setDescription($typeDescription)->setDefaultVisible(),
      'invoicing_policy' => (new Integer($this, $this->translate('Invoicing policy')))->setEnumValues(self::INVOICING_POLICY_ENUM_VALUES)->setDescription($invoicingPolicyDescription),
      'is_on_sale' => new Boolean($this, $this->translate('On sale'))->setDefaultVisible(),
      'image' => new Image($this, $this->translate('Image') . ' [540x600px]'),
      'description' => new Text($this, $this->translate('Description')),
      'notes' => new Text($this, $this->translate('Internal notes')),
      'amount_in_package' => new Decimal($this, $this->translate('Amount of items in package')),
      'sales_price' => (new Decimal($this, $this->translate('Sales price')))->setRequired()->setDefaultVisible(),
      'unit' => new Varchar($this, $this->translate('Unit'))->setDefaultVisible(),
      'margin' => (new Decimal($this, $this->translate('Margin')))->setUnit("%")->setColorScale('bg-light-blue-to-dark-blue'),
      'vat' => (new Decimal($this, $this->translate('VAT')))->setUnit("%"),
      'qr_code_data' => new Varchar($this, $this->translate('Data ')),
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
    $description->ui["addButtonText"] = $this->translate("Add product");
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->addFilter('fProductType', [
      'title' => $this->translate('Type'),
      'type' => 'multipleSelectButtons',
      'options' => self::TYPE_ENUM_VALUES
    ]);

    $description->addFilter('fProductInvoicingPolicy', [
      'title' => $this->translate('Invoicing policy'),
      'type' => 'multipleSelectButtons',
      'options' => self::INVOICING_POLICY_ENUM_VALUES
    ]);

    $fGroupOptions = [];
    foreach ($this->getModel(Group::class)->record->get() as $value) {
      $fGroupOptions[$value->id] = $value->title;
    }
    $description->addFilter('fProductGroup', [
      'title' => $this->translate('Group'),
      'type' => 'multipleSelectButtons',
      'options' => $fGroupOptions,
    ]);

    return $description;
  }
}
