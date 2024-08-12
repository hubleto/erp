<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'accounts';

  public function COMPANIES(): HasMany {
    return $this->hasMany(Company::class, 'id_account');
  }

}
