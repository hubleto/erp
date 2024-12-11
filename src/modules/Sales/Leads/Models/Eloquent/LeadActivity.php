<?php

namespace CeremonyCrmMod\Sales\Leads\Models\Eloquent;

use CeremonyCrmMod\Core\Customers\Models\Eloquent\Activity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadActivity extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'lead_activities';

  public function ACTIVITY(): BelongsTo {
    return $this->belongsTo(Activity::class, 'id_activity', 'id');
  }
  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }
}
