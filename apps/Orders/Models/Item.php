<?php

namespace Hubleto\App\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\App\Community\Products\Models\Product;
use Hubleto\App\Community\Products\Controllers\Api\CalculatePrice;
use Hubleto\App\Community\Auth\Models\User;

class Item extends \Hubleto\Erp\Model
{
  public string $table = 'orders_items';
  public string $recordManagerClass = RecordManagers\Item::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';
  public ?string $lookupUrlDetail = 'orders/items/{%ID%}';
  public ?string $lookupUrlAdd = 'orders/items/add';

  public array $relations = [
    'ORDER' => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id'],
    'INVOICE_ITEM' => [ self::BELONGS_TO, Item::class, "id_invoice_item" ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
      'title' => (new Varchar($this, $this->translate('Title')))->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setDefaultVisible(),

      'unit_price' => new Decimal($this, $this->translate('Unit price'))->setDefaultVisible()->setUnit($this->locale()->getCurrencySymbol()),
      'amount' => new Decimal($this, $this->translate('Amount'))->setDefaultVisible()->setUnit('x'),
      'discount' => new Decimal($this, $this->translate('Discount'))->setDefaultVisible()->setUnit('%'),
      'vat' => new Decimal($this, $this->translate('VAT'))->setUnit('%'),

      'price_excl_vat' => new Decimal($this, $this->translate('Price excl. VAT'))->setDefaultVisible()->setUnit($this->locale()->getCurrencySymbol()),
      'price_incl_vat' => new Decimal($this, $this->translate('Price incl. VAT'))->setDefaultVisible()->setUnit($this->locale()->getCurrencySymbol()),

      'date_due' => (new Date($this, $this->translate('Due date')))->setDefaultVisible()->setDefaultValue(date("Y-m-d")),
      'notes' => (new Text($this, $this->translate('Notes')))->setDefaultVisible(),
      'id_invoice_item' => (new Lookup($this, $this->translate('Invoice item'), Item::class))->setDefaultVisible(),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())->setDefaultVisible(),
      'position' => (new Integer($this, $this->translate('Position (in the PDF)')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Order Items';
    $description->ui["addButtonText"] = $this->translate("Add item");

    $description->ui['orderBy'] = [
      'field' => 'date_due',
      'direction' => 'asc',
    ];

    if (isset($filters['fGroupBy'])) {
      $fGroupBy = (array) $filters['fGroupBy'];

      $showOnlyColumns = [];
      if (in_array('order', $fGroupBy)) $showOnlyColumns[] = 'id_order';

      $description->showOnlyColumns($showOnlyColumns);

      $description->addColumn(
        'total_price_excl_vat',
        (new Decimal($this, $this->translate('Total price excl. VAT')))->setDecimals(2)->setCssClass('badge badge-warning')
      );

      $description->addColumn(
        'total_price_incl_vat',
        (new Decimal($this, $this->translate('Total price incl. VAT')))->setDecimals(2)->setCssClass('badge badge-warning')
      );

    }

    $description->addFilter('fStatus', [
      'title' => $this->translate('Status'),
      'options' => [
        1 => $this->translate('Not-prepared to invoice'),
        2 => $this->translate('Prepared to invoice'),
      ]
    ]);

    $description->addFilter('fDue', [
      'title' => $this->translate('Due / Not due'),
      'direction' => 'horizontal',
      'options' => [
        1 => $this->translate('Due'),
        2 => $this->translate('Not due'),
      ]
    ]);

    $description->addFilter('fGroupBy', [
      'title' => $this->translate('Group by'),
      'type' => 'multipleSelectButtons',
      'options' => [
        'order' => $this->translate('Order'),
      ]
    ]);

    return $description;
  }

  /**
   * [Description for describeForm]
   *
   * @return \Hubleto\Framework\Description\Form
   * 
   */
  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->show(['copyButton']);

    $idOrder = $this->router()->urlParamAsInteger('idOrder');
    $itemsCount = $this->record->where('orders_items.id_order', $idOrder)->count();

    $description->defaultValues['position'] = $itemsCount + 1;

    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['INVOICE_ITEM'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }

  /**
   * [Description for onBeforeCreate]
   *
   * @param array $record
   * 
   * @return array
   * 
   */
  public function onBeforeCreate(array $record): array
  {
    $record["price_excl_vat"] = ($this->getService(CalculatePrice::class))->calculatePriceExcludingVat(
      (float) ($record["sales_price"] ?? 0),
      (float) ($record["amount"] ?? 0),
      (float) ($record["discount"] ?? 0)
    );
    $record["price_incl_vat"] = ($this->getService(CalculatePrice::class))->calculatePriceIncludingVat(
      (float) ($record["sales_price"] ?? 0),
      (float) ($record["amount"] ?? 0),
      (float) ($record["vat"] ?? 0),
      (float) ($record["discount"] ?? 0)
    );
    return $record;
  }

  /**
   * [Description for onBeforeUpdate]
   *
   * @param array $record
   * 
   * @return array
   * 
   */
  public function onBeforeUpdate(array $record): array
  {
    $record["price_excl_vat"] = ($this->getService(CalculatePrice::class))->calculatePriceExcludingVat(
      (float) ($record["sales_price"] ?? 0),
      (float) ($record["amount"] ?? 0),
      (float) ($record["discount"] ?? 0)
    );
    $record["price_incl_vat"] = ($this->getService(CalculatePrice::class))->calculatePriceIncludingVat(
      (float) ($record["sales_price"] ?? 0),
      (float) ($record["amount"] ?? 0),
      (float) ($record["vat"] ?? 0),
      (float) ($record["discount"] ?? 0)
    );
    return $record;
  }
}
