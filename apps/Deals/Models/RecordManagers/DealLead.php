<?php

namespace Hubleto\App\Community\Deals\Models\RecordManagers;

use Hubleto\App\Community\Leads\Models\RecordManagers\Lead;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealLead extends \Hubleto\Erp\RecordManager
{
  public $table = 'deals_leads';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function DEAL(): BelongsTo
  {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }

  /** @return BelongsTo<Lead, covariant LeadTag> */
  public function LEAD(): BelongsTo
  {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }

}
