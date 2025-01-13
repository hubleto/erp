<?php

namespace HubletoApp\Community\Customers\Models\Eloquent;

use HubletoApp\Community\Settings\Models\Eloquent\ContactType;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'contacts';

  public function PERSON() {
    return $this->belongsTo(Person::class, 'id_person');
  }
  public function CONTACT_TYPE() {
    return $this->belongsTo(ContactType::class, 'id_contact_type', 'id');
  }

}
