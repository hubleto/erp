<?php

namespace Hubleto\App\Community\PriceList\Models;

use Hubleto\App\Community\Products\Models\Category;
use Hubleto\App\Community\Products\Models\Group;
use Hubleto\App\Community\Products\Models\Product;
use Hubleto\App\Community\Products\Models\Unit;
use Hubleto\App\Community\Suppliers\Models\Supplier;
use Hubleto\App\Community\Worksheets\Models\ActivityType;
use Hubleto\App\Community\Settings\Models\VatRate;
use Hubleto\App\Community\PriceList\Services\HistoryWriter;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Exceptions\RecordSaveException;

class Item extends \Hubleto\Erp\Model
{
  public const METHOD_FIXED = 1;
  public const METHOD_PERCENT_OFF_BASE = 2;
  public const METHOD_AMOUNT_OFF_BASE = 3;
  public const METHOD_MARKUP_ON_COST = 4;
  public const METHOD_MARGIN_ON_COST = 5;
  public const BASE_PRODUCT_SALES_PRICE = 1;
  public const BASE_PRODUCT_COST = 2;
  public const BASE_PARENT_LIST = 3;
  public const BASE_OTHER_LIST = 4;

  public string $table = 'price_lists_items';
  public string $recordManagerClass = RecordManagers\Item::class;
  public ?string $lookupSqlValue = 'concat("#", {%TABLE%}.id, " (min ", ifnull({%TABLE%}.min_qty, 0), ")")';
  public ?string $lookupUrlAdd = 'price-lists/items/add';
  public ?string $lookupUrlDetail = 'price-lists/items/{%ID%}';

  public array $relations = [
    'PRICE_LIST' => [ self::BELONGS_TO, PriceList::class, 'id_price_list', 'id' ],
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
    'CATEGORY' => [ self::BELONGS_TO, Category::class, 'id_category', 'id' ],
    'GROUP' => [ self::BELONGS_TO, Group::class, 'id_group', 'id' ],
    'UNIT' => [ self::BELONGS_TO, Unit::class, 'id_unit', 'id' ],
    'SUPPLIER' => [ self::BELONGS_TO, Supplier::class, 'id_supplier', 'id' ],
    'VAT_RATE' => [ self::BELONGS_TO, VatRate::class, 'id_vat_rate', 'id' ],
    'BASE_PRICE_LIST' => [ self::BELONGS_TO, PriceList::class, 'id_base_price_list', 'id' ],
  ];

  /**
   * Describes columns of the model.
   *
   * @return array
   *
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_price_list' => (new Lookup($this, $this->translate('Price list'), PriceList::class))->setFkOnDelete('CASCADE')->setRequired()->setDefaultVisible(),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setFkOnDelete('CASCADE')->setDefaultVisible(),
      'id_category' => (new Lookup($this, $this->translate('Category'), Category::class))->setFkOnDelete('CASCADE'),
      'id_group' => (new Lookup($this, $this->translate('Group'), Group::class))->setFkOnDelete('CASCADE'),
      'id_unit' => (new Lookup($this, $this->translate('Unit'), Unit::class))->setFkOnDelete('SET NULL'),
      'id_supplier' => (new Lookup($this, $this->translate('Supplier'), Supplier::class))->setFkOnDelete('SET NULL'),
      'supplier_product_code' => (new Varchar($this, $this->translate('Supplier product code'))),
      'id_vat_rate' => (new Lookup($this, $this->translate('VAT rate'), VatRate::class))->setFkOnDelete('SET NULL'),
      'min_qty' => (new Decimal($this, $this->translate('Minimum quantity')))->setDefaultVisible()->setDefaultValue(0)->setUnit('x'),
      'max_qty' => (new Decimal($this, $this->translate('Maximum quantity')))->setUnit('x'),
      'valid_from' => (new Date($this, $this->translate('Valid from'))),
      'valid_to' => (new Date($this, $this->translate('Valid to'))),
      'method' => (new Integer($this, $this->translate('Method')))->setDefaultVisible()->setDefaultValue(self::METHOD_FIXED)->setEnumValues([self::METHOD_FIXED => $this->translate('Fixed price'), self::METHOD_PERCENT_OFF_BASE => $this->translate('Percent off base'), self::METHOD_AMOUNT_OFF_BASE => $this->translate('Amount off base'), self::METHOD_MARKUP_ON_COST => $this->translate('Markup on cost'), self::METHOD_MARGIN_ON_COST => $this->translate('Margin on cost')]),
      'base' => (new Integer($this, $this->translate('Base')))->setDefaultValue(self::BASE_PRODUCT_SALES_PRICE)->setEnumValues([self::BASE_PRODUCT_SALES_PRICE => $this->translate('Product sales price'), self::BASE_PRODUCT_COST => $this->translate('Product cost'), self::BASE_PARENT_LIST => $this->translate('Parent price list'), self::BASE_OTHER_LIST => $this->translate('Other price list')]),
      'id_base_price_list' => (new Lookup($this, $this->translate('Base price list'), PriceList::class))->setFkOnDelete('SET NULL'),
      'value' => (new Decimal($this, $this->translate('Value')))->setDefaultVisible(),
      'extra_fee' => (new Decimal($this, $this->translate('Extra fee'))),
      'min_margin_percent' => (new Decimal($this, $this->translate('Minimum margin')))->setUnit('%'),
      'min_price' => (new Decimal($this, $this->translate('Minimum price'))),
      'max_price' => (new Decimal($this, $this->translate('Maximum price'))),
      'priority' => (new Integer($this, $this->translate('Priority')))->setDefaultValue(0),
      'is_active' => (new Boolean($this, $this->translate('Is active')))->setDefaultVisible()->setDefaultValue(true),
      'note' => (new Text($this, $this->translate('Note'))),
    ]);
  }

  /**
   * Describes the table (list) of records.
   *
   * @return \Hubleto\Framework\Description\Table
   *
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add price');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);

    $description->addFilter('fActive', [
      'title' => $this->translate('Active'),
      'type' => 'multipleSelectButtons',
      'options' => [ 1 => $this->translate('Active'), 2 => $this->translate('Inactive') ],
    ]);

    $description->addFilter('fValidNow', [
      'title' => $this->translate('Validity'),
      'type' => 'multipleSelectButtons',
      'options' => [ 1 => $this->translate('Valid now'), 2 => $this->translate('Not valid now') ],
    ]);

    $description->addFilter('fMethod', [
      'title' => $this->translate('Method'),
      'type' => 'multipleSelectButtons',
      'options' => [ 1 => $this->translate('Fixed price'), 2 => $this->translate('Percent off base'), 3 => $this->translate('Amount off base'), 4 => $this->translate('Markup on cost'), 5 => $this->translate('Margin on cost') ],
    ]);

    return $description;
  }

  /**
   * Describes the form of a record.
   *
   * @return \Hubleto\Framework\Description\Form
   *
   */
  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    $idFromUrl = $this->router()->urlParamAsInteger('idPriceList');
    if ($idFromUrl > 0) $description->defaultValues['id_price_list'] = $idFromUrl;

    $idFromUrl = $this->router()->urlParamAsInteger('idProduct');
    if ($idFromUrl > 0) $description->defaultValues['id_product'] = $idFromUrl;

    return $description;
  }

  /**
   * Validates that the price has one scope at most and sane quantity range and method values.
   *
   * @param array $record
   *
   * @return array
   *
   */
  public function onBeforeValidate(array $record): array
  {
    $record = parent::onBeforeValidate($record);

    $scopeColumns = ['id_product', 'id_category', 'id_group'];
    $scopes = 0;
    foreach ($scopeColumns as $scopeColumn) {
      if ((int) ($record[$scopeColumn] ?? 0) > 0) $scopes++;
    }
    if ($scopes > 1) {
      throw new RecordSaveException(
        $this->translate('A price can be scoped to only one of: product, category, group, activity type.'),
        $scopeColumns
      );
    }

    $minQty = (float) ($record['min_qty'] ?? 0);
    $maxQty = (float) ($record['max_qty'] ?? 0);
    if ($maxQty > 0 && $maxQty < $minQty) {
      throw new RecordSaveException($this->translate('Maximum quantity must not be lower than minimum quantity.'), ['max_qty']);
    }

    if ((int) ($record['method'] ?? self::METHOD_FIXED) === self::METHOD_MARGIN_ON_COST && (float) ($record['value'] ?? 0) >= 100) {
      throw new RecordSaveException($this->translate('Margin must be lower than 100 %.'), ['value']);
    }

    $validFrom = (string) ($record['valid_from'] ?? '');
    $validTo = (string) ($record['valid_to'] ?? '');
    if ($validFrom !== '' && $validTo !== '' && $validFrom > $validTo) {
      throw new RecordSaveException($this->translate('"Valid from" must not be later than "Valid to".'), ['valid_from', 'valid_to']);
    }

    return $record;
  }

  /**
   * Logs creation of the price.
   *
   * @param array $savedRecord
   *
   * @return array
   *
   */
  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);

    /** @var HistoryWriter $historyWriter */
    $historyWriter = $this->getService(HistoryWriter::class);
    $historyWriter->write(
      (int) $savedRecord['id_price_list'],
      History::EVENT_ITEM_PRICE_CHANGED,
      $this->translate('Price added'),
      ['id_item' => (int) $savedRecord['id'], 'id_product' => (int) ($savedRecord['id_product'] ?? 0), 'new_value' => (float) ($savedRecord['value'] ?? 0)]
    );

    return $savedRecord;
  }

  /**
   * Logs a change of the value or the method.
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

    $valueChanged = (float) ($originalRecord['value'] ?? 0) !== (float) ($savedRecord['value'] ?? 0);
    $methodChanged = (int) ($originalRecord['method'] ?? 0) !== (int) ($savedRecord['method'] ?? 0);

    if ($valueChanged || $methodChanged) {
      /** @var HistoryWriter $historyWriter */
      $historyWriter = $this->getService(HistoryWriter::class);
      $historyWriter->write(
        (int) $savedRecord['id_price_list'],
        History::EVENT_ITEM_PRICE_CHANGED,
        $this->translate('Price changed'),
        [
          'id_item' => (int) $savedRecord['id'],
          'id_product' => (int) ($savedRecord['id_product'] ?? 0),
          'old_value' => (float) ($originalRecord['value'] ?? 0),
          'new_value' => (float) ($savedRecord['value'] ?? 0),
        ]
      );
    }

    return $savedRecord;
  }

}
