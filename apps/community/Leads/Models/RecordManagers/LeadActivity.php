<?php

namespace HubletoApp\Community\Leads\Models\RecordManagers;

use HubletoApp\Community\Contacts\Models\RecordManagers\Contact;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadActivity extends \HubletoApp\Community\Calendar\Models\RecordManagers\Activity
{
  public $table = 'lead_activities';

  /** @return BelongsTo<Lead, covariant LeadActivity> */
  public function LEAD(): BelongsTo
  {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }

  /** @return BelongsTo<Contact, covariant LeadActivity> */
  public function CONTACT(): BelongsTo
  {
    return $this->belongsTo(Contact::class, 'id_lead', 'id');
  }

}
