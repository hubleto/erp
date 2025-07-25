<?php

namespace HubletoApp\Community\Cloud\Models;

use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Varchar;
use HubletoApp\Community\Settings\Models\User;

class BillingAccount extends \Hubleto\Framework\Models\Model
{
  public string $table = 'cloud_billing_accounts';
  public string $recordManagerClass = RecordManagers\BillingAccount::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name'))),
      'phone' => (new Varchar($this, $this->translate('Phone'))),
      'email' => (new Varchar($this, $this->translate('E-mail'))),
      'tax_id' => (new Varchar($this, $this->translate('Tax ID'))),
      'vat_id' => (new Varchar($this, $this->translate('VAT ID'))),
      'street_1' => (new Varchar($this, $this->translate('Street, line 1'))),
      'street_2' => (new Varchar($this, $this->translate('Street, line 2'))),
      'zip' => (new Varchar($this, $this->translate('ZIP'))),
      'city' => (new Varchar($this, $this->translate('City'))),
      'country' => (new Varchar($this, $this->translate('Country'))),
      'is_active' => (new Boolean($this, $this->translate('Is active'))),
      'datetime_created' => (new DateTime($this, $this->translate('Created'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->columns['id'] = $this->columns['id'];
    $description->permissions['canCreate'] = false;
    $description->permissions['canUpdate'] = false;
    $description->permissions['canDelete'] = false;
    return $description;
  }

}
