<?php

namespace CeremonyCrmApp\Modules\Sales\Deals\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent\Company;
use CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent\Person;
use CeremonyCrmApp\Modules\Sales\Deals\Models\Eloquent\Deal;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\ActivityType;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\Currency;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\User;
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
