<?php

namespace HubletoApp\Community\Leads\Models\RecordManagers;

use HubletoApp\Community\Customers\Models\RecordManagers\Customer;
use HubletoApp\Community\Contacts\Models\RecordManagers\Person;
use HubletoApp\Community\Leads\Models\RecordManagers\Lead;
use HubletoApp\Community\Settings\Models\RecordManagers\ActivityType;
use HubletoApp\Community\Settings\Models\RecordManagers\Currency;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadHistory extends \HubletoMain\Core\RecordManager
{
  public $table = 'lead_histories';

  /** @return BelongsTo<Lead, covariant LeadHistory> */
  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }

}
