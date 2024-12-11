<?php

namespace CeremonyCrmMod\Core\Customers\Models\Eloquent;

use CeremonyCrmMod\Core\Settings\Models\Eloquent\ActivityType;
use CeremonyCrmMod\Core\Settings\Models\Eloquent\User;
use CeremonyCrmMod\Sales\Leads\Models\Eloquent\LeadActivity;
use CeremonyCrmMod\Sales\Deals\Models\Eloquent\DealActivity;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Activity extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'activities';

  public function COMPANY_ACTIVITY(): HasOne {
    return $this->hasOne(CompanyActivity::class, 'id_activity', 'id');
  }
  public function LEAD_ACTIVITY(): HasOne {
    return $this->hasOne(LeadActivity::class, 'id_activity', 'id');
  }
  public function DEAL_ACTIVITY(): HasOne {
    return $this->hasOne(DealActivity::class, 'id_activity', 'id');
  }
  public function USER(): BelongsTo {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }
  public function ACTIVITY_TYPE(): HasOne {
    return $this->HasOne(ActivityType::class, 'id', 'id_activity_type');
  }
}
