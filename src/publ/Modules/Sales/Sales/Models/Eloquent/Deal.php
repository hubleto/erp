<?php

namespace CeremonyCrmApp\Modules\Sales\Sales\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent\Company;
use CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent\Person;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\Currency;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\DealStatus;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\Pipeline;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\User;
use CeremonyCrmApp\Modules\Sales\Sales\Models\Eloquent\DealHistory;
use CeremonyCrmApp\Modules\Sales\Sales\Models\Eloquent\DealLabel;

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
  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead','id' );
  }
  public function USER(): BelongsTo {
    return $this->belongsTo(User::class, 'id_user','id' );
  }
  public function PERSON(): HasOne {
    return $this->hasOne(Person::class, 'id', 'id_person');
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
    return $this->hasMany(LeadService::class, 'id_lead', 'id');
  }
}
