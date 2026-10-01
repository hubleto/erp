<?php

namespace Hubleto\App\Custom\LinkedinMessages\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends \Hubleto\Erp\RecordManager
{
  public $table = 'linkedin_campaigns';

  /** @return BelongsTo<User, covariant self> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return BelongsTo<Workflow, covariant self> */
  public function WORKFLOW(): BelongsTo
  {
    return $this->belongsTo(Workflow::class, 'id_workflow', 'id');
  }

  /** @return BelongsTo<WorkflowStep, covariant self> */
  public function WORKFLOW_STEP(): BelongsTo
  {
    return $this->belongsTo(WorkflowStep::class, 'id_workflow_step', 'id');
  }

  /** @return HasMany<Message, covariant self> */
  public function MESSAGES(): HasMany
  {
    return $this->hasMany(Message::class, 'id_campaign', 'id');
  }

  /**
   * @param mixed|null $query
   * @param int $level
   * @param array|null $includeRelations
   *
   * @return mixed
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    return $query;
  }

  /**
   * @param mixed $query
   *
   * @return mixed
   */
  public function addUrlFiltersToQuery(mixed $query): mixed
  {
    $query = parent::addUrlFiltersToQuery($query);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray('filters');

    $query = Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      (array) ($filters['fCampaignWorkflowStep'] ?? [])
    );

    $fCampaignClosed = $filters['fCampaignClosed'] ?? 1;
    if ($fCampaignClosed == 1) $query = $query->where($this->table . '.is_closed', false);
    if ($fCampaignClosed == 2) $query = $query->where($this->table . '.is_closed', true);

    return $query;
  }

  /**
   * @param array $dataRaw
   *
   * @return array
   */
  public function prepareLookupData(array $dataRaw): array
  {
    $data = parent::prepareLookupData($dataRaw);

    return $data;
  }
}
