<?php

namespace Hubleto\App\Community\Leads\Models\RecordManagers;

use Hubleto\App\Community\Leads\Models\RecordManagers\Lead;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadHistory extends \Hubleto\Erp\RecordManager
{
  public $table = 'lead_histories';

  /** @return BelongsTo<Lead, covariant LeadHistory> */
  public function LEAD(): BelongsTo
  {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }

  /**
   * [Description for prepareReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * @param array|null|null $includeRelations
   * 
   * @return mixed
   * 
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idLead = $hubleto->router()->urlParamAsInteger("idLead");

    if ($idLead > 0) {
      $query = $query->where($this->table . '.id_lead', $idLead);
    }

    return $query;
  }

}
