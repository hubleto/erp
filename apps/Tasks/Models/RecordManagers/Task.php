<?php

namespace Hubleto\App\Community\Tasks\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;
use Hubleto\App\Community\Pipeline\Models\RecordManagers\Pipeline;
use Hubleto\App\Community\Pipeline\Models\RecordManagers\PipelineStep;
use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;

class Task extends \Hubleto\Erp\RecordManager
{
  public $table = 'tasks';

  public function DEVELOPER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_developer', 'id');
  }

  public function TESTER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_tester', 'id');
  }

  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'id_customer', 'id');
  }

  public function CONTACT(): BelongsTo
  {
    return $this->belongsTo(Contact::class, 'id_contact', 'id');
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

  // /** @return HasMany<DealProduct, covariant Deal> */
  // public function DEALS(): HasMany
  // {
  //   return $this->hasMany(DealTask::class, 'id_task', 'id');
  // }

  // /** @return HasMany<DealProduct, covariant Deal> */
  // public function PROJECTS(): HasMany
  // {
  //   return $this->hasMany(ProjectTask::class, 'id_task', 'id');
  // }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    $filters = $main->getRouter()->urlParamAsArray("filters");

    $query = Pipeline::applyPipelineStepFilter(
      $this->model,
      $query,
      $filters['fTaskPipelineStep'] ?? []
    );

    if (isset($filters["fTaskClosed"])) {
      if ($filters["fTaskClosed"] == 0) $query = $query->where("tasks.is_closed", false);
      if ($filters["fTaskClosed"] == 1) $query = $query->where("tasks.is_closed", true);
    }

    return $query;
  }

}
