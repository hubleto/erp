<?php

namespace HubletoApp\Leads\Models\Eloquent;

use HubletoApp\Customers\Models\Eloquent\Company;
use HubletoApp\Customers\Models\Eloquent\Person;
use HubletoApp\Leads\Models\Eloquent\Lead;
use HubletoApp\Settings\Models\Eloquent\ActivityType;
use HubletoApp\Settings\Models\Eloquent\Currency;
use HubletoApp\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadHistory extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'lead_histories';


  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }
}
