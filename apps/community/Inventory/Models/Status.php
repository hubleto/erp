<?php

namespace HubletoApp\Community\Inventory\Models;

use \ADIOS\Core\Db\Column\Varchar;

class Status extends \HubletoMain\Core\Models\Model
{

  public string $table = 'inventory_status';
  public string $recordManagerClass = RecordManagers\Status::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name'))),
    ]);
  }

}
