<?php

namespace Hubleto\App\Community\EmailMarketing\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CampaignSchedule extends \Hubleto\Erp\RecordManager
{
  public $table = 'email_marketing_campaigns_schedule';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function EMAIL(): BelongsTo
  {
    return $this->belongsTo(Email::class, 'id_email', 'id');
  }

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function CAMPAIGN(): BelongsTo
  {
    return $this->belongsTo(Campaign::class, 'id_campaign', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $idCampaign = $hubleto->router()->urlParamAsInteger("idCampaign");
    $idEmail = $hubleto->router()->urlParamAsInteger("idEmail");

    if ($idCampaign > 0) $query = $query->where($this->table . '.id_campaign', $idCampaign);
    if ($idEmail > 0) $query = $query->where($this->table . '.id_email', $idEmail);

    return $query;
  }
}
