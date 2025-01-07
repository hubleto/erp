<?php

namespace HubletoApp\Services\Models\Eloquent;

use HubletoApp\Settings\Models\Eloquent\Currency;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'services';

  public function CURRENCY(): HasOne {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }
}
