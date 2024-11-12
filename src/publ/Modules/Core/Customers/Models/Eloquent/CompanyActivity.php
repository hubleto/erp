<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyActivity extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'company_activities';

  public function ACTIVITY(): BelongsTo {
    return $this->belongsTo(Activity::class, 'id_activity', 'id');
  }
  public function COMPANY(): BelongsTo {
    return $this->belongsTo(Company::class, 'id_company', 'id');
  }
}
