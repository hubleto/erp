<?php

namespace HubletoApp\Community\Deals\Models\Eloquent;

use HubletoApp\Community\Customers\Models\Eloquent\Customer;
use HubletoApp\Community\Contacts\Models\Eloquent\Person;
use HubletoApp\Community\Settings\Models\Eloquent\Currency;
use HubletoApp\Community\Settings\Models\Eloquent\Pipeline;
use HubletoApp\Community\Settings\Models\Eloquent\PipelineStep;
use HubletoApp\Community\Settings\Models\Eloquent\User;
use HubletoApp\Community\Deals\Models\Eloquent\DealHistory;
use HubletoApp\Community\Deals\Models\Eloquent\DealTag;
use HubletoApp\Community\Leads\Models\Eloquent\Lead;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Deal extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'deals';

  /** @return BelongsTo<Customer, covariant Deal> */
  public function CUSTOMER(): BelongsTo {
    return $this->belongsTo(Customer::class, 'id_customer', 'id' );
  }

  /** @return HasOne<Pipeline, covariant Deal> */
  public function PIPELINE(): HasOne {
    return $this->hasOne(Pipeline::class, 'id', 'id_pipeline');
  }

  /** @return HasOne<PipelineStep, covariant Deal> */
  public function PIPELINE_STEP(): HasOne {
    return $this->hasOne(PipelineStep::class, 'id', 'id_pipeline_step');
  }

  /** @return BelongsTo<Lead, covariant Deal> */
  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead','id' );
  }

  /** @return BelongsTo<User, covariant Deal> */
  public function USER(): BelongsTo {
    return $this->belongsTo(User::class, 'id_user','id' );
  }

  /** @return HasOne<Person, covariant Deal> */
  public function PERSON(): HasOne {
    return $this->hasOne(Person::class, 'id', 'id_person');
  }

  /** @return HasOne<DealStatus, covariant Deal> */
  public function STATUS(): HasOne {
    return $this->hasOne(DealStatus::class, 'id', 'id_deal_status');
  }

  /** @return HasOne<Currency, covariant Deal> */
  public function CURRENCY(): HasOne {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }

  /** @return HasMany<DealHistory, covariant Deal> */
  public function HISTORY(): HasMany {
    return $this->hasMany(DealHistory::class, 'id_deal', 'id');
  }

  /** @return HasMany<DealTag, covariant Deal> */
  public function TAGS(): HasMany {
    return $this->hasMany(DealTag::class, 'id_deal', 'id');
  }

  /** @return HasMany<DealService, covariant Deal> */
  public function SERVICES(): HasMany {
    return $this->hasMany(DealService::class, 'id_deal', 'id');
  }

  /** @return HasMany<DealActivity, covariant Deal> */
  public function ACTIVITIES(): HasMany {
    return $this->hasMany(DealActivity::class, 'id_deal', 'id' );
  }

  /** @return HasMany<DealDocument, covariant Deal> */
  public function DOCUMENTS(): HasMany {
    return $this->hasMany(DealDocument::class, 'id_deal', 'id' );
  }

}
