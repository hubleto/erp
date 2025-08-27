<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Tasks\Models\RecordManagers\Task;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealTask extends \HubletoMain\RecordManager
{
  public $table = 'deals_tasks';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function DEAL(): BelongsTo
  {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }

  /** @return BelongsTo<Task, covariant LeadTag> */
  public function TASK(): BelongsTo
  {
    return $this->belongsTo(Task::class, 'id_task', 'id');
  }

}
