<?php

namespace Hubleto\App\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Invoices\Models\Invoice;

class OrderInvoice extends \Hubleto\Erp\Model
{
  public string $table = 'orders_invoices';
  public string $recordManagerClass = RecordManagers\OrderInvoice::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER'   => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
    'INVOICE' => [ self::BELONGS_TO, Invoice::class, 'id_invoice', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
      'id_invoice' => (new Lookup($this, $this->translate('Invoice'), Invoice::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Order Invoices';
    $description->ui["addButtonText"] = $this->translate("Add invoice");

    if ($this->getRouter()->urlParamAsInteger('idOrder') > 0) {
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }
}
