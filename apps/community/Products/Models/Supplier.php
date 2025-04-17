<?php

namespace HubletoApp\Community\Products\Models;

use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Varchar;
use HubletoApp\Community\Settings\Models\Country;

class Supplier extends \HubletoMain\Core\Models\Model
{
  public string $table = 'product_suppliers';
  public string $recordManagerClass = RecordManagers\Supplier::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired(),
      'address' => (new Varchar($this, $this->translate('Address'))),
      'city' => (new Varchar($this, $this->translate('City'))),
      'postal_code' => (new Varchar($this, $this->translate('Postal code'))),
      'id_country' => (new Lookup($this, $this->translate('Country'), Country::class))->setFkOnUpdate("CASCADE")->setFkOnDelete("SET NULL"),
      'contact_person' => (new Varchar($this, $this->translate('Contact person'))),
      'phone_number' => (new Varchar($this, $this->translate('Phone number'))),
      'email' => (new Varchar($this, $this->translate('Supplier email'))),
      'order_email' => (new Varchar($this, $this->translate('Order email'))),
      'tax_id' => (new Varchar($this, $this->translate('Tax ID'))),
      'company_id' => (new Varchar($this, $this->translate('Company ID'))),
      'vat_id' => (new Varchar($this, $this->translate('VAT ID')))->setRequired(),
      'payment_account' => (new Varchar($this, $this->translate('Payment account number'))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Product Suppliers';
    $description->ui["addButtonText"] = $this->translate("Add product supplier");

    unset($description->columns["address"]);
    unset($description->columns["city"]);
    unset($description->columns["postal_code"]);
    unset($description->columns["id_country"]);
    unset($description->columns["tax_id"]);
    unset($description->columns["company_id"]);
    unset($description->columns["vat_id"]);
    unset($description->columns["payment_account"]);

    return $description;
  }
}
