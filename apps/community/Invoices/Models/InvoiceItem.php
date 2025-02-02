<?php

namespace HubletoApp\Community\Invoices\Models;

use \HubletoApp\Community\Customers\Models\Company;
use \HubletoApp\Community\Settings\Models\User;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Decimal;

class InvoiceItem extends \ADIOS\Core\Model {
  public string $table = 'invoice_items';
  public ?string $lookupSqlValue = '{%TABLE%}.id_invoice';
  public string $eloquentClass = Eloquent\InvoiceItem::class;

  public array $relations = [
    'INVOICE' => [ self::BELONGS_TO, Invoice::class, "id_invoice" ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_invoice' => (new Lookup($this, $this->translate('Invoice'), Invoice::class))->setRequired(),
      'item' => (new Varchar($this, $this->translate('Item')))->setRequired(),
      'unit_price' => new Decimal($this, $this->translate('Unit price')),
      'amount' => new Decimal($this, $this->translate('Amount')),
    ]));
  }
}