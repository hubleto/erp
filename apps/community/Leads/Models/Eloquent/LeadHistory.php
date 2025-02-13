<?php

namespace HubletoApp\Community\Leads\Models\Eloquent;

use HubletoApp\Community\Customers\Models\Eloquent\Customer;
use HubletoApp\Community\Customers\Models\Eloquent\Person;
use HubletoApp\Community\Leads\Models\Eloquent\Lead;
use HubletoApp\Community\Settings\Models\Eloquent\ActivityType;
use HubletoApp\Community\Settings\Models\Eloquent\Currency;
use HubletoApp\Community\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadHistory extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'lead_histories';

  /** @return BelongsTo<Lead, covariant LeadHistory> */
  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }

}
