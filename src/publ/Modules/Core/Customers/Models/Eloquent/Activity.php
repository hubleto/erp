<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Activity extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'activities';

  public function id_company(): BelongsTo
  {
    return $this->belongsTo(Company::class, "id_company", 'id');
  }
  public function id_user(): BelongsTo
  {
    return $this->belongsTo(User::class, "id_user", "id");
  }

  public function COMPANY() {
    return $this->id_company();
  }
  public function USER(): BelongsTo
  {
    return $this->id_user();
  }
  public function TAGS(): HasMany
  {
    return $this->hasMany(ActivityTag::class, 'id_activity', 'id');
  }
  public function ATENDANCE(): HasMany
  {
    return $this->hasMany(Atendance::class, 'id_activity', 'id');
  }


}
