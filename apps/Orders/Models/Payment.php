<?php

namespace Hubleto\App\Community\Orders\Models;

use Hubleto\App\Community\Invoices\Models\Item;
use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

class Payment extends \Hubleto\Erp\Model
{
  public string $table = 'orders_payments';
  public string $recordManagerClass = RecordManagers\Payment::class;
  public ?string $lookupSqlValue = '{%TABLE%}.date_due';
  public ?string $lookupUrlDetail = 'orders/payments/{%ID%}';
  public ?string $lookupUrlAdd = 'orders/payments/add';

  public array $relations = [
    'ORDER' => [ self::BELONGS_TO, Order::class, "id_order" ],
    'INVOICE_ITEM' => [ self::BELONGS_TO, Item::class, "id_invoice_item" ],
  ];

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class)),
      'title' => (new Varchar($this, $this->translate('Title')))->setDefaultVisible(),
      'date_due' => (new Date($this, $this->translate('Due date')))->setDefaultVisible()->setDefaultValue(date("Y-m-d")),
      'unit_price' => new Decimal($this, $this->translate('Unit price'))->setDefaultVisible(),
      'amount' => new Decimal($this, $this->translate('Amount'))->setDefaultVisible(),
      'discount' => new Decimal($this, $this->translate('Discount'))->setDefaultVisible(),
      'vat' => new Decimal($this, $this->translate('VAT'))->setUnit('%'),
      'notes' => (new Text($this, $this->translate('Notes')))->setDefaultVisible(),
      'id_invoice_item' => (new Lookup($this, $this->translate('Invoice item'), Item::class))->setDefaultVisible(),
    ]);
  }

  /**
   * [Description for describeTable]
   *
   * @return \Hubleto\Framework\Description\Table
   * 
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate("Add payment");
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    $description->ui['orderBy'] = [
      'field' => 'date_due',
      'direction' => 'asc',
    ];

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
    return $description;
  }
}
