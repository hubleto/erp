<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Activity extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'activity_categories';

  public function id_company(): BelongsTo
  {
    return $this->belongsTo(Company::class, 'id');
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
  public function CATEGORIES(): HasMany
  {
    return $this->hasMany(ActivityCategoryActivity::class, 'id_activity', 'id');
  }


}
