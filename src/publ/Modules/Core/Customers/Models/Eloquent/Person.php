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
  public function id_account(): BelongsTo
  {
    return $this->belongsTo(Account::class, "id_account", "id");
  }
  public function COMPANY() {
    return $this->id_company();
  }
  public function CONTACTS(): HasMany
  {
    return $this->hasMany(PersonContact::class, 'id_person');
  }
  public function ADDRESSES(): HasMany
  {
    return $this->hasMany(PersonAddress::class, 'id_person');
  }
  public function ACCOUNT(): BelongsTo
  {
    return $this->id_account();
  }

}
