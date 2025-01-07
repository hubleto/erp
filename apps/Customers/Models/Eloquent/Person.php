<?php

namespace CeremonyCrmMod\Customers\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'persons';

  public function COMPANY(): BelongsTo {
    return $this->belongsTo(Company::class, 'id_company');
  }
  public function CONTACTS(): HasMany {
     return $this->hasMany(Contact::class, 'id_person', 'id');
  }
  public function ADDRESSES(): HasMany {
     return $this->hasMany(Address::class, 'id_person', 'id');
  }
  public function TAGS(): HasMany {
     return $this->hasMany(PersonTag::class, 'id_person', 'id');
  }
}
