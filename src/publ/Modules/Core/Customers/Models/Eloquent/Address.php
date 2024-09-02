<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\Country;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Address extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'addresses';

  public function id_person(): BelongsTo
  {
    return $this->belongsTo(Person::class, 'id_person');
  }
  public function id_country(): HasOne {
   return $this->hasOne(Country::class, 'id', 'id_country' );
 }
 public function COUNTRY(): HasOne {
   return $this->id_country();
 }

  public function PERSON() {
    return $this->id_person();
  }

}
