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

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    if ($hubleto->router()->isUrlParam("idDashboard")) {
      $query = $query->where($this->table . '.id_dashboard', $hubleto->router()->urlParamAsInteger("idDashboard"));
    }

    $query->orderBy('position', 'asc');

    return $query;
  }

}
