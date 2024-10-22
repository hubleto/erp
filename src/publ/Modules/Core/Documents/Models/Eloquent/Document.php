<?php

namespace CeremonyCrmApp\Modules\Core\Documents\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'documents';
}
