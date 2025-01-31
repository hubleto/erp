<?php

namespace HubletoApp\Community\Invoices\Models;

use \HubletoApp\Community\Customers\Models\Company;
use \HubletoApp\Community\Settings\Models\User;

class InvoiceItem extends \ADIOS\Core\Model {
  public string $table = 'invoice_items';
  public ?string $lookupSqlValue = '{%TABLE%}.id_invoice';
  public string $eloquentClass = Eloquent\InvoiceItem::class;

  public array $relations = [
    'INVOICE' => [ self::BELONGS_TO, Invoice::class, "id_invoice" ],
  ];

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns, [
      "id_invoice" => [ "type" => "lookup", "model" => Invoice::class, "title" => $this->translate("Invoice") ],
      "item" => [ "type" => "varchar", "title" => $this->translate("Item") ],
      "unit_price" => [ "type" => "float", "title" => $this->translate("Unit price") ],
      "amount" => [ "type" => "float", "title" => $this->translate("Amount") ],
    ]));
  }
}