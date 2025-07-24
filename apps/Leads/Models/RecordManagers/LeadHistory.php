<?php

namespace HubletoApp\Community\Leads\Models\RecordManagers;

use HubletoApp\Community\Leads\Models\RecordManagers\Lead;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadHistory extends \Hubleto\Framework\RecordManager
{
  public $table = 'lead_histories';

  /** @return BelongsTo<Lead, covariant LeadHistory> */
  public function LEAD(): BelongsTo
  {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }
}
