<?php

namespace HubletoApp\Community\Campaigns\Models\RecordManagers;

use HubletoApp\Community\Contacts\Models\RecordManagers\Contact;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignContact extends \HubletoMain\RecordManager
{
  public $table = 'campaigns_contacts';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function CAMPAIGN(): BelongsTo
  {
    return $this->belongsTo(Campaign::class, 'id_campaign', 'id');
  }

  /** @return BelongsTo<Contact, covariant LeadTag> */
  public function CONTACT(): BelongsTo
  {
    return $this->belongsTo(Contact::class, 'id_contact', 'id');
  }

}
