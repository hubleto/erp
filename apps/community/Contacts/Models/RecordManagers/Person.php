<?php

namespace HubletoApp\Community\Contacts\Models\RecordManagers;

use HubletoApp\Community\Customers\Models\RecordManagers\Customer;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends \HubletoMain\Core\RecordManager
{
  public $table = 'persons';

  /** @return BelongsTo<Customer, covariant Person> */
  public function CUSTOMER(): BelongsTo {
    return $this->belongsTo(Customer::class, 'id_customer');
  }

  /** @return HasMany<Contact, covariant Person> */
  public function CONTACTS(): HasMany {
     return $this->hasMany(Contact::class, 'id_person', 'id');
  }

  /** @return HasMany<PersonTag, covariant Person> */
  public function TAGS(): HasMany {
     return $this->hasMany(PersonTag::class, 'id_person', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $query = $query->selectRaw("
      (Select value from contacts where id_person = persons.id and type = 'number' LIMIT 1) virt_number,
      (Select value from contacts where id_person = persons.id and type = 'email' LIMIT 1) virt_email
    ");

    return $query;
  }
}
