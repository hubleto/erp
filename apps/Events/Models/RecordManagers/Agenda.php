<?php

namespace HubletoApp\Community\Events\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Agenda extends \HubletoMain\RecordManager
{
  public $table = 'events_agendas';

  public function EVENT(): BelongsTo
  {
    return $this->belongsTo(Event::class, 'id_event', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \HubletoMain\Loader::getGlobalApp();

    if ($main->getRouter()->urlParamAsInteger("idEvent") > 0) {
      $query = $query->where($this->table . '.id_event', $main->getRouter()->urlParamAsInteger("idEvent"));
    }

    // Uncomment and modify these lines if you want to apply default filters to your model.
    // $defaultFilters = $main->getRouter()->urlParamAsArray("defaultFilters");
    // if (isset($defaultFilters["fArchive"]) && $defaultFilters["fArchive"] == 1) $query = $query->where("customers.is_active", false);
    // else $query = $query->where("customers.is_active", true);

    return $query;
  }

}
