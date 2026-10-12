<?php

namespace Hubleto\App\Community\PriceLists\Models;

use Hubleto\App\Community\Cashdesk\Models\CashRegister;
use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Customers\Models\Tag;
use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Projects\Models\Project;
use Hubleto\App\Community\Settings\Models\Country;
use Hubleto\App\Community\Shops\Models\Shop;
use Hubleto\App\Community\Suppliers\Models\Supplier;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Exceptions\RecordSaveException;

class Assignment extends \Hubleto\Erp\Model
{
  public string $table = 'price_lists_assignments';
  public string $recordManagerClass = RecordManagers\Assignment::class;
  public ?string $lookupSqlValue = 'concat("Assignment #", {%TABLE%}.id)';
  public ?string $lookupUrlAdd = 'price-lists/assignments/add';
  public ?string $lookupUrlDetail = 'price-lists/assignments/{%ID%}';

  public array $relations = [
    'PRICE_LIST' => [ self::BELONGS_TO, PriceList::class, 'id_price_list', 'id' ],
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer', 'id' ],
    'CUSTOMER_TAG' => [ self::BELONGS_TO, Tag::class, 'id_customer_tag', 'id' ],
    'COUNTRY' => [ self::BELONGS_TO, Country::class, 'id_country', 'id' ],
    'SUPPLIER' => [ self::BELONGS_TO, Supplier::class, 'id_supplier', 'id' ],
    'SHOP' => [ self::BELONGS_TO, Shop::class, 'id_shop', 'id' ],
    'CASH_REGISTER' => [ self::BELONGS_TO, CashRegister::class, 'id_cash_register', 'id' ],
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
    'PROJECT' => [ self::BELONGS_TO, Project::class, 'id_project', 'id' ],
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
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setFkOnDelete('CASCADE')->setDefaultVisible(),
      'id_customer_tag' => (new Lookup($this, $this->translate('Customer tag'), Tag::class))->setFkOnDelete('CASCADE')->setDefaultVisible(),
      'id_country' => (new Lookup($this, $this->translate('Country'), Country::class))->setFkOnDelete('CASCADE')->setDefaultVisible(),
      'id_supplier' => (new Lookup($this, $this->translate('Supplier'), Supplier::class))->setFkOnDelete('CASCADE')->setDefaultVisible(),
      'id_shop' => (new Lookup($this, $this->translate('Shop'), Shop::class))->setFkOnDelete('CASCADE')->setDefaultVisible(),
      'id_cash_register' => (new Lookup($this, $this->translate('Cash register'), CashRegister::class))->setFkOnDelete('CASCADE'),
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setFkOnDelete('CASCADE'),
      'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setFkOnDelete('CASCADE'),
      'valid_from' => (new Date($this, $this->translate('Valid from')))->setDefaultVisible(),
      'valid_to' => (new Date($this, $this->translate('Valid to')))->setDefaultVisible(),
      'priority' => (new Integer($this, $this->translate('Priority')))->setDefaultValue(0),
      'is_default_for_target' => (new Boolean($this, $this->translate('Default for the target'))),
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
    $description->ui['addButtonText'] = $this->translate('Add assignment');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);

    $description->addFilter('fValidNow', [
      'title' => $this->translate('Validity'),
      'type' => 'multipleSelectButtons',
      'options' => [ 1 => $this->translate('Valid now'), 2 => $this->translate('Not valid now') ],
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

    $idFromUrl = $this->router()->urlParamAsInteger('idCustomer');
    if ($idFromUrl > 0) $description->defaultValues['id_customer'] = $idFromUrl;

    $idFromUrl = $this->router()->urlParamAsInteger('idShop');
    if ($idFromUrl > 0) $description->defaultValues['id_shop'] = $idFromUrl;

    $idFromUrl = $this->router()->urlParamAsInteger('idProject');
    if ($idFromUrl > 0) $description->defaultValues['id_project'] = $idFromUrl;

    $idFromUrl = $this->router()->urlParamAsInteger('idDeal');
    if ($idFromUrl > 0) $description->defaultValues['id_deal'] = $idFromUrl;

    $idFromUrl = $this->router()->urlParamAsInteger('idSupplier');
    if ($idFromUrl > 0) $description->defaultValues['id_supplier'] = $idFromUrl;

    return $description;
  }

  /**
   * An assignment targets exactly one party (or none, which means a global assignment).
   *
   * @param array $record
   *
   * @return array
   *
   */
  public function onBeforeValidate(array $record): array
  {
    $record = parent::onBeforeValidate($record);

    $targetColumns = ['id_customer', 'id_customer_tag', 'id_country', 'id_supplier', 'id_shop', 'id_cash_register', 'id_deal', 'id_project'];
    $targets = 0;
    foreach ($targetColumns as $targetColumn) {
      if ((int) ($record[$targetColumn] ?? 0) > 0) $targets++;
    }
    if ($targets > 1) {
      throw new RecordSaveException($this->translate('An assignment can have only one target.'), $targetColumns);
    }

    return $record;
  }

}
