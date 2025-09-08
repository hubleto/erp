<?php

namespace Hubleto\App\Community\Campaigns\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Click extends \Hubleto\Erp\RecordManager
{
  public $table = 'campaigns_clicks';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function CAMPAIGN(): BelongsTo
  {
    return $this->belongsTo(Campaign::class, 'id_campaign', 'id');
  }

}
