<?php

namespace CeremonyCrmMod\Settings\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ActivityType extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'activity_types';
}
