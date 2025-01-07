<?php

namespace HubletoApp\Settings\Models\Eloquent;

use HubletoApp\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tag extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'tags';

}
