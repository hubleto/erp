<?php

namespace HubletoApp\Community\Leads\Models\RecordManagers;

use HubletoApp\Community\Customers\Models\RecordManagers\Customer;
use HubletoApp\Community\Contacts\Models\RecordManagers\Person;
use HubletoApp\Community\Settings\Models\RecordManagers\Currency;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use HubletoApp\Community\Deals\Models\RecordManagers\Deal;
use HubletoApp\Community\Leads\Models\RecordManagers\LeadHistory;
use HubletoApp\Community\Leads\Models\RecordManagers\LeadTag;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends \HubletoMain\Core\RecordManager
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
  public function PRODUCTS(): HasMany {
    return $this->hasMany(LeadProduct::class, 'id_lead', 'id');
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
