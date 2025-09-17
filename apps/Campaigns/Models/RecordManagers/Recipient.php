<?php

namespace Hubleto\App\Community\Campaigns\Models\RecordManagers;

use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;
use Hubleto\App\Community\Mail\Models\RecordManagers\Mail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipient extends \Hubleto\Erp\RecordManager
{
  public $table = 'campaigns_recipients';

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

  /** @return BelongsTo<Contact, covariant LeadTag> */
  public function MAIL(): BelongsTo
  {
    return $this->belongsTo(Mail::class, 'id_mail', 'id');
  }


  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    if ($main->router()->isUrlParam("idCampaign")) {
      $query = $query->where($this->table . '.id_campaign', $main->router()->urlParamAsInteger("idCampaign"));
    }

    return $query;
  }
}
