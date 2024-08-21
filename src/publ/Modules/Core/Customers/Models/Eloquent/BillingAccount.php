<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BillingAccount extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'billing_accounts';

  public function id_company(): BelongsTo {
    return $this->belongsTo(Company::class, 'id');
  }

}
