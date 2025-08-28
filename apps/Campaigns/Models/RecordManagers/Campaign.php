<?php

namespace Hubleto\App\Community\Campaigns\Models\RecordManagers;

use Hubleto\App\Community\Settings\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Hubleto\App\Community\Pipeline\Models\RecordManagers\Pipeline;
use Hubleto\App\Community\Pipeline\Models\RecordManagers\PipelineStep;

class Campaign extends \Hubleto\Erp\RecordManager
{
  public $table = 'campaigns';

  /** @return BelongsTo<User, covariant Lead> */
  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

  /** @return HasOne<Pipeline, covariant Deal> */
  public function PIPELINE(): HasOne
  {
    return $this->hasOne(Pipeline::class, 'id', 'id_pipeline');
  }

  /** @return HasOne<PipelineStep, covariant Deal> */
  public function PIPELINE_STEP(): HasOne
  {
    return $this->hasOne(PipelineStep::class, 'id', 'id_pipeline_step');
  }

  /** @return HasMany<DealTask, covariant Deal> */
  public function CONTACTS(): HasMany
  {
    return $this->hasMany(CampaignContact::class, 'id_campaign', 'id');
  }

  /** @return HasMany<DealTask, covariant Deal> */
  public function TASKS(): HasMany
  {
    return $this->hasMany(CampaignTask::class, 'id_campaign', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    $filters = $main->getRouter()->urlParamAsArray("filters");

    $query = Pipeline::applyPipelineStepFilter(
      $this->model,
      $query,
      $filters['fCampaignPipelineStep'] ?? []
    );

    if (isset($filters["fCampaignClosed"])) {
      if ($filters["fCampaignClosed"] == 0) $query = $query->where("campaigns.is_closed", false);
      if ($filters["fCampaignClosed"] == 1) $query = $query->where("campaigns.is_closed", true);
    }

    return $query;
  }

}
