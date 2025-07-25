<?php

namespace HubletoApp\Community\Leads\Models;

use Hubleto\Framework\Db\Column\Varchar;

class LostReason extends \Hubleto\Framework\Models\Model
{
  public string $table = 'lead_lost_reasons';
  public string $recordManagerClass = RecordManagers\LostReason::class;
  public ?string $lookupSqlValue = '{%TABLE%}.reason';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'reason' => (new Varchar($this, $this->translate('Lost Reason')))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Lead Lost Reasons';
    $description->ui['addButtonText'] = 'Add Reason';
    return $description;
  }

}
