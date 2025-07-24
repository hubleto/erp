<?php

namespace HubletoApp\Community\Projects\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Project extends \Hubleto\Framework\RecordManager
{
  public $table = 'projects';

  public function MAIN_DEVELOPER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_main_developer', 'id');
  }

  public function ACCOUNT_MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_account_manager', 'id');
  }

  public function PHASE(): BelongsTo
  {
    return $this->belongsTo(Phase::class, 'id_phase', 'id');
  }

  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \HubletoMain\Loader::getGlobalApp();

    if ($main->urlParamAsInteger("idDeal") > 0) {
      $query = $query->where($this->table . '.id_deal', $main->urlParamAsInteger("idDeal"));
    }

    $defaultFilters = $main->urlParamAsArray("defaultFilters");
    if (isset($defaultFilters["fPhase"]) && count($defaultFilters["fPhase"]) > 0) {
      $query = $query->whereIn("{$this->table}.id_phase", $defaultFilters["fPhase"]);
    }

    return $query;
  }

}
