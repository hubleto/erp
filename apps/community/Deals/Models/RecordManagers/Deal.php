<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Customers\Models\RecordManagers\Customer;
use HubletoApp\Community\Contacts\Models\RecordManagers\Person;
use HubletoApp\Community\Settings\Models\RecordManagers\Currency;
use HubletoApp\Community\Settings\Models\RecordManagers\Pipeline;
use HubletoApp\Community\Settings\Models\RecordManagers\PipelineStep;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use HubletoApp\Community\Deals\Models\RecordManagers\DealHistory;
use HubletoApp\Community\Deals\Models\RecordManagers\DealTag;
use HubletoApp\Community\Leads\Models\RecordManagers\Lead;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Deal extends \HubletoMain\Core\RecordManager
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

  /** @return HasMany<DealProduct, covariant Deal> */
  public function PRODUCTS(): HasMany {
    return $this->hasMany(DealProduct::class, 'id_deal', 'id')
        ->whereHas("PRODUCT", function ($query) {
          $query->where('type', \HubletoApp\Community\Products\Models\Product::TYPE_PRODUCT);
      });
    ;
  }

  /** @return HasMany<DealProduct, covariant Deal> */
  public function SERVICES(): HasMany {
    return $this->hasMany(DealProduct::class, 'id_deal', 'id')
        ->whereHas("PRODUCT", function ($query) {
          $query->where('type', \HubletoApp\Community\Products\Models\Product::TYPE_SERVICE);
      });
    ;
  }

  /** @return HasMany<DealActivity, covariant Deal> */
  public function ACTIVITIES(): HasMany {
    return $this->hasMany(DealActivity::class, 'id_deal', 'id' );
  }

  /** @return HasMany<DealDocument, covariant Deal> */
  public function DOCUMENTS(): HasMany {
    return $this->hasMany(DealDocument::class, 'id_lookup', 'id' );
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \ADIOS\Core\Helper::getGlobalApp();

    if ($main->urlParamAsInteger("id") <= 0) {
      if ($main->urlParamAsBool("showArchive")) {
        $query = $query->where("deals.is_archived", 1);
      } else {
        $query = $query->where("deals.is_archived", 0);
      }
    }

    return $query;
  }

}
