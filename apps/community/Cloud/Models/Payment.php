<?php

namespace HubletoApp\Community\Cloud\Models;

use ADIOS\Core\Db\Column\Decimal;
use ADIOS\Core\Db\Column\DateTime;
use ADIOS\Core\Db\Column\Varchar;
use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Boolean;

class Payment extends \HubletoMain\Core\Models\Model
{
  public string $table = 'cloud_payments';
  public string $recordManagerClass = RecordManagers\Payment::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'datetime_charged' => (new DateTime($this, $this->translate('Charged')))->setRequired(),
      'discount_percent' => (new Decimal($this, $this->translate('Discount')))->setUnit('%'),
      'full_amount' => (new Decimal($this, $this->translate('Full amount')))->setUnit('€')->setDecimals(2),
      'discounted_amount' => (new Decimal($this, $this->translate('Discounted amount')))->setUnit('€')->setDecimals(2),
      'notes' => (new Varchar($this, $this->translate('Notes'))),
      'has_invoice' => (new Boolean($this, $this->translate('Has invoice'))),
      'id_billing_account' => (new Lookup($this, $this->translate("Billing account"), BillingAccount::class, 'CASCADE')),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->columns['id'] = $this->columns['id'];
    $description->permissions['canCreate'] = false;
    $description->permissions['canUpdate'] = false;
    $description->permissions['canDelete'] = false;
    return $description;
  }

}
