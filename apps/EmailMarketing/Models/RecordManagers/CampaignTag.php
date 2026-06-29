<?php

namespace Hubleto\App\Community\EmailMarketing\Models\RecordManagers;

use Hubleto\App\Community\EmailMarketing\Models\RecordManagers\Tag;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignTag extends \Hubleto\Erp\RecordManager
{
  public $table = 'email_marketing_campaign_tags';

  /** @return BelongsTo<Tag, covariant CampaignTag> */
  public function TAG()
  {
    return $this->belongsTo(Tag::class, 'id_tag', 'id');
  }

  /** @return BelongsTo<Campaign, covariant CampaignTag> */
  public function CAMPAIGN(): BelongsTo
  {
    return $this->belongsTo(Campaign::class, 'id_campaign', 'id');
  }

}
