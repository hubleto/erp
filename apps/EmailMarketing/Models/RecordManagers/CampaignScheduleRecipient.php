<?php

namespace Hubleto\App\Community\EmailMarketing\Models\RecordManagers;

use Hubleto\App\Community\Mail\Models\RecordManagers\Mail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CampaignScheduleRecipient extends \Hubleto\Erp\RecordManager
{
  public $table = 'email_marketing_campaigns_schedule_recipients';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function CAMPAIGN_SCHEDULE(): BelongsTo
  {
    return $this->belongsTo(CampaignSchedule::class, 'id_campaign_schedule', 'id');
  }

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function RECIPIENT(): BelongsTo
  {
    return $this->belongsTo(Recipient::class, 'id_recipient', 'id');
  }

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function MAIL(): BelongsTo
  {
    return $this->belongsTo(Mail::class, 'id_mail', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $idCampaignSchedule = $hubleto->router()->urlParamAsInteger("idCampaignSchedule");

    if ($idCampaignSchedule > 0) $query = $query->where($this->table . '.id_campaign_schedule', $idCampaignSchedule);

    return $query;
  }
}
