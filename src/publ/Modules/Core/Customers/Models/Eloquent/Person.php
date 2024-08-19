<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'persons';

  public function id_company(): BelongsTo
  {
    return $this->belongsTo(Company::class, 'id');
  }
  public function COMPANY() {
    return $this->id_company();
  }
  public function CONTACTS(): HasMany
  {
    return $this->hasMany(Contact::class, 'id_person');
  }
  public function ADDRESSES(): HasMany
  {
    return $this->hasMany(Address::class, 'id_person');
  }

}
