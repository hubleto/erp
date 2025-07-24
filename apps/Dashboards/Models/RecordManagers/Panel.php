<?php

namespace HubletoApp\Community\Dashboards\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Panel extends \Hubleto\Framework\RecordManager
{
  public $table = 'dashboards_panels';

  /** @return BelongsTo<Customer, covariant BillingAccount> */
  public function DASHBOARD(): BelongsTo
  {
    return $this->belongsTo(Dashboard::class, 'id_dashboard', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \HubletoMain\Loader::getGlobalApp();

    if ($main->isUrlParam("idDashboard")) {
      $query = $query->where($this->table . '.id_dashboard', $main->urlParamAsInteger("idDashboard"));
    }

    return $query;
  }

}
