<?php

namespace Hubleto\App\Community\Suppliers\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\App\Community\Settings\Models\Country;
use Hubleto\App\Community\Contacts\Models\Contact;

class Supplier extends \Hubleto\Erp\Model
{
  public string $table = 'suppliers';
  public string $recordManagerClass = RecordManagers\Supplier::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlDetail = 'suppliers/{%ID%}';

  public array $relations = [
    'COUNTRY' => [ self::BELONGS_TO, Country::class, 'id_country', 'id'  ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'address' => (new Varchar($this, $this->translate('Address')))->setIcon(self::COLUMN_ADDRESS_DEFAULT_ICON),
      'city' => (new Varchar($this, $this->translate('City'))),
      'postal_code' => (new Varchar($this, $this->translate('Postal code'))),
      'id_country' => (new Lookup($this, $this->translate('Country'), Country::class)),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setIcon(self::COLUMN_CONTACT_DEFAULT_ICON),
      'order_email' => (new Varchar($this, $this->translate('Order email'))),
      'tax_id' => (new Varchar($this, $this->translate('Tax ID'))),
      'company_id' => (new Varchar($this, $this->translate('Company ID'))),
      'vat_id' => (new Varchar($this, $this->translate('VAT ID'))),
      'payment_account' => (new Varchar($this, $this->translate('Payment account number'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Suppliers';
    $description->ui["addButtonText"] = $this->translate("Add supplier");

    $fCountryOptions = [];
    foreach ($this->record->groupBy('id_country')->with('COUNTRY')->get() as $value) {
      if ($value->COUNTRY) $fCountryOptions[$value->id] = $value->COUNTRY->name;
    }
    $description->addFilter('fSupplierCountry', [
      'title' => $this->translate('Country'),
      'type' => 'multipleSelectButtons',
      'options' => $fCountryOptions,
    ]);
    
    return $description;
  }
}
