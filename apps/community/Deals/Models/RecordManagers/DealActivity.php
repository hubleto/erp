<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Contacts\Models\RecordManagers\Contact;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealActivity extends \HubletoApp\Community\Calendar\Models\RecordManagers\Activity
{
  public $table = 'deal_activities';

  /** @return BelongsTo<Deal, covariant DealActivity> */
  public function DEAL(): BelongsTo
  {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }

  /** @return BelongsTo<Lead, covariant LeadActivity> */
  public function CONTACT(): BelongsTo
  {
    return $this->belongsTo(Contact::class, 'id_lead', 'id');
  }

}
