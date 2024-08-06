<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Person extends \ADIOS\Core\Model\Eloquent {
  public $table = 'persons';

  public function id_company(): BelongsTo
  {
    return $this->belongsTo(Company::class, 'id_company', 'id')->orderBy('name', 'asc');
  }

}
