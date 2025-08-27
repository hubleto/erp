<?php

namespace Hubleto\App\Community\Worksheets\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;
use Hubleto\App\Community\Tasks\Models\RecordManagers\Task;

class Activity extends \Hubleto\Erp\RecordManager
{
  public $table = 'worksheet_activities';

  public function WORKER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_worker', 'id');
  }

  public function TASK(): BelongsTo
  {
    return $this->belongsTo(Task::class, 'id_task', 'id');
  }

  public function TYPE(): BelongsTo
  {
    return $this->belongsTo(ActivityType::class, 'id_type', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    if ($main->getRouter()->urlParamAsInteger("idTask") > 0) {
      $query = $query->where($this->table . '.id_task', $main->getRouter()->urlParamAsInteger("idTask"));
    }

    // Uncomment and modify these lines if you want to apply default filters to your model.
    // $defaultFilters = $main->getRouter()->urlParamAsArray("defaultFilters");
    // if (isset($defaultFilters["fArchive"]) && $defaultFilters["fArchive"] == 1) $query = $query->where("customers.is_active", false);
    // else $query = $query->where("customers.is_active", true);

    return $query;
  }

}
