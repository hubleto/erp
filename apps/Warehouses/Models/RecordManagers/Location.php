<?php

namespace Hubleto\App\Community\Warehouses\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;

class Location extends \Hubleto\Erp\RecordManager
{
  public $table = 'warehouses_locations';

  public function OPERATION_MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_operation_manager', 'id');
  }

  public function TYPE(): BelongsTo
  {
    return $this->belongsTo(LocationType::class, 'id_type', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    if ($main->getRouter()->urlParamAsInteger("idWarehouse") > 0) {
      $query = $query->where($this->table . '.id_warehouse', $main->getRouter()->urlParamAsInteger("idWarehouse"));
    }

    // Uncomment and modify these lines if you want to apply default filters to your model.
    // $filters = $main->getRouter()->urlParamAsArray("filters");
    // if (isset($filters["fArchive"]) && $filters["fArchive"] == 1) $query = $query->where("customers.is_active", false);
    // else $query = $query->where("customers.is_active", true);

    return $query;
  }

}
