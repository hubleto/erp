<?php

namespace HubletoApp\Community\Customers\Models\Eloquent;

use \HubletoApp\Community\Billing\Models\Eloquent\BillingAccount;
use \HubletoApp\Community\Customers\Models\Eloquent\CustomerDocument;
use \HubletoApp\Community\Settings\Models\Eloquent\Country;
use \HubletoApp\Community\Settings\Models\Eloquent\User;
use \HubletoApp\Community\Deals\Models\Eloquent\Deal;
use \HubletoApp\Community\Leads\Models\Eloquent\Lead;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\HasOne;
use \Illuminate\Database\Eloquent\Builder;

class Customer extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'customers';

  /** @return HasMany<Person, covariant Customer> */
  public function PERSONS(): HasMany {
    return $this->hasMany(Person::class, 'id_customer');
  }

  /** @return HasOne<Country, covariant Customer> */
  public function COUNTRY(): HasOne {
    return $this->hasOne(Country::class, 'id', 'id_country' );
  }

  public function FIRST_CONTACT(): HasOne { //@phpstan-ignore-line
    return $this->hasOne(Person::class, 'id_customer')->where('is_main', true); //@phpstan-ignore-line
  }

  /** @return HasMany<CustomerActivity, covariant Customer> */
  public function ACTIVITIES(): HasMany {
    return $this->hasMany(CustomerActivity::class, 'id_customer', 'id' );
  }

  /** @return HasMany<CustomerDocument, covariant Customer> */
  public function DOCUMENTS(): HasMany {
    return $this->hasMany(CustomerDocument::class, 'id_customer', 'id' );
  }

  /** @return HasMany<CustomerTag, covariant Customer> */
  public function TAGS(): HasMany {
    return $this->hasMany(CustomerTag::class, 'id_customer', 'id');
  }

  /** @return HasMany<Lead, covariant Customer> */
  public function LEADS(): HasMany {
    return $this->hasMany(Lead::class, 'id_customer', 'id');
  }

  /** @return HasMany<Deal, covariant Customer> */
  public function DEALS(): HasMany {
    return $this->hasMany(Deal::class, 'id_customer', 'id');
  }

  /** @return BelongsTo<User, covariant Customer> */
  public function USER(): BelongsTo {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }

}
