<?php

namespace Hubleto\App\Community\Campaigns\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Click extends \Hubleto\Erp\RecordManager
{
  public $table = 'campaigns_clicks';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function CAMPAIGN(): BelongsTo
  {
    return $this->belongsTo(Campaign::class, 'id_campaign', 'id');
  }

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function RECIPIENT(): BelongsTo
  {
    return $this->belongsTo(Recipient::class, 'id_recipient', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    if ($hubleto->router()->isUrlParam("idCampaign")) {
      $query = $query->where($this->table . '.id_campaign', $hubleto->router()->urlParamAsInteger("idCampaign"));
    }

    return $query;
  }
}
