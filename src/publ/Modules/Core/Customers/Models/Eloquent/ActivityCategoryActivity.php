<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ActivityCategoryActivity extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'activity_categories_activities';

  public function id_activity(): BelongsTo
  {
    return $this->belongsTo(Activity::class, "id_activity", 'id');
  }
  public function id_activity_category(): BelongsTo
  {
    return $this->belongsTo(ActivityCategory::class, "id_activity_category", "id");
  }

  public function CATEGORY() {
    return $this->id_activity_category();
  }

}
