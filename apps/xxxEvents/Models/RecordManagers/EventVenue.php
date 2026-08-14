<?php

namespace Hubleto\App\Community\Events\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventVenue extends \Hubleto\Erp\RecordManager
{
  public $table = 'events_has_venues';

  public function EVENT(): BelongsTo
  {
    return $this->belongsTo(Event::class, 'id_event', 'id');
  }

  public function VENUE(): BelongsTo
  {
    return $this->belongsTo(Venue::class, 'id_venue', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    if ($hubleto->router()->urlParamAsInteger("idEvent") > 0) {
      $query = $query->where($this->table . '.id_event', $hubleto->router()->urlParamAsInteger("idEvent"));
    }

    // Uncomment and modify these lines if you want to apply default filters to your model.
    // $filters = $hubleto->router()->urlParamAsArray("filters");
    // if (isset($filters["fArchive"]) && $filters["fArchive"] == 1) $query = $query->where("customers.is_active", false);
    // else $query = $query->where("customers.is_active", true);

    return $query;
  }

}
