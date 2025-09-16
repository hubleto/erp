<?php

namespace Hubleto\App\Community\Workflow\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Hubleto\App\Community\Settings\Models\RecordManagers\User;

class WorkflowHistory extends \Hubleto\Erp\RecordManager
{
  public $table = 'workflow_history';

  public function USER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }
}
