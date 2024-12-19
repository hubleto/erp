<?php

namespace CeremonyCrmMod\Customers\Models\Eloquent;

use CeremonyCrmMod\Settings\Models\Eloquent\Country;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Address extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'addresses';

  public function COUNTRY(): HasOne {
    return $this->hasOne(Country::class, 'id', 'id_country' );
  }
  public function PERSON() {
    return $this->belongsTo(Person::class, 'id_person');
  }

}
