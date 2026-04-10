<?php

namespace Hubleto\App\Community\Issues\Models\RecordManagers;

use Hubleto\App\Community\Tasks\Models\RecordManagers\Task;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueTask extends \Hubleto\Erp\RecordManager
{
  public $table = 'issues_tasks';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function ISSUE(): BelongsTo
  {
    return $this->belongsTo(Issue::class, 'id_issue', 'id');
  }

  /** @return BelongsTo<Task, covariant LeadTag> */
  public function TASK(): BelongsTo
  {
    return $this->belongsTo(Task::class, 'id_task', 'id');
  }

}
