<?php

namespace HubletoApp\Community\Premium\Models;

use ADIOS\Core\Db\Column\Decimal;
use ADIOS\Core\Db\Column\DateTime;

class Payment extends \HubletoMain\Core\Models\Model
{
  public string $table = 'premium_payments';
  public string $recordManagerClass = RecordManagers\Payment::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'datetime_charged' => (new DateTime($this, $this->translate('Charged')))->setRequired(),
      'amount' => (new Decimal($this, $this->translate('Amount')))->setRequired(),
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
