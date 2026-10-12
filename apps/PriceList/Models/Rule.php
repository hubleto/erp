<?php

namespace Hubleto\App\Community\PriceLists\Models;

use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Customers\Models\Tag;
use Hubleto\App\Community\Products\Models\Category;
use Hubleto\App\Community\Products\Models\Group;
use Hubleto\App\Community\Products\Models\Product;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Exceptions\RecordSaveException;

class Rule extends \Hubleto\Erp\Model
{
  public const KIND_PERCENT_OFF = 1;
  public const KIND_AMOUNT_OFF = 2;
  public const KIND_PERCENT_SURCHARGE = 3;
  public const KIND_AMOUNT_SURCHARGE = 4;
  public const KIND_SET_PRICE = 5;

  public string $table = 'price_lists_rules';
  public string $recordManagerClass = RecordManagers\Rule::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlAdd = 'price-lists/rules/add';
  public ?string $lookupUrlDetail = 'price-lists/rules/{%ID%}';

  public array $relations = [
    'PRICE_LIST' => [ self::BELONGS_TO, PriceList::class, 'id_price_list', 'id' ],
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
    'CATEGORY' => [ self::BELONGS_TO, Category::class, 'id_category', 'id' ],
    'GROUP' => [ self::BELONGS_TO, Group::class, 'id_group', 'id' ],
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer', 'id' ],
    'CUSTOMER_TAG' => [ self::BELONGS_TO, Tag::class, 'id_customer_tag', 'id' ],
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
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setDefaultVisible()->setCssClass('font-bold'),
      'id_price_list' => (new Lookup($this, $this->translate('Price list'), PriceList::class))->setFkOnDelete('CASCADE')->setDefaultVisible(),
      'kind' => (new Integer($this, $this->translate('Kind')))->setDefaultVisible()->setDefaultValue(self::KIND_PERCENT_OFF)->setEnumValues([self::KIND_PERCENT_OFF => $this->translate('Percent off'), self::KIND_AMOUNT_OFF => $this->translate('Amount off'), self::KIND_PERCENT_SURCHARGE => $this->translate('Percent surcharge'), self::KIND_AMOUNT_SURCHARGE => $this->translate('Amount surcharge'), self::KIND_SET_PRICE => $this->translate('Set price')]),
      'value' => (new Decimal($this, $this->translate('Value')))->setDefaultVisible(),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setFkOnDelete('CASCADE'),
      'id_category' => (new Lookup($this, $this->translate('Category'), Category::class))->setFkOnDelete('CASCADE'),
      'id_group' => (new Lookup($this, $this->translate('Group'), Group::class))->setFkOnDelete('CASCADE'),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setFkOnDelete('CASCADE'),
      'id_customer_tag' => (new Lookup($this, $this->translate('Customer tag'), Tag::class))->setFkOnDelete('CASCADE'),
      'min_qty' => (new Decimal($this, $this->translate('Minimum quantity')))->setUnit('x'),
      'max_qty' => (new Decimal($this, $this->translate('Maximum quantity')))->setUnit('x'),
      'min_amount' => (new Decimal($this, $this->translate('Minimum amount'))),
      'max_amount' => (new Decimal($this, $this->translate('Maximum amount'))),
      'valid_from' => (new Date($this, $this->translate('Valid from')))->setDefaultVisible(),
      'valid_to' => (new Date($this, $this->translate('Valid to')))->setDefaultVisible(),
      'priority' => (new Integer($this, $this->translate('Priority')))->setDefaultValue(0),
      'is_stackable' => (new Boolean($this, $this->translate('Is stackable'))),
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
    $description->ui['addButtonText'] = $this->translate('Add rule');
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

    $description->addFilter('fKind', [
      'title' => $this->translate('Kind'),
      'type' => 'multipleSelectButtons',
      'options' => [ 1 => $this->translate('Percent off'), 2 => $this->translate('Amount off'), 3 => $this->translate('Percent surcharge'), 4 => $this->translate('Amount surcharge'), 5 => $this->translate('Set price') ],
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

    return $description;
  }

  /**
   * A rule has one scope at most and a sane quantity range.
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
      throw new RecordSaveException($this->translate('A rule can be scoped to only one of: product, category, group.'), $scopeColumns);
    }

    $minQty = (float) ($record['min_qty'] ?? 0);
    $maxQty = (float) ($record['max_qty'] ?? 0);
    if ($maxQty > 0 && $maxQty < $minQty) {
      throw new RecordSaveException($this->translate('Maximum quantity must not be lower than minimum quantity.'), ['max_qty']);
    }

    return $record;
  }

}
