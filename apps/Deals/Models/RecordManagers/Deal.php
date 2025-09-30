<?php

namespace Hubleto\App\Community\Deals\Models\RecordManagers;


use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;
use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Documents\Models\RecordManagers\Template;
use Hubleto\App\Community\Leads\Models\RecordManagers\Lead;
use Hubleto\App\Community\Settings\Models\RecordManagers\Currency;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Deal extends \Hubleto\Erp\RecordManager
{
  public $table = 'deals';

  /** @return BelongsTo<Customer, covariant Deal> */
  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'id_customer', 'id');
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

  /** @return BelongsTo<Lead, covariant Deal> */
  public function LEAD(): BelongsTo
  {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }

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

  /** @return HasOne<Contact, covariant Deal> */
  public function CONTACT(): HasOne
  {
    return $this->hasOne(Contact::class, 'id', 'id_contact');
  }

  /** @return HasOne<Currency, covariant Deal> */
  public function CURRENCY(): HasOne
  {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }

  /** @return HasMany<DealHistory, covariant Deal> */
  public function HISTORY(): HasMany
  {
    return $this->hasMany(DealHistory::class, 'id_deal', 'id');
  }

  /** @return HasMany<DealTag, covariant Deal> */
  public function TAGS(): HasMany
  {
    return $this->hasMany(DealTag::class, 'id_deal', 'id');
  }

  /** @return HasMany<DealProduct, covariant Deal> */
  public function PRODUCTS(): HasMany
  {
    return $this->hasMany(DealProduct::class, 'id_deal', 'id');
  }

  /** @return HasMany<DealTask, covariant Deal> */
  public function TASKS(): HasMany
  {
    return $this->hasMany(DealTask::class, 'id_deal', 'id');
  }

  // /** @return HasMany<DealProduct, covariant Deal> */
  // public function ORDERS(): HasMany
  // {
  //   return $this->hasMany(OrderDeal::class, 'id_deal', 'id');
  // }

  /** @return HasMany<DealActivity, covariant Deal> */
  public function ACTIVITIES(): HasMany
  {
    return $this->hasMany(DealActivity::class, 'id_deal', 'id');
  }

  /** @return HasMany<DealDocument, covariant Deal> */
  public function DOCUMENTS(): HasMany
  {
    return $this->hasMany(DealDocument::class, 'id_deal', 'id');
  }

  /** @return hasMany<LeadDocument, covariant Lead> */
  public function LEADS(): HasMany
  {
    return $this->hasMany(DealLead::class, 'id_deal', 'id');
  }

  /** @return hasOne<Currency, covariant Lead> */
  public function TEMPLATE_QUOTATION(): HasOne
  {
    return $this->hasOne(Template::class, 'id', 'id_template_quotation');
  }

  /**
   * [Description for prepareReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * 
   * @return mixed
   * 
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    if ($hubleto->router()->urlParamAsInteger("idCustomer") > 0) {
      $query = $query->where("deals.id_customer", $hubleto->router()->urlParamAsInteger("idCustomer"));
    }

    $filters = $hubleto->router()->urlParamAsArray("filters");

    $query = Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      $filters['fDealWorkflowStep'] ?? []
    );

    if (isset($filters["fDealSourceChannel"]) && $filters["fDealSourceChannel"] > 0) {
      $query = $query->where("deals.source_channel", $filters["fDealSourceChannel"]);
    }
    if (isset($filters["fDealResult"]) && $filters["fDealResult"] > 0) {
      $query = $query->where("deals.deal_result", $filters["fDealResult"]);
    }
    if (isset($filters["fDealBusinessType"]) && $filters["fDealBusinessType"] > 0) {
      $query = $query->where("deals.business_type", $filters["fDealBusinessType"]);
    }
    if (isset($filters["fDealBusinessType"]) && $filters["fDealBusinessType"] > 0) {
      $query = $query->where("deals.business_type", $filters["fDealBusinessType"]);
    }

    if (isset($filters["fDealOwnership"])) {
      switch ($filters["fDealOwnership"]) {
        case 1: $query = $query->where("deals.id_owner", $hubleto->getService(\Hubleto\Framework\AuthProvider::class)->getUserId());
          break;
        case 2: $query = $query->where("deals.id_manager", $hubleto->getService(\Hubleto\Framework\AuthProvider::class)->getUserId());
          break;
      }
    }

    if (isset($filters["fDealClosed"])) {
      if ($filters["fDealClosed"] == 0) $query = $query->where("deals.is_closed", false);
      if ($filters["fDealClosed"] == 1) $query = $query->where("deals.is_closed", true);
    }

    return $query;
  }

}
