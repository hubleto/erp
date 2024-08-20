<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'sbx_categories';
}
