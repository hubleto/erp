<?php

namespace HubletoApp\Community\Customers\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'persons';

  /** @return BelongsTo<Company, covariant Person> */
  public function COMPANY(): BelongsTo {
    return $this->belongsTo(Company::class, 'id_company');
  }

  /** @return HasMany<Contact, covariant Person> */
  public function CONTACTS(): HasMany {
     return $this->hasMany(Contact::class, 'id_person', 'id');
  }

  /** @return HasMany<Address, covariant Person> */
  public function ADDRESSES(): HasMany {
     return $this->hasMany(Address::class, 'id_person', 'id');
  }

  /** @return HasMany<PersonTag, covariant Person> */
  public function TAGS(): HasMany {
     return $this->hasMany(PersonTag::class, 'id_person', 'id');
  }
}
