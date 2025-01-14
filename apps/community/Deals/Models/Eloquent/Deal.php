<?php

namespace HubletoApp\Community\Deals\Models\Eloquent;

use HubletoApp\Community\Customers\Models\Eloquent\Company;
use HubletoApp\Community\Customers\Models\Eloquent\Person;
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

  public function COMPANY(): BelongsTo {
    return $this->belongsTo(Company::class, 'id_company', 'id' );
  }
  public function PIPELINE(): HasOne {
    return $this->hasOne(Pipeline::class, 'id', 'id_pipeline');
  }
  public function PIPELINE_STEP(): HasOne {
    return $this->hasOne(PipelineStep::class, 'id', 'id_pipeline_step');
  }
  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead','id' );
  }
  public function USER(): BelongsTo {
    return $this->belongsTo(User::class, 'id_user','id' );
  }
  public function PERSON(): HasOne {
    return $this->hasOne(Person::class, 'id', 'id_person');
  }
  public function STATUS(): HasOne {
    return $this->hasOne(DealStatus::class, 'id', 'id_deal_status');
  }
  public function CURRENCY(): HasOne {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }
  public function HISTORY(): HasMany {
    return $this->hasMany(DealHistory::class, 'id_deal', 'id');
  }
  public function TAGS(): HasMany {
    return $this->hasMany(DealTag::class, 'id_deal', 'id');
  }
  public function SERVICES(): HasMany {
    return $this->hasMany(DealService::class, 'id_deal', 'id');
  }
  public function ACTIVITIES(): HasMany {
    return $this->hasMany(DealActivity::class, 'id_deal', 'id' );
  }
  public function DOCUMENTS(): HasMany {
    return $this->hasMany(DealDocument::class, 'id_deal', 'id' );
  }
}
