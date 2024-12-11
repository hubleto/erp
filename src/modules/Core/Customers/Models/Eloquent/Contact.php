<?php

namespace CeremonyCrmMod\Core\Customers\Models\Eloquent;

use CeremonyCrmMod\Core\Settings\Models\Eloquent\ContactType;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'contacts';

  public function PERSON() {
    return $this->belongsTo(Person::class, 'id_person');
  }
  public function CONTACT_TYPE() {
    return $this->belongsTo(ContactType::class, 'id_contact_type', 'id');
  }

}
