<?php

namespace Hubleto\App\Community\Orders\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;

use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Documents\Models\RecordManagers\Template;
use Hubleto\App\Community\Projects\Models\RecordManagers\ProjectOrder;
use Hubleto\App\Community\Settings\Models\RecordManagers\Currency;
use Hubleto\App\Community\Suppliers\Models\RecordManagers\Supplier;
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

  /** @return HasMany<Item, covariant Order> */
  public function ITEMS(): HasMany
  {
    return $this->hasMany(Item::class, 'id_order', 'id');
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

  /** @return BelongsTo<Customer, covariant Invoice> */
  public function SUPPLIER(): BelongsTo {
    return $this->BelongsTo(Supplier::class, 'id_supplier');
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

  /**
   * [Description for prepareSelectsForReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * @param array|null|null $includeRelations
   * 
   * @return array
   * 
   */
  public function prepareSelectsForReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): array
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray("filters");
    $selects = parent::prepareSelectsForReadQuery($query, $level, $includeRelations);

    if (isset($filters['fGroupBy']) && is_array($filters['fGroupBy'])) {
      $selects[] = 'sum(price_excl_vat) as total_price_excl_vat';
    }

    return $selects;
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
    $view = $hubleto->router()->urlParamAsString("view");

    $query = Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      (array) ($filters['fOrderWorkflowStep'] ?? [])
    );

    if (isset($filters["fOrderClosed"])) {
      if ($filters["fOrderClosed"] == 1) $query = $query->where("orders.is_closed", false);
      if ($filters["fOrderClosed"] == 2) $query = $query->where("orders.is_closed", true);
    }

    if (isset($filters["fPurchaseSales"]) && $filters["fPurchaseSales"] > 0) {
      $query = $query->where("orders.purchase_sales", $filters["fPurchaseSales"]);
    }

    if ($view == 'purchaseOrders') $query = $query->where("orders.purchase_sales", 1);
    if ($view == 'salesOrders') $query = $query->where("orders.purchase_sales", 2);

    if (isset($filters['fGroupBy'])) {
      $fGroupBy = (array) $filters['fGroupBy'];
      if (in_array('customer', $fGroupBy)) $query = $query->groupBy('id_customer');
      if (in_array('supplier', $fGroupBy)) $query = $query->groupBy('id_supplier');
      if (in_array('manager', $fGroupBy)) $query = $query->groupBy('id_manager');
      if (in_array('workflow_step', $fGroupBy)) $query = $query->groupBy('id_workflow_step');
    }

    return $query;
  }

}
