<?php

namespace CeremonyCrmMod\Sales\Deals\Models\Eloquent;

use CeremonyCrmMod\Core\Customers\Models\Eloquent\Company;
use CeremonyCrmMod\Core\Customers\Models\Eloquent\Person;
use CeremonyCrmMod\Sales\Deals\Models\Eloquent\Deal;
use CeremonyCrmMod\Core\Settings\Models\Eloquent\ActivityType;
use CeremonyCrmMod\Core\Settings\Models\Eloquent\Currency;
use CeremonyCrmMod\Core\Settings\Models\Eloquent\User;
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
