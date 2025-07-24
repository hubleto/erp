<?php

namespace HubletoApp\Community\Orders\Models\RecordManagers;

use HubletoApp\Community\Customers\Models\RecordManagers\Customer;
use HubletoApp\Community\Settings\Models\RecordManagers\Currency;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends \Hubleto\Framework\RecordManager
{
  public $table = 'orders';

  /** @return HasMany<OrderProduct, covariant Order> */
  public function PRODUCTS(): HasMany
  {
    return $this->hasMany(OrderProduct::class, 'id_order', 'id');
  }

  /** @return HasMany<History, covariant Order> */
  public function HISTORY(): HasMany
  {
    return $this->hasMany(History::class, 'id_order', 'id');
  }

  /** @return HasOne<Customer, covariant Order> */
  public function CUSTOMER(): HasOne
  {
    return $this->hasOne(Customer::class, 'id', 'id_customer');
  }

  /** @return hasOne<Currency, covariant Lead> */
  public function CURRENCY(): HasOne
  {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }
}
