<?php

namespace HubletoApp\Community\Cloud\Models;

use ADIOS\Core\Db\Column\Decimal;
use ADIOS\Core\Db\Column\DateTime;

class Credit extends \HubletoMain\Core\Models\Model
{
  public string $table = 'cloud_credit';
  public string $recordManagerClass = RecordManagers\Credit::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'datetime_recalculated' => (new DateTime($this, $this->translate('Recalculated')))->setRequired(),
      'credit' => (new Decimal($this, $this->translate('Credit')))->setRequired(),
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
