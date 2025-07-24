<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Customers\Models\RecordManagers\Customer;
use HubletoApp\Community\Contacts\Models\RecordManagers\Contact;
use HubletoApp\Community\Settings\Models\RecordManagers\Currency;
use HubletoApp\Community\Pipeline\Models\RecordManagers\Pipeline;
use HubletoApp\Community\Pipeline\Models\RecordManagers\PipelineStep;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use HubletoApp\Community\Deals\Models\RecordManagers\DealHistory;
use HubletoApp\Community\Deals\Models\RecordManagers\DealTag;
use HubletoApp\Community\Leads\Models\RecordManagers\Lead;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Deal extends \Hubleto\Framework\RecordManager
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
    return $this->hasMany(DealProduct::class, 'id_deal', 'id')
        ->whereHas("PRODUCT", function ($query) {
          $query->where('type', \HubletoApp\Community\Products\Models\Product::TYPE_PRODUCT);
        });
    ;
  }

  /** @return HasMany<DealProduct, covariant Deal> */
  public function SERVICES(): HasMany
  {
    return $this->hasMany(DealProduct::class, 'id_deal', 'id')
        ->whereHas("PRODUCT", function ($query) {
          $query->where('type', \HubletoApp\Community\Products\Models\Product::TYPE_SERVICE);
        });
    ;
  }

  /** @return HasMany<DealActivity, covariant Deal> */
  public function ACTIVITIES(): HasMany
  {
    return $this->hasMany(DealActivity::class, 'id_deal', 'id');
  }

  /** @return HasMany<DealDocument, covariant Deal> */
  public function DOCUMENTS(): HasMany
  {
    return $this->hasMany(DealDocument::class, 'id_lookup', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \HubletoMain\Loader::getGlobalApp();

    if ($main->urlParamAsInteger("idCustomer") > 0) {
      $query = $query->where("deals.id_customer", $main->urlParamAsInteger("idCustomer"));
    }

    $defaultFilters = $main->urlParamAsArray("defaultFilters");
    if (isset($defaultFilters["fDealSourceChannel"]) && $defaultFilters["fDealSourceChannel"] > 0) {
      $query = $query->where("deals.source_channel", $defaultFilters["fDealSourceChannel"]);
    }
    if (isset($defaultFilters["fDealResult"]) && $defaultFilters["fDealResult"] > 0) {
      $query = $query->where("deals.deal_result", $defaultFilters["fDealResult"]);
    }
    if (isset($defaultFilters["fDealBusinessType"]) && $defaultFilters["fDealBusinessType"] > 0) {
      $query = $query->where("deals.business_type", $defaultFilters["fDealBusinessType"]);
    }
    if (isset($defaultFilters["fDealBusinessType"]) && $defaultFilters["fDealBusinessType"] > 0) {
      $query = $query->where("deals.business_type", $defaultFilters["fDealBusinessType"]);
    }

    if (isset($defaultFilters["fDealOwnership"])) {
      switch ($defaultFilters["fDealOwnership"]) {
        case 1: $query = $query->where("deals.id_owner", $main->auth->getUserId());
          break;
        case 2: $query = $query->where("deals.id_manager", $main->auth->getUserId());
          break;
      }
    }

    if (isset($defaultFilters["fDealClosed"])) {
      if ($defaultFilters["fDealClosed"] == 1) {
        $query = $query->where("deals.is_closed", true);
      } else {
        $query = $query->where("deals.is_closed", false);
      }
    }

    if (isset($defaultFilters["fDealArchive"])) {
      if ($defaultFilters["fDealArchive"] == 1) {
        $query = $query->where("deals.is_archived", 1);
      } else {
        $query = $query->where("deals.is_archived", 0);
      }
    }

    return $query;
  }

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
