<?php

namespace HubletoApp\Community\Services\Models\Eloquent;

use HubletoApp\Community\Settings\Models\Eloquent\Currency;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'services';

  /** @return HasOne<Currency, covariant Service> */
  public function CURRENCY(): HasOne
  {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }
}
