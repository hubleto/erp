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
  public function FIRST_CONTACT(): HasOne {
    return $this->hasOne(Person::class, 'id_company')->where("is_primary", true);
  }
  public function BUSINESS_ACCOUNT(): HasOne {
    return $this->hasOne(BusinessAccount::class, 'id_company');
  }
  public function ACTIVITIES(): HasMany {
    return $this->hasMany(Activity::class, 'id_company');
  }
}
