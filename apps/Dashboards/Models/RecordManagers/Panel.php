<?php

namespace Hubleto\App\Community\Dashboards\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Panel extends \Hubleto\Erp\RecordManager
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

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    if ($main->getRouter()->isUrlParam("idDashboard")) {
      $query = $query->where($this->table . '.id_dashboard', $main->getRouter()->urlParamAsInteger("idDashboard"));
    }

    return $query;
  }

}
