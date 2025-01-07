<?php

namespace HubletoApp\Settings\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Country extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'countries';
}
