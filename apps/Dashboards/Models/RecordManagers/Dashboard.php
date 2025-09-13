<?php

namespace Hubleto\App\Community\Dashboards\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;

class Dashboard extends \Hubleto\Erp\RecordManager
{
  public $table = 'dashboards';

  /** @return HasOne<Customer, covariant BillingAccount> */
  public function OWNER(): HasOne
  {
    return $this->hasOne(User::class, 'id', 'id_owner');
  }

  /** @return HasMany<Panel, covariant Panel> */
  public function PANELS(): HasMany
  {
    return $this->hasMany(Panel::class, 'id_dashboard', 'id')->orderBy('order', 'asc');
  }

}
