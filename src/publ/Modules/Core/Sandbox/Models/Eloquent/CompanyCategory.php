<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyCategory extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'companies_categories';

  // public function REPORT(): BelongsTo
  // {
  //   return $this->belongsTo(\EMonitorApp\Models\Eloquent\Report::class, 'id_report', 'id')->orderBy('name', 'asc');
  // }

}
