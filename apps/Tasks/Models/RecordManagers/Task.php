<?php

namespace HubletoApp\Community\Tasks\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use HubletoApp\Community\Projects\Models\RecordManagers\Project;

class Task extends \Hubleto\Framework\RecordManager
{
  public $table = 'tasks';

  public function PROJECT(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'id_project', 'id');
  }

  public function DEVELOPER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_developer', 'id');
  }

  public function TESTER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_tester', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \HubletoMain\Loader::getGlobalApp();

    $externalModel = $main->urlParamAsString("externalModel");
    $externalId = $main->urlParamAsInteger("externalId");
    if (!empty($externalModel) && $externalId > 0) {
      $query = $query
        ->where($this->table . '.external_model', $externalModel)
        ->where($this->table . '.external_id', $externalId)
      ;
    }

    $defaultFilters = $main->urlParamAsArray("defaultFilters");
    if (isset($defaultFilters["fExternalModels"]) && count($defaultFilters["fExternalModels"]) > 0) {
      $query = $query->whereIn("tasks.external_model", $defaultFilters["fExternalModels"]);
    }

    return $query;
  }

}
