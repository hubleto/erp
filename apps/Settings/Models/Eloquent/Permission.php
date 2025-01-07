<?php

namespace CeremonyCrmMod\Settings\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'permissions';
}
