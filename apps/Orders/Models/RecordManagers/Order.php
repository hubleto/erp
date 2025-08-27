<?php

namespace HubletoApp\Community\Orders\Models\RecordManagers;

use HubletoApp\Community\Projects\Models\RecordManagers\ProjectOrder;
use HubletoApp\Community\Documents\Models\RecordManagers\Template;
use HubletoApp\Community\Customers\Models\RecordManagers\Customer;
use HubletoApp\Community\Settings\Models\RecordManagers\Currency;
use HubletoApp\Community\Pipeline\Models\RecordManagers\Pipeline;
use HubletoApp\Community\Pipeline\Models\RecordManagers\PipelineStep;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

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

    $defaultFilters = $main->getRouter()->urlParamAsArray("defaultFilters");

    $query = Pipeline::applyPipelineStepDefaultFilter(
      $this->model,
      $query,
      $defaultFilters['fOrderPipelineStep'] ?? []
    );

    return $query;
  }

}
