<?php

namespace HubletoApp\Community\Cloud\Models;

use ADIOS\Core\Db\Column\Decimal;
use ADIOS\Core\Db\Column\DateTime;
use ADIOS\Core\Db\Column\Varchar;
use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Integer;
use ADIOS\Core\Db\Column\Json;

class Payment extends \HubletoMain\Core\Models\Model
{

  const TYPE_SUBSCRIPTION_FEE = 1;
  const TYPE_BACK_PAY = 2;
  const TYPE_PAYMENT_BY_CARD = 3;
  const TYPE_SUBSCRIPTION_RENEWAL_ACTIVATED = 4;

  const TYPE_ENUM_VALUES = [
    self::TYPE_SUBSCRIPTION_FEE => 'subscription fee',
    self::TYPE_BACK_PAY => 'back pay',
    self::TYPE_PAYMENT_BY_CARD => 'payment by card',
    self::TYPE_SUBSCRIPTION_RENEWAL_ACTIVATED => 'renewal activated',
  ];

  const TYPE_BACKGROUND_CSS_CLASSES = [
    self::TYPE_SUBSCRIPTION_FEE => 'bg-green-50',
    self::TYPE_BACK_PAY => 'bg-yellow-50',
    self::TYPE_PAYMENT_BY_CARD => 'bg-lime-50',
    self::TYPE_SUBSCRIPTION_RENEWAL_ACTIVATED => 'bg-violet-50',
  ];

  public string $table = 'cloud_payments';
  public string $recordManagerClass = RecordManagers\Payment::class;

  public function addPayment(string $datetime, float $amount, string $notes): void
  {
    $this->record->recordCreate([
      'datetime_charged' => $datetime,
      'full_amount' => $amount,
      'notes' => $notes,
    ]);

  }

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'datetime_charged' => (new DateTime($this, $this->translate('Charged')))->setRequired(),
      'discount_percent' => (new Decimal($this, $this->translate('Discount')))->setUnit('%'),
      'full_amount' => (new Decimal($this, $this->translate('Full amount')))->setUnit('€')->setDecimals(2),
      'discounted_amount' => (new Decimal($this, $this->translate('Discounted amount')))->setUnit('€')->setDecimals(2),
      'type' => (new Integer($this, $this->translate('Type')))->setEnumValues(self::TYPE_ENUM_VALUES),
      'details' => (new Json($this, $this->translate('Details'))),
      'has_invoice' => (new Boolean($this, $this->translate('Has invoice'))),
      'id_billing_account' => (new Lookup($this, $this->translate("Billing account"), BillingAccount::class)),
      'uuid' => (new Varchar($this, $this->translate('UUID'))),
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
