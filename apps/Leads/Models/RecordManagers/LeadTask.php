<?php

namespace HubletoApp\Community\Leads\Models\RecordManagers;

use HubletoApp\Community\Tasks\Models\RecordManagers\Task;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadTask extends \HubletoMain\RecordManager
{
  public $table = 'leads_tasks';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function LEAD(): BelongsTo
  {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }

  /** @return BelongsTo<Task, covariant LeadTag> */
  public function TASK(): BelongsTo
  {
    return $this->belongsTo(Task::class, 'id_task', 'id');
  }

}
