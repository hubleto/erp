<?php

namespace Hubleto\App\Community\Campaigns\Models\RecordManagers;


use Hubleto\App\Community\Mail\Models\RecordManagers\Account;
use Hubleto\App\Community\Mail\Models\RecordManagers\Template;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class Campaign extends \Hubleto\Erp\RecordManager
{
  public $table = 'campaigns';

  /** @return BelongsTo<User, covariant Lead> */
  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function LAUNCHED_BY(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_launched_by', 'id');
  }

  /** @return HasOne<Account, covariant Deal> */
  public function MAIL_ACCOUNT(): HasOne
  {
    return $this->hasOne(Account::class, 'id', 'id_mail_account');
  }

  /** @return HasOne<Template, covariant Deal> */
  public function MAIL_TEMPLATE(): HasOne
  {
    return $this->hasOne(Template::class, 'id', 'id_mail_template');
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
  public function RECIPIENTS(): HasMany
  {
    return $this->hasMany(Recipient::class, 'id_campaign', 'id');
  }

  /** @return HasMany<DealTask, covariant Deal> */
  public function TASKS(): HasMany
  {
    return $this->hasMany(CampaignTask::class, 'id_campaign', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $filters = $hubleto->router()->urlParamAsArray("filters");

    $query = Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      $filters['fCampaignWorkflowStep'] ?? []
    );

    if (isset($filters["fCampaignClosed"])) {
      if ($filters["fCampaignClosed"] == 0) $query = $query->where("campaigns.is_closed", false);
      if ($filters["fCampaignClosed"] == 1) $query = $query->where("campaigns.is_closed", true);
    }

    return $query;
  }

}
