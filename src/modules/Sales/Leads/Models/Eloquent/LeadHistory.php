<?php

namespace CeremonyCrmMod\Sales\Leads\Models\Eloquent;

use CeremonyCrmMod\Core\Customers\Models\Eloquent\Company;
use CeremonyCrmMod\Core\Customers\Models\Eloquent\Person;
use CeremonyCrmMod\Sales\Leads\Models\Eloquent\Lead;
use CeremonyCrmMod\Core\Settings\Models\Eloquent\ActivityType;
use CeremonyCrmMod\Core\Settings\Models\Eloquent\Currency;
use CeremonyCrmMod\Core\Settings\Models\Eloquent\User;
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
