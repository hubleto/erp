<?php

namespace HubletoApp\Community\Services\Models\RecordManagers;

use HubletoApp\Community\Settings\Models\RecordManagers\Currency;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends \HubletoMain\Core\RecordManager
{
  public $table = 'services';

  /** @return HasOne<Currency, covariant Service> */
  public function CURRENCY(): HasOne
  {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }
}
