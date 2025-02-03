<?php

namespace HubletoApp\Community\Customers\Models\Eloquent;

use HubletoApp\Community\Settings\Models\Eloquent\Country;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Address extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'addresses';

  /** @return HasOne<Country, covariant Address> */
  public function COUNTRY(): HasOne {
    return $this->hasOne(Country::class, 'id', 'id_country' );
  }

  /** @return BelongsTo<Person, covariant Address> */
  public function PERSON(): BelongsTo {
    return $this->belongsTo(Person::class, 'id_person');
  }

}
