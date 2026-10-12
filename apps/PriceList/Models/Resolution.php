<?php

namespace Hubleto\App\Community\PriceLists\Models;

use Hubleto\App\Community\Products\Models\Product;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

class Resolution extends \Hubleto\Erp\Model
{
  public string $table = 'price_lists_resolutions';
  public string $recordManagerClass = RecordManagers\Resolution::class;
  public ?string $lookupSqlValue = 'concat({%TABLE%}.model, " #", ifnull({%TABLE%}.record_id, 0))';
  public ?string $lookupUrlAdd = 'price-lists/resolutions/add';
  public ?string $lookupUrlDetail = 'price-lists/resolutions/{%ID%}';

  public array $relations = [
    'PRICE_LIST' => [ self::BELONGS_TO, PriceList::class, 'id_price_list', 'id' ],
    'ITEM' => [ self::BELONGS_TO, Item::class, 'id_item', 'id' ],
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
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
      'model' => (new Varchar($this, $this->translate('Model')))->setReadonly()->setDefaultVisible(),
      'record_id' => (new Integer($this, $this->translate('Document item')))->setReadonly()->setDefaultVisible(),
      'parent_model' => (new Varchar($this, $this->translate('Parent model')))->setReadonly(),
      'parent_record_id' => (new Integer($this, $this->translate('Parent document')))->setReadonly()->setDefaultVisible(),
      'id_price_list' => (new Lookup($this, $this->translate('Price list'), PriceList::class))->setFkOnDelete('SET NULL')->setReadonly()->setDefaultVisible(),
      'id_item' => (new Lookup($this, $this->translate('Price'), Item::class))->setFkOnDelete('SET NULL')->setReadonly(),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setFkOnDelete('SET NULL')->setReadonly()->setDefaultVisible(),
      'qty' => (new Decimal($this, $this->translate('Quantity')))->setReadonly()->setDefaultVisible(),
      'base_price' => (new Decimal($this, $this->translate('Base price')))->setReadonly(),
      'unit_price' => (new Decimal($this, $this->translate('Resolved unit price')))->setReadonly()->setDefaultVisible(),
      'discount_percent' => (new Decimal($this, $this->translate('Discount')))->setReadonly()->setUnit('%'),
      'item_unit_price' => (new Decimal($this, $this->translate('Unit price on the item')))->setReadonly()->setDefaultVisible(),
      'deviates' => (new Boolean($this, $this->translate('Deviates')))->setReadonly()->setDefaultVisible(),
      'trace' => (new Json($this, $this->translate('Trace')))->setReadonly(),
      'date_time' => (new DateTime($this, $this->translate('Date and time')))->setReadonly()->setDefaultVisible(),
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
    $description->ui['addButtonText'] = $this->translate('Add resolution');
    $description->ui['showAddButton'] = false;
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);

    $description->addFilter('fDeviates', [
      'title' => $this->translate('Deviation'),
      'type' => 'multipleSelectButtons',
      'options' => [ 1 => $this->translate('Deviates'), 2 => $this->translate('Does not deviate') ],
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
