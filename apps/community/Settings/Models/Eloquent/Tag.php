<?php

namespace HubletoApp\Community\Settings\Models\Eloquent;

use HubletoApp\Community\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tag extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'tags';

}
