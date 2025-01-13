<?php

namespace HubletoApp\Community\Settings\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'profiles';

  // public function REPORT(): BelongsTo
  // {
  //   return $this->belongsTo(\EMonitorApp\Models\Eloquent\Report::class, 'id_report', 'id')->orderBy('name', 'asc');
  // }

}
