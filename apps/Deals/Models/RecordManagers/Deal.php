<?php

namespace Hubleto\App\Community\Deals\Models\RecordManagers;

use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;
use Hubleto\App\Community\Settings\Models\RecordManagers\Currency;
use Hubleto\App\Community\Pipeline\Models\RecordManagers\Pipeline;
use Hubleto\App\Community\Pipeline\Models\RecordManagers\PipelineStep;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;
use Hubleto\App\Community\Deals\Models\RecordManagers\DealHistory;
use Hubleto\App\Community\Deals\Models\RecordManagers\DealTag;
use Hubleto\App\Community\Leads\Models\RecordManagers\Lead;
use Hubleto\App\Community\Documents\Models\RecordManagers\Template;
use Hubleto\App\Community\Orders\Models\RecordManagers\OrderDeal;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Deal extends \Hubleto\Erp\RecordManager
{
  public $table = 'deals';

  /** @return BelongsTo<Customer, covariant Deal> */
  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'id_customer', 'id');
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

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    if ($main->router()->urlParamAsInteger("idCustomer") > 0) {
      $query = $query->where("deals.id_customer", $main->router()->urlParamAsInteger("idCustomer"));
    }

    $filters = $main->router()->urlParamAsArray("filters");

    $query = Pipeline::applyPipelineStepFilter(
      $this->model,
      $query,
      $filters['fDealPipelineStep'] ?? []
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
        case 1: $query = $query->where("deals.id_owner", $main->authProvider()->getUserId());
          break;
        case 2: $query = $query->where("deals.id_manager", $main->authProvider()->getUserId());
          break;
      }
    }

    if (isset($filters["fDealClosed"])) {
      if ($filters["fDealClosed"] == 0) $query = $query->where("deals.is_closed", false);
      if ($filters["fDealClosed"] == 1) $query = $query->where("deals.is_closed", true);
    }

    return $query;
  }

  /**
   * [Description for addOrderByToQuery]
   *
   * @param mixed $query
   * @param array $orderBy
   * 
   * @return mixed
   * 
   */
  public function addOrderByToQuery(mixed $query, array $orderBy): mixed
  {
    if (isset($orderBy['field']) && $orderBy['field'] == 'tags') {
      if (empty($this->joinManager["tags"])) {
        $this->joinManager["tags"]["order"] = true;
        $query
          ->addSelect("deal_tags.name")
          ->leftJoin('cross_deal_tags', 'cross_deal_tags.id_deal', '=', 'deals.id')
          ->leftJoin('deal_tags', 'cross_deal_tags.id_tag', '=', 'deal_tags.id')
        ;
      }
      $query->orderBy('deal_tags.name', $orderBy['direction']);

      return $query;
    } else {
      return parent::addOrderByToQuery($query, $orderBy);
    }
  }

  /**
   * [Description for addFulltextSearchToQuery]
   *
   * @param mixed $query
   * @param string $fulltextSearch
   * 
   * @return mixed
   * 
   */
  public function addFulltextSearchToQuery(mixed $query, string $fulltextSearch): mixed
  {
    if (!empty($fulltextSearch)) {
      $query = parent::addFulltextSearchToQuery($query, $fulltextSearch);

      if (empty($this->joinManager["tags"])) {
        $this->joinManager["tags"]["fullText"] = true;
        $query
          ->addSelect("deal_tags.name")
          ->leftJoin('cross_deal_tags', 'cross_deal_tags.id_deal', '=', 'deals.id')
          ->leftJoin('deal_tags', 'cross_deal_tags.id_tag', '=', 'deal_tags.id')
        ;
      }
      $query->orHaving('deal_tags.name', 'like', "%{$fulltextSearch}%");

    }
    return $query;
  }

  /**
   * [Description for addColumnSearchToQuery]
   *
   * @param mixed $query
   * @param array $columnSearch
   * 
   * @return mixed
   * 
   */
  public function addColumnSearchToQuery(mixed $query, array $columnSearch): mixed
  {
    $query = parent::addColumnSearchToQuery($query, $columnSearch);

    if (!empty($columnSearch) && !empty($columnSearch['tags'])) {
      if (empty($this->joinManager["tags"])) {
        $this->joinManager["tags"]["column"] = true;
        $query
          ->addSelect("deal_tags.name")
          ->leftJoin('cross_deal_tags', 'cross_deal_tags.id_deal', '=', 'deals.id')
          ->leftJoin('deal_tags', 'cross_deal_tags.id_tag', '=', 'deal_tags.id')
        ;
      }
      $query->having('deal_tags.name', 'like', "%{$columnSearch['tags']}%");
    }
    return $query;
  }

}
