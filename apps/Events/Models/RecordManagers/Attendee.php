<?php

namespace Hubleto\App\Community\Events\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;

class Attendee extends \Hubleto\Erp\RecordManager
{
  public $table = 'events_attendees';

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    // Uncomment this line if you are going to use $main.
    // $main = \Hubleto\Erp\Loader::getGlobalApp();

    // Uncomment and modify these lines if you want to apply filtering based on URL parameters
    // if ($main->getRouter()->urlParamAsInteger("idCustomer") > 0) {
    //   $query = $query->where($this->table . '.id_customer', $main->getRouter()->urlParamAsInteger("idCustomer"));
    // }

    // Uncomment and modify these lines if you want to apply default filters to your model.
    // $filters = $main->getRouter()->urlParamAsArray("filters");
    // if (isset($filters["fArchive"]) && $filters["fArchive"] == 1) $query = $query->where("customers.is_active", false);
    // else $query = $query->where("customers.is_active", true);

    return $query;
  }

}
