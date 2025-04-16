<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends \HubletoMain\Core\RecordManager
{
  public $table = 'profiles';

  // public function REPORT(): BelongsTo
  // {
  //   return $this->belongsTo(\EMonitorApp\Models\RecordManagers\Report::class, 'id_report', 'id')->orderBy('name', 'asc');
  // }

}
