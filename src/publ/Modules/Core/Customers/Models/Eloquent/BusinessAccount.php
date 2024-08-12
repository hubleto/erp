<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BusinessAccount extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'business_accounts';

  public function id_company(): BelongsTo {
    return $this->belongsTo(Company::class, 'id');
  }

}
