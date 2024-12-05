<?php

namespace CeremonyCrmApp\Modules\Sales\Leads\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent\Company;
use CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent\Person;
use CeremonyCrmApp\Modules\Sales\Leads\Models\Eloquent\Lead;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\ActivityType;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\Currency;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadHistory extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'lead_histories';


  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }
}
