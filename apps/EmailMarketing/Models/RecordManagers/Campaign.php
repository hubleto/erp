<?php

namespace Hubleto\App\Community\EmailMarketing\Models\RecordManagers;

use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class Campaign extends \Hubleto\Erp\RecordManager
{
  public $table = 'email_marketing_campaigns';

  /** @return BelongsTo<User, covariant Lead> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

  /** @return HasOne<Workflow, covariant Deal> */
  public function WORKFLOW(): HasOne
  {
    return $this->hasOne(Workflow::class, 'id', 'id_workflow');
  }

  /** @return HasOne<WorkflowStep, covariant Deal> */
  public function WORKFLOW_STEP(): HasOne
  {
    return $this->hasOne(WorkflowStep::class, 'id', 'id_workflow_step');
  }

  /** @return HasMany<DealTask, covariant Deal> */
  public function SCHEDULES(): HasMany
  {
    return $this->hasMany(CampaignSchedule::class, 'id_campaign', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $filters = $hubleto->router()->urlParamAsArray("filters");

    $query = Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      (array) ($filters['fCampaignWorkflowStep'] ?? [])
    );

    if (isset($filters["fCampaignOwnership"])) {
      /** @var \Hubleto\Framework\AuthProvider */
      $authProvider = $hubleto->getService(\Hubleto\Framework\AuthProvider::class);
      $idUser = $authProvider->getUserId();

      switch ($filters["fCampaignOwnership"]) {
        case 1: $query = $query->where("email_marketing_campaigns.id_owner", $idUser); break;
        case 2: $query = $query->where("email_marketing_campaigns.id_manager", $idUser); break;
      }
    }

    if (isset($filters["fCampaignClosed"])) {
      if ($filters["fCampaignClosed"] == 0) $query = $query->where("email_marketing_campaigns.is_closed", false);
      if ($filters["fCampaignClosed"] == 1) $query = $query->where("email_marketing_campaigns.is_closed", true);
    }

    return $query;
  }

}
