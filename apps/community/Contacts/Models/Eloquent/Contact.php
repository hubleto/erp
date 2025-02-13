<?php

namespace HubletoApp\Community\Contacts\Models\Eloquent;

use HubletoApp\Community\Settings\Models\Eloquent\ContactType;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'contacts';

  /** @return BelongsTo<Person, covariant Contact> */
  public function PERSON(): BelongsTo {
    return $this->belongsTo(Person::class, 'id_person');
  }

  /** @return BelongsTo<ContactType, covariant Contact> */
  public function CONTACT_TYPE(): BelongsTo {
    return $this->belongsTo(ContactType::class, 'id_contact_category', 'id');
  }

}
