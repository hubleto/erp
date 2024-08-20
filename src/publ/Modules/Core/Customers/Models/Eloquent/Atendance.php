<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Atendance extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'atendance';

  public function id_activity(): BelongsTo
  {
    return $this->belongsTo(Activity::class, "id_activity", 'id');
  }
  public function ACTIVITY() {
    return $this->id_activity();
  }
  public function id_user(): BelongsTo
  {
    return $this->belongsTo(Activity::class, "id_user", 'id');
  }
  public function ATENDEES() {
    return $this->id_activity();
  }


}
