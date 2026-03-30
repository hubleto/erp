<?php

namespace Hubleto\App\Community\Tasks\Models\RecordManagers;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class Todo extends \Hubleto\Erp\RecordManager
{
  public $table = 'tasks_todo';

  public function RESPONSIBLE(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_responsible', 'id');
  }

  public function TASK(): BelongsTo
  {
    return $this->belongsTo(Task::class, 'id_task', 'id');
  }

  /**
   * [Description for prepareReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * @param array|null|null $includeRelations
   * 
   * @return mixed
   * 
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray("filters");

    if (isset($filters["fTodoClosed"])) {
      if ($filters["fTodoClosed"] == 0) $query = $query->where($this->table . '.is_closed', false);
      if ($filters["fTodoClosed"] == 1) $query = $query->where($this->table . '.is_closed', true);
    }

    if (isset($filters['fResponsible']) && is_array($filters['fResponsible']) && count($filters['fResponsible']) > 0) {
      $query = $query->whereIn($this->table . '.id_responsible', $filters['fResponsible']);
    }

    return $query;
  }
}
