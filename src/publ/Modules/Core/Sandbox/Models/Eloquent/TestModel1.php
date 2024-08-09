<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestModel1 extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'test_model_1';

  // public function REPORT(): BelongsTo
  // {
  //   return $this->belongsTo(\EMonitorApp\Models\Eloquent\Report::class, 'id_report', 'id')->orderBy('name', 'asc');
  // }

}
