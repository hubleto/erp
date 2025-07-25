<?php

namespace HubletoApp\Community\Suppliers\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;
use HubletoApp\Community\Settings\Models\Country;
use HubletoApp\Community\Contacts\Models\Contact;

class Supplier extends \Hubleto\Framework\Models\Model
{
  public string $table = 'suppliers';
  public string $recordManagerClass = RecordManagers\Supplier::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlDetail = 'Suppliers/{%ID%}';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired(),
      'address' => (new Varchar($this, $this->translate('Address'))),
      'city' => (new Varchar($this, $this->translate('City'))),
      'postal_code' => (new Varchar($this, $this->translate('Postal code'))),
      'id_country' => (new Lookup($this, $this->translate('Country'), Country::class)),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class)),
      'order_email' => (new Varchar($this, $this->translate('Order email'))),
      'tax_id' => (new Varchar($this, $this->translate('Tax ID'))),
      'company_id' => (new Varchar($this, $this->translate('Company ID'))),
      'vat_id' => (new Varchar($this, $this->translate('VAT ID')))->setRequired(),
      'payment_account' => (new Varchar($this, $this->translate('Payment account number'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Suppliers';
    $description->ui["addButtonText"] = $this->translate("Add supplier");

    return $description;
  }
}
