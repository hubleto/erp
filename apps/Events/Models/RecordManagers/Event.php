<?php

namespace Hubleto\App\Community\Events\Models\RecordManagers;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class Event extends \Hubleto\Erp\RecordManager
{
  public $table = 'events';

  public function TYPE(): BelongsTo
  {
    return $this->belongsTo(Type::class, 'id_type', 'id');
  }

  public function ORGANIZER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_organizer', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    // Uncomment this line if you are going to use $hubleto.
    // $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    // Uncomment and modify these lines if you want to apply filtering based on URL parameters
    // if ($hubleto->router()->urlParamAsInteger("idCustomer") > 0) {
    //   $query = $query->where($this->table . '.id_customer', $hubleto->router()->urlParamAsInteger("idCustomer"));
    // }

    // Uncomment and modify these lines if you want to apply default filters to your model.
    // $filters = $hubleto->router()->urlParamAsArray("filters");
    // if (isset($filters["fArchive"]) && $filters["fArchive"] == 1) $query = $query->where("customers.is_active", false);
    // else $query = $query->where("customers.is_active", true);

    return $query;
  }

}
