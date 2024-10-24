<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'contacts';

  public function PERSON() {
    return $this->belongsTo(Person::class, 'id_person');
  }

}
