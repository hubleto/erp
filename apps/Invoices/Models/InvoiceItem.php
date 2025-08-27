<?php

namespace Hubleto\App\Community\Invoices\Models;

use \Hubleto\App\Community\Customers\Models\Customer;
use \Hubleto\App\Community\Settings\Models\User;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;

class InvoiceItem extends \Hubleto\Erp\Model {
  public string $table = 'invoice_items';
  public ?string $lookupSqlValue = '{%TABLE%}.id_invoice';
  public string $recordManagerClass = RecordManagers\InvoiceItem::class;

  public array $relations = [
    'INVOICE' => [ self::BELONGS_TO, Invoice::class, "id_invoice" ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_invoice' => (new Lookup($this, $this->translate('Invoice'), Invoice::class))->setRequired(),
      'item' => (new Varchar($this, $this->translate('Item')))->setRequired(),
      'unit_price' => new Decimal($this, $this->translate('Unit price')),
      'amount' => new Decimal($this, $this->translate('Amount')),
    ]);
  }

}