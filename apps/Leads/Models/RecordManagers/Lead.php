<?php

namespace Hubleto\App\Community\Leads\Models\RecordManagers;


use Hubleto\App\Community\Auth\Models\RecordManagers\User;

use Hubleto\App\Community\Campaigns\Models\RecordManagers\Campaign;
use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;
use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Deals\Models\RecordManagers\Deal;
use Hubleto\App\Community\Settings\Models\RecordManagers\Currency;
use Hubleto\App\Community\Settings\Models\RecordManagers\Team;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends \Hubleto\Erp\RecordManager
{
  public $table = 'leads';

  /** @return belongsTo<Campaign, covariant Lead> */
  public function CAMPAIGN(): BelongsTo
  {
    return $this->belongsTo(Campaign::class, 'id_campaign', 'id');
  }

  /** @return hasOne<Deal, covariant Lead> */
  public function DEAL(): HasOne
  {
    return $this->hasOne(Deal::class, 'id_lead', 'id');
  }

  /** @return BelongsTo<Customer, covariant Lead> */
  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'id_customer', 'id');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function TEAM(): BelongsTo
  {
    return $this->belongsTo(Team::class, 'id_team', 'id');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function LEVEL(): BelongsTo
  {
    return $this->belongsTo(Level::class, 'id_level', 'id');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

  /** @return hasOne<Contact, covariant Lead> */
  public function CONTACT(): HasOne
  {
    return $this->hasOne(Contact::class, 'id', 'id_contact');
  }

  /** @return hasOne<Currency, covariant Lead> */
  public function CURRENCY(): HasOne
  {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }

  /** @return hasMany<LeadHistory, covariant Lead> */
  public function HISTORY(): HasMany
  {
    return $this->hasMany(LeadHistory::class, 'id_lead', 'id');
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

  /** @return hasMany<LeadTag, covariant Lead> */
  public function TAGS(): HasMany
  {
    return $this->hasMany(LeadTag::class, 'id_lead', 'id');
  }

  /** @return hasMany<LeadActivity, covariant Lead> */
  public function ACTIVITIES(): HasMany
  {
    return $this->hasMany(LeadActivity::class, 'id_lead', 'id');
  }

  /** @return hasMany<LeadDocument, covariant Lead> */
  public function DOCUMENTS(): HasMany
  {
    return $this->hasMany(LeadDocument::class, 'id_lead', 'id');
  }

  /** @return hasMany<LeadDocument, covariant Lead> */
  public function CAMPAIGNS(): HasMany
  {
    return $this->hasMany(LeadCampaign::class, 'id_lead', 'id');
  }

  /** @return HasMany<DealTask, covariant Deal> */
  public function TASKS(): HasMany
  {
    return $this->hasMany(LeadTask::class, 'id_lead', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    if ($hubleto->router()->urlParamAsInteger("idCustomer") > 0) {
      $query = $query->where("leads.id_customer", $hubleto->router()->urlParamAsInteger("idCustomer"));
    }

    if ($hubleto->router()->urlParamAsInteger("idCampaign") > 0) {
      $query = $query->where("leads.id_campaign", $hubleto->router()->urlParamAsInteger("idCampaign"));
    }

    $filters = $hubleto->router()->urlParamAsArray("filters");

    $query = Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      $filters['fLeadWorkflowStep'] ?? []
    );

    if (isset($filters["fLeadArchive"]) && $filters["fLeadArchive"] > 0) {
      $query = $query->where("leads.is_archived", $filters["fLeadArchive"]);
    }

    if (isset($filters["fLeadOwnership"])) {
      switch ($filters["fLeadOwnership"]) {
        case 1: $query = $query->where("leads.id_owner", $hubleto->getService(\Hubleto\Framework\AuthProvider::class)->getUserId());
          break;
        case 2: $query = $query->where("leads.id_manager", $hubleto->getService(\Hubleto\Framework\AuthProvider::class)->getUserId());
          break;
      }
    }


    // $query = $query->selectRaw("
    //   (Select value from contact_values cv where cv.id_contact = leads.id_contact and cv.type = 'email' LIMIT 1) virt_email
    // ");

    return $query;
  }

  public function addOrderByToQuery(mixed $query, array $orderBy): mixed
  {
    if (isset($orderBy['field']) && $orderBy['field'] == 'DEAL') {
      if (empty($this->joinManager["DEAL"])) {
        $this->joinManager["DEAL"]["order"] = true;
        $query->leftJoin('deals', 'deals.id_lead', '=', 'leads.id');
      }
      $query->orderBy('deals.identifier', $orderBy['direction']);

      return $query;
    } elseif (isset($orderBy['field']) && $orderBy['field'] == 'tags') {
      if (empty($this->joinManager["tags"])) {
        $this->joinManager["tags"]["order"] = true;
        $query
          ->addSelect("lead_tags.name")
          ->leftJoin('cross_lead_tags', 'cross_lead_tags.id_lead', '=', 'leads.id')
          ->leftJoin('lead_tags', 'cross_lead_tags.id_tag', '=', 'lead_tags.id')
        ;
      }
      $query->orderBy('lead_tags.name', $orderBy['direction']);

      return $query;
    } else {
      return parent::addOrderByToQuery($query, $orderBy);
    }
  }

  public function addFulltextSearchToQuery(mixed $query, string $fulltextSearch): mixed
  {
    if (!empty($fulltextSearch)) {
      $query = parent::addFulltextSearchToQuery($query, $fulltextSearch);

      if (empty($this->joinManager["DEAL"])) {
        $this->joinManager["DEAL"]["fullText"] = true;
        $query
          ->addSelect("deals.identifier as idDeal")
          ->leftJoin('deals', 'deals.id_lead', '=', 'leads.id')
        ;
      }
      $query->orHaving('idDeal', 'like', "%{$fulltextSearch}%");

      if (empty($this->joinManager["tags"])) {
        $this->joinManager["tags"]["fullText"] = true;
        $query
          ->addSelect("lead_tags.name")
          ->leftJoin('cross_lead_tags', 'cross_lead_tags.id_lead', '=', 'leads.id')
          ->leftJoin('lead_tags', 'cross_lead_tags.id_tag', '=', 'lead_tags.id')
        ;
      }
      $query->orHaving('lead_tags.name', 'like', "%{$fulltextSearch}%");
    }
    return $query;
  }

  public function addColumnSearchToQuery(mixed $query, array $columnSearch): mixed
  {
    $query = parent::addColumnSearchToQuery($query, $columnSearch);
    if (!empty($columnSearch) && !empty($columnSearch['DEAL'])) {
      if (empty($this->joinManager["DEAL"])) {
        $this->joinManager["DEAL"]["column"] = true;
        $query
          ->addSelect("deals.identifier as idDeal")
          ->leftJoin('deals', 'deals.id_lead', '=', 'leads.id')
        ;
      }
      $query->having('idDeal', 'like', "%{$columnSearch['DEAL']}%");
    }

    if (!empty($columnSearch) && !empty($columnSearch['tags'])) {
      if (empty($this->joinManager["tags"])) {
        $this->joinManager["tags"]["column"] = true;
        $query
          ->addSelect("lead_tags.name")
          ->leftJoin('cross_lead_tags', 'cross_lead_tags.id_lead', '=', 'leads.id')
          ->leftJoin('lead_tags', 'cross_lead_tags.id_tag', '=', 'lead_tags.id')
        ;
      }
      $query->having('lead_tags.name', 'like', "%{$columnSearch['tags']}%");
    }
    return $query;
  }
}
