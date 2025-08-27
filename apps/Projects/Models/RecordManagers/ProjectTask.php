<?php

namespace HubletoApp\Community\Projects\Models\RecordManagers;

use HubletoApp\Community\Tasks\Models\RecordManagers\Task;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTask extends \HubletoMain\RecordManager
{
  public $table = 'projects_tasks';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function PROJECT(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'id_project', 'id');
  }

  /** @return BelongsTo<Task, covariant LeadTag> */
  public function TASK(): BelongsTo
  {
    return $this->belongsTo(Task::class, 'id_task', 'id');
  }

}
