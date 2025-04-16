<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHasRole extends \HubletoMain\Core\RecordManager
{
  public $table = 'user_has_roles';

  // public function REPORT(): BelongsTo
  // {
  //   return $this->belongsTo(\EMonitorApp\Models\RecordManagers\Report::class, 'id_report', 'id')->orderBy('name', 'asc');
  // }

}
