<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ActivityTag extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'activities_tags';

  public function id_activity(): BelongsTo
  {
    return $this->belongsTo(Activity::class, 'id_activity', 'id');
  }
  public function id_tag(): BelongsTo
  {
    return $this->belongsTo(Tag::class, 'id_tag', 'id');
  }

  public function TAG() {
    return $this->id_tag();
  }

}
