<?php

namespace Hubleto\App\Community\Orders\Models\RecordManagers;


use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Documents\Models\RecordManagers\Template;
use Hubleto\App\Community\Projects\Models\RecordManagers\ProjectOrder;
use Hubleto\App\Community\Settings\Models\RecordManagers\Currency;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends \Hubleto\Erp\RecordManager
{
  public $table = 'orders';

  /** @return BelongsTo<User, covariant Deal> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

  /** @return HasMany<OrderActivity, covariant Deal> */
  public function ACTIVITIES(): HasMany
  {
    return $this->hasMany(OrderActivity::class, 'id_order', 'id');
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

  /** @return HasMany<OrderProduct, covariant Order> */
  public function PRODUCTS(): HasMany
  {
    return $this->hasMany(OrderProduct::class, 'id_order', 'id');
  }

  /** @return HasMany<OrderDocument, covariant Order> */
  public function DOCUMENTS(): HasMany
  {
    return $this->hasMany(OrderDocument::class, 'id_order', 'id');
  }

  /** @return HasMany<OrderDeal, covariant Order> */
  public function DEALS(): HasMany
  {
    return $this->hasMany(OrderDeal::class, 'id_order', 'id');
  }

  /** @return HasMany<ProjectOrder, covariant Order> */
  public function PROJECTS(): HasMany
  {
    return $this->hasMany(ProjectOrder::class, 'id_order', 'id');
  }

  /** @return HasMany<OrderInvoice, covariant Order> */
  public function INVOICES(): HasMany
  {
    return $this->hasMany(OrderInvoice::class, 'id_order', 'id');
  }

  /** @return HasMany<History, covariant Order> */
  public function HISTORY(): HasMany
  {
    return $this->hasMany(History::class, 'id_order', 'id');
  }

  /** @return HasOne<Customer, covariant Order> */
  public function CUSTOMER(): HasOne
  {
    return $this->hasOne(Customer::class, 'id', 'id_customer');
  }

  /** @return hasOne<Currency, covariant Lead> */
  public function CURRENCY(): HasOne
  {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }

  /** @return hasOne<Currency, covariant Lead> */
  public function TEMPLATE(): HasOne
  {
    return $this->hasOne(Template::class, 'id', 'id_template');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    $filters = $main->router()->urlParamAsArray("filters");

    $query = Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      $filters['fOrderWorkflowStep'] ?? []
    );

    if (isset($filters["fOrderClosed"])) {
      if ($filters["fOrderClosed"] == 0) $query = $query->where("orders.is_closed", false);
      if ($filters["fOrderClosed"] == 1) $query = $query->where("orders.is_closed", true);
    }

    return $query;
  }

}
