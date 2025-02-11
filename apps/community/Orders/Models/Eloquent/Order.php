<?php

namespace HubletoApp\Community\Orders\Models\Eloquent;

use HubletoApp\Community\Customers\Models\Eloquent\Company;
use HubletoApp\Community\Settings\Models\Eloquent\Currency;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'orders';

  /** @return HasMany<OrderProduct, covariant Order> */
  public function PRODUCTS(): HasMany {
    return $this->hasMany(OrderProduct::class, 'id_order', 'id');
  }

  /** @return HasMany<History, covariant Order> */
  public function HISTORY(): HasMany {
    return $this->hasMany(History::class, 'id_order', 'id');
  }

  /** @return HasOne<Company, covariant Order> */
  public function CUSTOMER(): HasOne {
    return $this->hasOne(Company::class, 'id','id_company');
  }

  /** @return hasOne<Currency, covariant Lead> */
  public function CURRENCY(): HasOne {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }
}