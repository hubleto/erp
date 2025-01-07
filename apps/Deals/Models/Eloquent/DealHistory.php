<?php

namespace HubletoApp\Deals\Models\Eloquent;

use HubletoApp\Customers\Models\Eloquent\Company;
use HubletoApp\Customers\Models\Eloquent\Person;
use HubletoApp\Deals\Models\Eloquent\Deal;
use HubletoApp\Settings\Models\Eloquent\ActivityType;
use HubletoApp\Settings\Models\Eloquent\Currency;
use HubletoApp\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealHistory extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'deal_histories';


  public function DEAL(): BelongsTo {
    return $this->belongsTo(Deal::class, 'id_lead', 'id');
  }
}
