<?php

namespace HubletoApp\Community\Settings\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Currency extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'currencies';
}
