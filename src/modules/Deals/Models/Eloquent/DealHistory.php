<?php

namespace CeremonyCrmMod\Deals\Models\Eloquent;

use CeremonyCrmMod\Customers\Models\Eloquent\Company;
use CeremonyCrmMod\Customers\Models\Eloquent\Person;
use CeremonyCrmMod\Deals\Models\Eloquent\Deal;
use CeremonyCrmMod\Settings\Models\Eloquent\ActivityType;
use CeremonyCrmMod\Settings\Models\Eloquent\Currency;
use CeremonyCrmMod\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealHistory extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'deal_histories';


  public function DEAL(): BelongsTo {
    return $this->belongsTo(Deal::class, 'id_lead', 'id');
  }
}
