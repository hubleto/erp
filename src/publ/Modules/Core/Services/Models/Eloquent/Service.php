<?php

namespace CeremonyCrmApp\Modules\Core\Services\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'services';
}
