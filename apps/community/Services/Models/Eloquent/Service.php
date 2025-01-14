<?php

namespace HubletoApp\Community\Services\Models\Eloquent;

use HubletoApp\Community\Settings\Models\Eloquent\Currency;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'services';

  public function CURRENCY(): HasOne {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }
}
