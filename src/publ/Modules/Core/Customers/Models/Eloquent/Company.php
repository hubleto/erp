<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'companies';

  public function PERSONS(): HasMany {
    return $this->hasMany(Person::class, 'id_company');
  }
  public function id_account(): BelongsTo {
    return $this->belongsTo(Account::class, 'id');
  }
  public function BUSINESS_ACCOUNT(): HasOne {
    return $this->hasOne(BusinessAccount::class, 'id_company');
  }

}
