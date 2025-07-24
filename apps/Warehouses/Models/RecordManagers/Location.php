<?php

namespace HubletoApp\Community\Warehouses\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Location extends \Hubleto\Framework\RecordManager
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

    $main = \HubletoMain\Loader::getGlobalApp();

    if ($main->urlParamAsInteger("idWarehouse") > 0) {
      $query = $query->where($this->table . '.id_warehouse', $main->urlParamAsInteger("idWarehouse"));
    }

    // Uncomment and modify these lines if you want to apply default filters to your model.
    // $defaultFilters = $main->urlParamAsArray("defaultFilters");
    // if (isset($defaultFilters["fArchive"]) && $defaultFilters["fArchive"] == 1) $query = $query->where("customers.is_active", false);
    // else $query = $query->where("customers.is_active", true);

    return $query;
  }

}
