<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tag extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'tags';

}
