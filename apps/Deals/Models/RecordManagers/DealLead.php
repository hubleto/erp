<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Leads\Models\RecordManagers\Lead;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealLead extends \HubletoMain\RecordManager
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
