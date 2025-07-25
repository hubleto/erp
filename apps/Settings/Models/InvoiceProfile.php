<?php

namespace HubletoApp\Community\Settings\Models;

use Hubleto\Framework\Db\Column\Varchar;

class InvoiceProfile extends \Hubleto\Framework\Models\Model
{
  public string $table = 'invoice_profiles';
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public string $recordManagerClass = RecordManagers\InvoiceProfile::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name'))),
      'phone' => (new Varchar($this, $this->translate('Phone'))),
      'email' => (new Varchar($this, $this->translate('E-mail'))),
      'www' => (new Varchar($this, $this->translate('WWW'))),
      'customer_id' => (new Varchar($this, $this->translate('Customer ID'))),
      'tax_id' => (new Varchar($this, $this->translate('Tax ID'))),
      'vat_id' => (new Varchar($this, $this->translate('VAT ID'))),
      'street_1' => (new Varchar($this, $this->translate('Street, line 1'))),
      'street_2' => (new Varchar($this, $this->translate('Street, line 2'))),
      'zip' => (new Varchar($this, $this->translate('ZIP'))),
      'city' => (new Varchar($this, $this->translate('City'))),
      'country' => (new Varchar($this, $this->translate('Country'))),
      'bank_name' => (new Varchar($this, $this->translate('Bank name'))),
      'account_number' => (new Varchar($this, $this->translate('Account number'))),
      'account_iban' => (new Varchar($this, $this->translate('Account IBAN'))),
      'swift' => (new Varchar($this, $this->translate('SWIFT'))),
      'numbering_pattern' => (new Varchar($this, $this->translate('Numbering pattern'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['addButtonText'] = "Add invoice profile";

    unset($description->columns['phone']);
    unset($description->columns['email']);
    unset($description->columns['www']);
    unset($description->columns['tax_id']);
    unset($description->columns['vat_id']);
    unset($description->columns['street_1']);
    unset($description->columns['street_2']);
    unset($description->columns['zip']);
    unset($description->columns['city']);
    unset($description->columns['country']);
    unset($description->columns['bank_name']);
    unset($description->columns['account_number']);
    unset($description->columns['swift']);

    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    $description->ui['title'] = 'Invoice profile';

    return $description;
  }

}
