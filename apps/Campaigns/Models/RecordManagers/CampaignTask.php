<?php

namespace HubletoApp\Community\Campaigns\Models\RecordManagers;

use HubletoApp\Community\Tasks\Models\RecordManagers\Task;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignTask extends \HubletoMain\RecordManager
{
  public $table = 'campaigns_tasks';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function CAMPAIGN(): BelongsTo
  {
    return $this->belongsTo(Campaign::class, 'id_campaign', 'id');
  }

  /** @return BelongsTo<Task, covariant LeadTag> */
  public function TASK(): BelongsTo
  {
    return $this->belongsTo(Task::class, 'id_task', 'id');
  }

}
