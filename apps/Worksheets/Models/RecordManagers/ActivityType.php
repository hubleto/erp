<?php

namespace HubletoApp\Community\Worksheets\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class ActivityType extends \Hubleto\Framework\RecordManager
{
  public $table = 'worksheet_activities_types';

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    // Uncomment this line if you are going to use $main.
    // $main = \HubletoMain\Loader::getGlobalApp();

    // Uncomment and modify these lines if you want to apply filtering based on URL parameters
    // if ($main->urlParamAsInteger("idCustomer") > 0) {
    //   $query = $query->where($this->table . '.id_customer', $main->urlParamAsInteger("idCustomer"));
    // }

    // Uncomment and modify these lines if you want to apply default filters to your model.
    // $defaultFilters = $main->urlParamAsArray("defaultFilters");
    // if (isset($defaultFilters["fArchive"]) && $defaultFilters["fArchive"] == 1) $query = $query->where("customers.is_active", false);
    // else $query = $query->where("customers.is_active", true);

    return $query;
  }

}
