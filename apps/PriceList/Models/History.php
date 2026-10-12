<?php

namespace Hubleto\App\Community\PriceLists\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Products\Models\Product;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

class History extends \Hubleto\Erp\Model
{
  public const EVENT_CREATED = 1;
  public const EVENT_ACTIVATED = 2;
  public const EVENT_ARCHIVED = 3;
  public const EVENT_ITEM_PRICE_CHANGED = 4;
  public const EVENT_BULK_ADJUSTED = 5;
  public const EVENT_COPIED = 6;
  public const EVENT_SOURCE_PRICE_CHANGED = 7;
  public const EVENT_MARGIN_FLOOR_HIT = 8;
  public const EVENT_DRAFTED = 9;

  public string $table = 'price_lists_history';
  public string $recordManagerClass = RecordManagers\History::class;
  public ?string $lookupSqlValue = '{%TABLE%}.short_description';
  public ?string $lookupUrlAdd = 'price-lists/histories/add';
  public ?string $lookupUrlDetail = 'price-lists/histories/{%ID%}';

  public array $relations = [
    'PRICE_LIST' => [ self::BELONGS_TO, PriceList::class, 'id_price_list', 'id' ],
    'ITEM' => [ self::BELONGS_TO, Item::class, 'id_item', 'id' ],
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_user', 'id' ],
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
      'id_price_list' => (new Lookup($this, $this->translate('Price list'), PriceList::class))->setFkOnDelete('CASCADE')->setRequired()->setReadonly()->setDefaultVisible(),
      'id_item' => (new Lookup($this, $this->translate('Price'), Item::class))->setFkOnDelete('SET NULL')->setReadonly(),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setFkOnDelete('SET NULL')->setReadonly()->setDefaultVisible(),
      'event_type' => (new Integer($this, $this->translate('Event')))->setReadonly()->setDefaultVisible()->setEnumValues([self::EVENT_CREATED => $this->translate('Created'), self::EVENT_ACTIVATED => $this->translate('Activated'), self::EVENT_ARCHIVED => $this->translate('Archived'), self::EVENT_ITEM_PRICE_CHANGED => $this->translate('Price changed'), self::EVENT_BULK_ADJUSTED => $this->translate('Bulk adjusted'), self::EVENT_COPIED => $this->translate('Copied'), self::EVENT_SOURCE_PRICE_CHANGED => $this->translate('Source price changed'), self::EVENT_MARGIN_FLOOR_HIT => $this->translate('Margin floor hit'), self::EVENT_DRAFTED => $this->translate('Set to draft')]),
      'old_value' => (new Decimal($this, $this->translate('Old value')))->setReadonly()->setDefaultVisible(),
      'new_value' => (new Decimal($this, $this->translate('New value')))->setReadonly()->setDefaultVisible(),
      'short_description' => (new Varchar($this, $this->translate('Description')))->setReadonly()->setDefaultVisible(),
      'date_time' => (new DateTime($this, $this->translate('Date and time')))->setReadonly()->setDefaultVisible(),
      'id_user' => (new Lookup($this, $this->translate('User'), User::class))->setFkOnDelete('SET NULL')->setReadonly()->setDefaultVisible(),
      'source' => (new Varchar($this, $this->translate('Source')))->setReadonly()->setDefaultVisible(),
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
    $description->ui['addButtonText'] = $this->translate('Add history record');
    $description->ui['showAddButton'] = false;
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);

    $description->addFilter('fEvent', [
      'title' => $this->translate('Event'),
      'type' => 'multipleSelectButtons',
      'options' => [ 1 => $this->translate('Created'), 2 => $this->translate('Activated'), 3 => $this->translate('Archived'), 4 => $this->translate('Price changed'), 5 => $this->translate('Bulk adjusted'), 6 => $this->translate('Copied'), 7 => $this->translate('Source price changed'), 8 => $this->translate('Margin floor hit'), 9 => $this->translate('Set to draft') ],
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

}
