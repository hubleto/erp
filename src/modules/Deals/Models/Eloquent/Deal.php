<?php

namespace CeremonyCrmMod\Deals\Models\Eloquent;

use CeremonyCrmMod\Customers\Models\Eloquent\Company;
use CeremonyCrmMod\Customers\Models\Eloquent\Person;
use CeremonyCrmMod\Settings\Models\Eloquent\Currency;
use CeremonyCrmMod\Settings\Models\Eloquent\DealStatus;
use CeremonyCrmMod\Settings\Models\Eloquent\Pipeline;
use CeremonyCrmMod\Settings\Models\Eloquent\PipelineStep;
use CeremonyCrmMod\Settings\Models\Eloquent\User;
use CeremonyCrmMod\Deals\Models\Eloquent\DealHistory;
use CeremonyCrmMod\Deals\Models\Eloquent\DealLabel;
use CeremonyCrmMod\Leads\Models\Eloquent\Lead;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Deal extends \ADIOS\Core\Model\Eloquent
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
  public function LABELS(): HasMany {
    return $this->hasMany(DealLabel::class, 'id_deal', 'id');
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
