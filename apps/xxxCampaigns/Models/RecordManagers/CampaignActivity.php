<?php

namespace Hubleto\App\Community\Campaigns\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignActivity extends \Hubleto\App\Community\Calendar\Models\RecordManagers\Activity
{
  public $table = 'campaign_activities';

  /** @return BelongsTo<Campaign, covariant CampaignActivity> */
  public function CAMPAIGN(): BelongsTo
  {
    return $this->belongsTo(Campaign::class, 'id_campaign', 'id');
  }

}
