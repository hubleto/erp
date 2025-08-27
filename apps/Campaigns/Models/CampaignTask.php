<?php

namespace HubletoApp\Community\Campaigns\Models;

use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Tasks\Models\Task;

class CampaignTask extends \HubletoMain\Model
{
  public string $table = 'campaigns_tasks';
  public string $recordManagerClass = RecordManagers\CampaignTask::class;

  public array $relations = [
    'CAMPAIGN' => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id' ],
    'TASK' => [ self::BELONGS_TO, Task::class, 'id_task', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setRequired(),
      'id_task' => (new Lookup($this, $this->translate('Task'), Task::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Add campaign';
    return $description;
  }

}
