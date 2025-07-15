<?php

namespace HubletoApp\Community\Deals\Models;

use ADIOS\Core\Db\Column\Varchar;

class LostReason extends \HubletoMain\Core\Models\Model
{
  public string $table = 'deal_lost_reasons';
  public string $recordManagerClass = RecordManagers\LostReason::class;
  public ?string $lookupSqlValue = '{%TABLE%}.reason';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'reason' => (new Varchar($this, $this->translate('Lost Reason')))->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Deal Lost Reasons');
    $description->ui['addButtonText'] = $this->translate('Add Reason');
    return $description;
  }

}
