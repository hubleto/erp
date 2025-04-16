<?php

namespace HubletoApp\Community\Leads\Models\Eloquent;

use HubletoApp\Community\Customers\Models\Eloquent\Customer;
use HubletoApp\Community\Contacts\Models\Eloquent\Person;
use HubletoApp\Community\Settings\Models\Eloquent\Currency;
use HubletoApp\Community\Settings\Models\Eloquent\User;
use HubletoApp\Community\Deals\Models\Eloquent\Deal;
use HubletoApp\Community\Leads\Models\Eloquent\LeadHistory;
use HubletoApp\Community\Leads\Models\Eloquent\LeadTag;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends \ADIOS\Core\ModelEloquentRecord
{
  public $table = 'leads';

  /** @return hasOne<Deal, covariant Lead> */
  public function DEAL(): HasOne {
    return $this->hasOne(Deal::class, 'id_lead', 'id' );
  }

  /** @return BelongsTo<Customer, covariant Lead> */
  public function CUSTOMER(): BelongsTo {
    return $this->belongsTo(Customer::class, 'id_customer', 'id' );
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function USER(): BelongsTo {
    return $this->belongsTo(User::class, 'id_user','id' );
  }

  /** @return hasOne<Person, covariant Lead> */
  public function PERSON(): HasOne {
    return $this->hasOne(Person::class, 'id', 'id_person');
  }

  /** @return hasOne<Currency, covariant Lead> */
  public function CURRENCY(): HasOne {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }

  /** @return hasOne<LeadStatus, covariant Lead> */
  public function STATUS(): HasOne {
    return $this->hasOne(LeadStatus::class, 'id', 'id_lead_status');
  }

  /** @return hasMany<LeadHistory, covariant Lead> */
  public function HISTORY(): HasMany {
    return $this->hasMany(LeadHistory::class, 'id_lead', 'id');
  }

  /** @return hasMany<LeadTag, covariant Lead> */
  public function TAGS(): HasMany {
    return $this->hasMany(LeadTag::class, 'id_lead', 'id');
  }

  /** @return hasMany<LeadService, covariant Lead> */
  public function SERVICES(): HasMany {
    return $this->hasMany(LeadService::class, 'id_lead', 'id');
  }

  /** @return hasMany<LeadActivity, covariant Lead> */
  public function ACTIVITIES(): HasMany {
    return $this->hasMany(LeadActivity::class, 'id_lead', 'id' );
  }

  /** @return hasMany<LeadDocument, covariant Lead> */
  public function DOCUMENTS(): HasMany {
    return $this->hasMany(LeadDocument::class, 'id_lookup', 'id' );
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \ADIOS\Core\Helper::getGlobalApp();

    if ($main->urlParamAsBool("showArchive")) {
      $query = $query->where("leads.is_archived", 1);
    } else {
      $query = $query->where("leads.is_archived", 0);
    }

    return $query;
  }

  public function addOrderByToQuery(mixed $query, array $orderBy): mixed
  {
    if ($orderBy['field'] == 'DEAL') {
      $query
        ->join('deals', 'deals.id_lead', '=', 'leads.id')
        ->orderBy('deals.identifier', $orderBy['direction'])
      ;
      return $query;
    } else {
      return parent::addOrderByToQuery($query, $orderBy);
    }
  }
}
