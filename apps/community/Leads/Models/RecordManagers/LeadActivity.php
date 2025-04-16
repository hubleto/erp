<?php

namespace HubletoApp\Community\Leads\Models\RecordManagers;

// use HubletoApp\Community\Customers\Models\RecordManagers\Activity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadActivity extends \HubletoMain\Core\RecordManager
{
  public $table = 'lead_activities';

  /** @return BelongsTo<Lead, covariant LeadActivity> */
  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }
}
