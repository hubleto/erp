<?php

namespace HubletoApp\Community\Leads\Models\Eloquent;

// use HubletoApp\Community\Customers\Models\Eloquent\Activity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadActivity extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'lead_activities';

  // public function ACTIVITY(): BelongsTo {
  //   return $this->belongsTo(Activity::class, 'id_activity', 'id');
  // }
  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }
}
