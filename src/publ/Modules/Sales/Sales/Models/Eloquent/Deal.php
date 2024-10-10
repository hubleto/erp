<?php

namespace CeremonyCrmApp\Modules\Sales\Sales\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent\Company;
use CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent\Person;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\Currency;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\DealStatus;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\User;
use CeremonyCrmApp\Modules\Sales\Sales\Models\Eloquent\DealHistory;
use CeremonyCrmApp\Modules\Sales\Sales\Models\Eloquent\DealLabel;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Deal extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'deals';

  public function COMPANY(): HasOne
  {
    return $this->hasOne(Company::class, 'id', 'id_company');
  }
  public function LEAD(): BelongsTo
  {
    return $this->belongsTo(Lead::class, 'id_lead','id' );
  }
  public function USER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user','id' );
  }
  public function PERSON(): HasOne
  {
    return $this->hasOne(Person::class, 'id', 'id_person');
  }
  public function CURRENCY(): HasOne
  {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }
  public function STATUS(): HasOne
  {
    return $this->hasOne(DealStatus::class, 'id', 'id_status');
  }
  public function DEAL_HISTORY(): HasMany
  {
    return $this->hasMany(DealHistory::class, 'id_deal', 'id');
  }
  public function LABELS(): HasMany
  {
    return $this->hasMany(DealLabel::class, 'id_deal', 'id');
  }
}
