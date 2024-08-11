<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonAddress extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'person_addresses';

  public function id_person(): BelongsTo
  {
    return $this->belongsTo(Person::class, 'id_person', 'id');
  }

}
