<?php

namespace HubletoApp\Community\Inventory\Models;

use Hubleto\Framework\Db\Column\Varchar;

class Status extends \Hubleto\Framework\Models\Model
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
