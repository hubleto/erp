<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\ActivityType;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Activity extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'activities';

  public function COMPANY() {
    return $this->belongsTo(Company::class, 'id_company', 'id');
  }
  public function USER() {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }
  public function ACTIVITY_TYPE(): HasOne {
    return $this->HasOne(ActivityType::class, 'id', 'id_activity_type');
  }
}
